<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Comments;
use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Artisan;

class CommentsController extends MainAdminController
{
	public function __construct()
    {
		 $this->middleware('auth');
         check_verify_purchase();
    }

    public function comments_list()
    {
        if(Auth::User()->usertype!="Admin"){
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }

        $page_title = 'Comments';
        $comments_list = Comments::orderBy('id','desc')->paginate(10);
        
        return view('admin.pages.comments.list', compact('page_title','comments_list'));
    }

    public function delete($id)
    {
    	if(Auth::User()->usertype!="Admin"){
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }
        
        $comment = Comments::findOrFail($id);
        $comment->delete();

        \Session::flash('flash_message', trans('words.deleted'));

        return redirect()->back();
    }
    
    public function approve($id)
    {
        if(Auth::User()->usertype!="Admin"){
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }
        
        $comment = Comments::findOrFail($id);
        $comment->status = 1;
        $comment->save();

        \Session::flash('flash_message', 'Comment Approved');

        return redirect()->back();
    }
    
    public function unapprove($id)
    {
        if(Auth::User()->usertype!="Admin"){
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }
        
        $comment = Comments::findOrFail($id);
        $comment->status = 0;
        $comment->save();

        \Session::flash('flash_message', 'Comment Unapproved');

        return redirect()->back();
    }
}
