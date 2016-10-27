<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

	public function getDashboard()
	{
		$posts = Post::orderBy('created_at', 'desc')->get();
		return view('dashboard', ['posts' => $posts]);
	}

    public function postCreatePost(Request $request)
    {
    	// Validation
    	$this->validate($request, [
    		'body' => 'required'
    	]);
	    $post = new Post();
	    $post->body = $request['body'];
	    if ($request->user()->posts()->save($post))
	    {
	    	$message = 'Berhasil ngepost';
	    }
	    else
	    {
	    	$message = 'Gagal ngepost';
	    }

	    return redirect()->route('dashboard')->with(['message' => $message]);
    }

    public function getDeletePost($post_id)
    {
    	$post = Post::where('id', $post_id)->first();
    	if (Auth::user() != $post->user) {
    		return redirect()->back();
    	}
    	$post->delete();

    	return redirect()->route('dashboard')->with(['message' => 'Sukses hapus']);
    }

    public function postEditPost(Request $request)
    {
    	$this->validate($request, [
    		'body' => 'required'
    	]);
    	$post = Post::find($request['postId']);
    	$post->body = $request['body'];
    	$post->update();

    	//return response()->json(['message' => 'Post edited!'], 200); testing respon
    	return response()->json(['new_body' => $post->body], 200);
    }
}
