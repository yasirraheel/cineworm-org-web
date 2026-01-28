<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Award;
use Illuminate\Http\Request;
use Auth;

class AwardsController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(Auth::User()->usertype!="Admin"){
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }

        $page_title = 'Awards List';
        $awards = Award::with(['user', 'movie'])->orderBy('id', 'desc')->paginate(15);

        return view('admin.pages.awards.list', compact('awards', 'page_title'));
    }
}
