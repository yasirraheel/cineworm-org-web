<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\PlanFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanFeaturesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (auth()->user()->usertype !== 'Admin' && auth()->user()->usertype !== 'Sub_Admin') {
            return redirect('admin/dashboard')->with('error_flash_message', 'Access Denied');
        }

        $features = PlanFeature::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.pages.plan_features.list', compact('features'));
    }

    public function save(Request $request)
    {
        if (auth()->user()->usertype !== 'Admin' && auth()->user()->usertype !== 'Sub_Admin') {
            return redirect('admin/dashboard')->with('error_flash_message', 'Access Denied');
        }

        $request->validate([
            'feature_name' => 'required|string|max:255',
            'url'          => 'nullable|string|max:255',
            'icon'         => 'nullable|string|max:100',
        ]);

        $id = $request->input('id');

        if ($id) {
            $feature = PlanFeature::findOrFail($id);
        } else {
            $feature = new PlanFeature();
            $feature_key = Str::slug($request->input('feature_name'), '_');
            
            // Ensure unique key
            $original_key = $feature_key;
            $counter = 1;
            while (PlanFeature::where('feature_key', $feature_key)->exists()) {
                $feature_key = $original_key . '_' . $counter;
                $counter++;
            }

            $feature->feature_key = $feature_key;
        }

        $feature->feature_name = $request->input('feature_name');
        $feature->url = $request->input('url');
        $feature->icon = $request->input('icon') ?: 'fa fa-check-circle';
        $feature->status = $request->has('status') ? (int) $request->input('status') : 1;
        $feature->save();

        return redirect()->back()->with('flash_message', 'Plan Feature Saved Successfully');
    }

    public function delete($id)
    {
        if (auth()->user()->usertype !== 'Admin') {
            return redirect('admin/dashboard')->with('error_flash_message', 'Access Denied');
        }

        $feature = PlanFeature::findOrFail($id);
        $feature->delete();

        return redirect()->back()->with('flash_message', 'Plan Feature Deleted Successfully');
    }
}
