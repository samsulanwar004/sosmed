<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{

    public function postSignUp(Request $request)
    {
    	$this->validate($request, [
    		'email' => 'required|email|unique:users',
    		'first_name' => 'required|max:120',
    		'password' => 'required|min:6'
    	]);
    	$email = $request['email'];
    	$first_name = $request['first_name'];
    	$password = bcrypt($request['password']);

    	$user = new User();
    	$user->email = $email;
    	$user->first_name = $first_name;
    	$user->password = $password;

    	Auth::login($user);
    	$user->save();
    	return redirect()->route('dashboard');
    }

    public function postSignIn(Request $request)
    {
    	$this->validate($request, [
    		'email' => 'required|email',
    		'password' => 'required|min:6'
    	]);
    	if (Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
    		return redirect()->route('dashboard')->with('message','Hi '.$request['email']);
    	}
    	return redirect()->back();
    }

    public function getLogout()
    {
        Auth::logout();
        return redirect()->route('home');
    }

    public function getAccount()
    {
        return view('account', ['user' => Auth::user()]);
    }

    public function postSaveAccount(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required'
        ]);

        $user = Auth::user();
        $user->first_name = $request['first_name'];
        $user->update();

        $file = $request->file('image');
        $filename = $request['first_name'].'-'.$user->id.'.jpg';
        if ($file) {
            Storage::disk('local')->put($filename, File::get($file));
        }

        return redirect()->route('account');
    }

    public function getUserImage($filename)
    {
        $file = Storage::disk('local')->get($filename);

        return new Response($file, 200);
    }

}
