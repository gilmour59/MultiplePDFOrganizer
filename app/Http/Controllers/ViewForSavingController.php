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

class ViewForSavingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web,admin'); //$this->middleware('auth');
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
            return redirect('/')->with('isAdd', $isAdd)->with('errors', $errors)->withInput();
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
            $request->session()->put('passData', $passData);
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

    public function deleteViewFile(Request $request){

        $passData = $request->session()->get('passData');
        $id = $request->get('delete');
        unset($passData[$id]);
        //reindex the array
        //$passData = array_values($passData); 
        $request->session()->put('passData', $passData);
        //dd($passData);
        return view('view_files')->with('passData', $passData);
    }

}