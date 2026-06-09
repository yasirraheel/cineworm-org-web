<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\JobListing;
use Illuminate\Http\Request;

class JobListingController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $jobs = JobListing::with('user')->orderBy('id', 'DESC')->paginate(10);
        $page_title = 'Job Listings';
        return view('admin.pages.jobs.index', compact('jobs', 'page_title'));
    }

    public function show($id)
    {
        $job = JobListing::with('user')->findOrFail($id);
        $page_title = 'Job Details';
        return view('admin.pages.jobs.show', compact('job', 'page_title'));
    }

    public function approve($id)
    {
        $job = JobListing::findOrFail($id);
        $job->status = 1;
        $job->save();

        \Session::flash('flash_message', 'Job Listing Approved');
        return redirect()->back();
    }

    public function unapprove($id)
    {
        $job = JobListing::findOrFail($id);
        $job->status = 0;
        $job->save();

        \Session::flash('flash_message', 'Job Listing Unapproved');
        return redirect()->back();
    }

    public function delete($id)
    {
        $job = JobListing::findOrFail($id);
        $job->delete();

        \Session::flash('flash_message', 'Job Listing Deleted');
        return redirect()->back();
    }
}
