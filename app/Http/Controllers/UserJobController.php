<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\JobListing;
use Illuminate\Http\Request;

class UserJobController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Check if user has job listing feature
        $user = Auth::user();
        if ($user->plan_id) {
            $plan = \App\SubscriptionPlan::find($user->plan_id);
            if (!$plan || !in_array('job_listing', $plan->getEffectiveFeatureKeys())) {
                \Session::flash('flash_message', trans('words.access_denied'));
                return redirect('dashboard');
            }
        } else {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $jobs = JobListing::where('user_id', $user->id)->orderBy('id', 'DESC')->paginate(10);
        return view('pages.user.jobs.index', compact('jobs'));
    }

    public function create()
    {
        return view('pages.user.jobs.create');
    }

    public function store(Request $request)
    {
        $data = \Request::except(['_token']);

        $rule = array(
            'title' => 'required',
            'company' => 'required',
            'location' => 'required',
        );

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }

        $job = new JobListing();
        $job->user_id = Auth::user()->id;
        $job->title = $request->title;
        $job->description = $request->description;
        $job->company = $request->company;
        $job->location = $request->location;
        $job->salary = $request->salary;
        $job->contact_details = $request->contact_details;
        $job->status = 0; // Default to pending
        $job->save();

        \Session::flash('flash_message', 'Job Listing Created and Pending Approval.');
        return redirect('user/jobs');
    }

    public function edit($id)
    {
        $job = JobListing::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();
        return view('pages.user.jobs.edit', compact('job'));
    }

    public function update(Request $request, $id)
    {
        $job = JobListing::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();

        $data = \Request::except(['_token']);

        $rule = array(
            'title' => 'required',
            'company' => 'required',
            'location' => 'required',
        );

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }

        $job->title = $request->title;
        $job->description = $request->description;
        $job->company = $request->company;
        $job->location = $request->location;
        $job->salary = $request->salary;
        $job->contact_details = $request->contact_details;
        // If they edit, should it go back to pending? Usually yes.
        $job->status = 0; 
        $job->save();

        \Session::flash('flash_message', 'Job Listing Updated and Pending Approval.');
        return redirect('user/jobs');
    }

    public function destroy($id)
    {
        $job = JobListing::where('id', $id)->where('user_id', Auth::user()->id)->firstOrFail();
        $job->delete();

        \Session::flash('flash_message', 'Job Listing Deleted.');
        return redirect('user/jobs');
    }
}
