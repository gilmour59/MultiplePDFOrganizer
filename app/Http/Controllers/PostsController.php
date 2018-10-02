<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Validator;
use Illuminate\Http\Request;
use App\ArchiveFile;
use App\Division;
use App\Category;
use Smalot\PdfParser\Parser;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web,admin'); //$this->middleware('auth');
    }

    public function index(Request $request)
    {
        $request->session()->put('division', $request
                ->has('division') ? $request->get('division') : ($request->session()
                ->has('division') ? $request->session()->get('division') : 0));

        $request->session()->put('search', $request
                ->has('search') ? $request->get('search') : ($request->session()
                ->has('search') ? $request->session()->get('search') : ''));

        $request->session()->put('field', $request
                ->has('field') ? $request->get('field') : ($request->session()
                ->has('field') ? $request->session()->get('field') : 'date'));

        $request->session()->put('sort', $request
                ->has('sort') ? $request->get('sort') : ($request->session()
                ->has('sort') ? $request->session()->get('sort') : 'asc'));

        $archiveFileIds = new ArchiveFile();
        $archiveFiles = new ArchiveFile();

        $archiveFileIds = $archiveFileIds->search($request->session()->get('search'))->raw();
        $searchIds = $archiveFileIds['ids'];

        if(empty($request->session()->get('search'))){
            $isShowAll = true;
        }else{
            $isShowAll = false;
        }
        
        //dd($isShowAll);
        
        if($request->session()->get('division') == 0){
            $archiveFiles = $archiveFiles
                ->join('divisions', 'archive_files.division_id', '=', 'divisions.id')
                ->when($isShowAll == false, function ($query) use ($searchIds){
                    $query->whereIn('archive_files.id', $searchIds);
                })
                ->select('archive_files.*', 'divisions.div_name')
                ->orderBy($request->session()->get('field'), $request->session()->get('sort'))
                ->paginate(10);
        }else{
                $archiveFiles = $archiveFiles
                    ->join('divisions', 'archive_files.division_id', '=', 'divisions.id')
                    ->where('archive_files.division_id', '=', $request->session()->get('division'))
                    ->when($isShowAll == false, function ($query) use ($searchIds){
                        $query->whereIn('archive_files.id', $searchIds);
                    })
                    ->select('archive_files.*', 'divisions.div_name')
                    ->orderBy($request->session()->get('field'), $request->session()->get('sort'))
                    ->paginate(10);
            }

            $division = $request->session()->get('division');

            if($request->ajax()){
                return view('index')->with('archiveFiles', $archiveFiles)->with('division', $division);
            }
            return view('ajax')->with('archiveFiles', $archiveFiles)->with('division', $division);
    }

    public function viewFiles(Request $request){
        
        $validator = Validator::make($request->all(), [
            'addFileUpload' => 'required',
            'addFileUpload.*' => 'file|required|mimes:pdf',
            'addDate' => 'required',
        ]); 

        if ($validator->fails())
        {
            $isAdd = true;
            $errors = $validator->errors();
            return redirect('/')->with('isAdd', $isAdd)->with('errors', $errors);
        }

        $date = $request->input('addDate');

        if($request->hasFile('addFileUpload')){
            
            $cleaner = new Filesystem();
            $cleaner->cleanDirectory(storage_path('app/public/temp'));

            foreach($request->file('addFileUpload') as $file)
            {
                $fileNameToStore = $file->getClientOriginalName();
                $path = $file->storeAs('public/temp', $fileNameToStore); 

                $parser = new Parser();
                if($pdf = $parser->parseFile(storage_path('/app/') . $path)){
                    //IF FAIL - 'content cannot be parsed'
                    $text = $pdf->getText();

                    $key_div = $this->checkKeywords($text);
                }else{
                    alert('Parsing Error');
                    return view('index');
                }
                $data[] = array('file_name' => $fileNameToStore, 'date' => $date, 'content' => $text, 'key_div' => $key_div);  
            }
            $passData = $data;
        }
        return view('view_files')->with('passData', $passData);
    }

    public function checkKeywords($text){

        $divisions = Division::select('div_name')->get();

        $Keywords = array();
        
        foreach($divisions->toArray() as $key => $value){
            $Keywords[$key + 1] = $value['div_name'];
        }
        //dd($Keywords);
        $textWithKeyword = array();

        //key is integer for the division
        foreach($Keywords as $key => $value){
            $posKeyword = stripos($text, $value);
            if($posKeyword !== false){
                $textWithKeyword[$key] = $posKeyword;
            }else{
                unset($textWithKeyword[$key]);
            }
        }    

        //dd($textWithKeyword);

        if(empty($textWithKeyword)){
            return 0;
        }else{
            //This returns the key of the division containing the keyword
            $wordDivision = array_search(min($textWithKeyword), $textWithKeyword);

            //Returns the key of the division
            //dd($wordDivision);
            return $wordDivision;
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'addFileUpload' => 'file|required|mimes:pdf',
            'addFileUpload.*' => 'file|required|mimes:pdf',
            'addDate' => 'required',
        ]);

        if ($validator->fails())
            return response()->json([
            'fail' =>true,
            'errors' => $validator->errors()
            ]);

        //Create new Data 
        $archiveFiles = new ArchiveFile();
        $archiveFiles->division_id = $request->input('addDivision');
        $archiveFiles->date = $request->input('addDate');
 
        //Handle File Upload
        if ($request->hasFile('addFileUpload')) {

            //get File Name
            //$fileNameWithExtension = $request->file('addFileUpload')->getClientOriginalName();
            $extension = $request->file('addFileUpload')->getClientOriginalExtension();
            $fileNameToStore = time() . '' . $request->input('addFileName') . '.'. $extension;

            //Find Category name
            $category = Category::find($archiveFiles->category_id);
            $division = Division::find($category->division_id);

            //Storage::makeDirectory($directory);
            //Move file to it's category name

            //THIS OVERWRITES FILES WITH SAME NAME
            $path = $request->file('addFileUpload')->storeAs('public/' . $division->div_name . '/' . $category->name, $fileNameToStore);

            //Parse pdf
            $parser = new Parser();
            if($pdf = $parser->parseFile(storage_path('/app/') . $path)){
                //IF FAIL - 'content cannot be parsed'
                $text = $pdf->getText();
                $archiveFiles->content = $text; 
            }else{
                return response()->json([
                    'fail' => true,
                    'errorParse' => 'File Parse Error!'
                ]);
            }
        }else{
            $fileNameToStore = null;
        }
        $archiveFiles->file_name = $request->input('addFileName');
        $archiveFiles->file = $fileNameToStore;
        $archiveFiles->save();
        
        return response()->json([
            'fail' => false,
            'redirect_url' => route('index')
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'editFileUpload' => 'file|mimes:pdf',
            'editDivision' => 'required',
            'editCategory' => 'required',
            'editDate' => 'required',
            'editFileName' => 'required'
        ]);

        if ($validator->fails())
            return response()->json([
                'fail' =>true,
                'errors' => $validator->errors()
            ]);

        //Find Data 
        $archiveFiles = ArchiveFile::find($id);

        $archiveFiles->date = $request->input('editDate');

        if($archiveFiles->file_name != $request->input('editFileName')){

            $category = Category::find($archiveFiles->category_id);
            $division = Division::find($category->division_id);

            $extension = explode(".", $archiveFiles->file);
            $extension = end($extension);
            $newFileName = time() . '' . $request->input('editFileName') . '.' . $extension;

            Storage::move('public/' . $division->div_name . '/' . $category->name . '/' . $archiveFiles->file, 'public/' . $division->div_name . '/' . $category->name . '/' . $newFileName);

            $archiveFiles->file_name = $request->input('editFileName');
            $archiveFiles->file = $newFileName;
        }

        if($archiveFiles->category_id != $request->input('editCategory')){

            $categoryOld = Category::find($archiveFiles->category_id);
            $divisionOld = Division::find($categoryOld->division_id);
    
            $categoryNew = Category::find($request->input('editCategory'));
            $divisionNew = Division::find($categoryNew->division_id);

            Storage::move('public/' . $divisionOld->div_name . '/' . $categoryOld->name . '/' . $archiveFiles->file, 'public/' . $divisionNew->div_name . '/' . $categoryNew->name . '/' . $archiveFiles->file);

            $archiveFiles->category_id = $request->input('editCategory');
        }

        //Handle File Upload
        if ($request->hasFile('editFileUpload')) {

            //get File Name
            //$fileNameWithExtension = $request->file('editFileUpload')->getClientOriginalName();
            $extension = $request->file('editFileUpload')->getClientOriginalExtension();
            $fileNameToStore = time() . '' . $request->input('editFileName') . '.' . $extension;

            //Find Category name
            $category = Category::find($archiveFiles->category_id);
            $division = Division::find($category->division_id);

            //Delete and Replace
            Storage::delete('public/' . $division->div_name . '/' . $category->name . '/' . $archiveFiles->file);
            $path = $request->file('editFileUpload')->storeAs('public/' . $division->div_name . '/' . $category->name, $fileNameToStore);

            //Parse pdf
            $parser = new Parser();
            if($pdf = $parser->parseFile(storage_path('/app/') . $path)){
                //IF FAIL - 'content cannot be parsed'
                $text = $pdf->getText();
                $archiveFiles->content = $text; 
            }else{
                return response()->json([
                    'fail' => true,
                    'errorParse' => 'File Parse Error!'
                ]);
            }
            $archiveFiles->file_name = $request->input('editFileName');
            $archiveFiles->file = $fileNameToStore;
        }
        $archiveFiles->save();

        return response()->json([
            'fail' => false,
            'redirect_url' => route('index')
        ]);
    }

    public function division()
    {
        $divisions = Division::get();

        return response()->json([
            'divisions' => $divisions,
        ]);
    }

    public function edit($id)
    {
        $archiveFiles = ArchiveFile::find($id);
        $category = Category::find($archiveFiles->category_id);
        $division = Division::find($category->division_id);

        return response()->json([
            'file' => $archiveFiles,
            'category' => $category->id,
            'division' => $division->id
        ]);
    }

    public function destroy($id)
    {
        $archiveFiles = ArchiveFile::find($id);
        $category = Category::find($archiveFiles->category_id);
        $division = Division::find($category->division_id);

        Storage::delete('public/' . $division->div_name . '/' . $category->name . '/' . $archiveFiles->file);
        $archiveFiles->delete();
    }
    
    public function view($id)
    {
        $archiveFiles = ArchiveFile::find($id);
        $category = Category::find($archiveFiles->category_id);
        $division = Division::find($category->division_id);

        return response()->file(storage_path('app/public/') . $division->div_name . '/' . $category->name . '/' . $archiveFiles->file);
    }

    public function download($id)
    {
        $archiveFiles = ArchiveFile::find($id);
        $category = Category::find($archiveFiles->category_id);
        $division = Division::find($category->division_id);

        return response()->download(storage_path('app/public/') . $division->div_name . '/' . $category->name . '/' . $archiveFiles->file);
    }
}
