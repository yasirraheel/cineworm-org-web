<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Models\NewsTicker;
use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;

class NewsTickerController extends MainAdminController
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

        $page_title = 'News Ticker';
        $list = NewsTicker::orderBy('id', 'desc')->paginate(10);
        $edit = null;

        return view('admin.pages.news_ticker.index', compact('page_title', 'list', 'edit'));
    }

    public function edit($id)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = 'Edit News Ticker';
        $list = NewsTicker::orderBy('id', 'desc')->paginate(10);
        $edit = NewsTicker::findOrFail($id);

        return view('admin.pages.news_ticker.index', compact('page_title', 'list', 'edit'));
    }

    public function save(Request $request)
    {
        $data =  \Request::except(array('_token')) ;

        $rule = array(
            'headline' => 'required',
        );

        $validator = \Validator::make($data, $rule);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $inputs = $request->all();

        if(!empty($inputs['id'])){
            $obj = NewsTicker::findOrFail($inputs['id']);
        }else{
            $obj = new NewsTicker;
        }

        $obj->headline = $inputs['headline'];
        $obj->details = isset($inputs['details']) ? $inputs['details'] : null;
        $obj->is_breaking = isset($inputs['is_breaking']) ? 1 : 0;
        $obj->status = isset($inputs['status']) ? 1 : 0;

        $obj->save();

        if(!empty($inputs['id'])){
            \Session::flash('flash_message', trans('words.successfully_updated'));
        }else{
            \Session::flash('flash_message', trans('words.successfully_added'));
        }

        return redirect('admin/news_ticker');
    }

    public function delete($id)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $obj = NewsTicker::findOrFail($id);
        $obj->delete();

        \Session::flash('flash_message', trans('words.successfully_deleted'));

        return redirect()->back();
    }
}
