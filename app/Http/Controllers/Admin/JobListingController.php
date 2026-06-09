<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobListingController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function create()
    {
        $page_title = 'Add Job Listing';
        return view('admin.pages.jobs.add', compact('page_title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'company' => 'required',
            'location' => 'required',
            'description' => 'required'
        ]);

        $job = new JobListing;
        $job->user_id = Auth::user()->id; // Assigned to the admin creating it
        $job->title = $request->title;
        $job->company = $request->company;
        $job->location = $request->location;
        $job->salary = $request->salary;
        $job->contact_details = $request->contact_details;
        $job->description = $request->description;
        $job->status = $request->status ?? 1; // Default to approved since admin creates it
        $job->save();

        \Session::flash('flash_message', 'Job Listing Added');
        return redirect('admin/job_listings');
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
