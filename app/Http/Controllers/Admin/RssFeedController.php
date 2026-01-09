<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Models\RssFeed;
use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;

class RssFeedController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
        check_verify_purchase();
    }

    public function index()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = 'RSS Feeds';
        $list = RssFeed::orderBy('id', 'desc')->paginate(10);
        $edit = null;

        return view('admin.pages.rss_feeds.index', compact('page_title', 'list', 'edit'));
    }

    public function edit($id)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = 'Edit RSS Feed';
        $list = RssFeed::orderBy('id', 'desc')->paginate(10);
        $edit = RssFeed::findOrFail($id);

        return view('admin.pages.rss_feeds.index', compact('page_title', 'list', 'edit'));
    }

    public function save(Request $request)
    {
        $data =  \Request::except(array('_token')) ;

        $rule = array(
            'name' => 'required',
            'url' => 'required|url',
        );

        $validator = \Validator::make($data, $rule);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $inputs = $request->all();

        if(!empty($inputs['id'])){
            $obj = RssFeed::findOrFail($inputs['id']);
        }else{
            $obj = new RssFeed;
        }

        $obj->name = $inputs['name'];
        $obj->url = $inputs['url'];
        $obj->description = isset($inputs['description']) ? $inputs['description'] : null;
        $obj->status = isset($inputs['status']) ? 1 : 0;

        $obj->save();

        if(!empty($inputs['id'])){
            \Session::flash('flash_message', trans('words.successfully_updated'));
        }else{
            \Session::flash('flash_message', trans('words.successfully_added'));
        }

        return redirect('admin/rss_feeds');
    }

    public function delete($id)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $obj = RssFeed::findOrFail($id);
        $obj->delete();

        \Session::flash('flash_message', trans('words.successfully_deleted'));

        return redirect()->back();
    }
}
