<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
//use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function __construct(){
        $this->middleware('guest:admin'); //For Guest 'guest:{Name of Guard}'
    }

    public function showLoginForm(){
        return view('auth.admin-login');
    }

    public function login(Request $request){
        //validate form data
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = array(
            'email' => $request->email,
            'password' => $request->password
        );

        $remember = $request->remember;

        //attempt to log the user in 
        if(Auth::guard('admin')->attempt($credentials, $remember) /*declare the guard and attempt*/){
            //if successful, then redirect to intended location
            return redirect()->intended(route('admin')); 
            //intended redirects user back to where they where going after they successfully logged in or the url in the argument
        }else{
            //if unsuccessful, then redirect to log in form with form data
            return redirect()->back()->withInput($request->only('email', 'remember'));
        }
    }
}
