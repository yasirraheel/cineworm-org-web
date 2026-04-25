<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\SubscriptionPlan; 

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image; 


class SubscriptionPlanController extends MainAdminController
{
	public function __construct()
    {
		 $this->middleware('auth');
		  
		parent::__construct();
        check_verify_purchase();	
		  
    }
    public function subscription_plan_list()    { 
        
        if(Auth::User()->usertype!="Admin")
        {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('dashboard');
            
         }

        $page_title=trans('words.subscription_plan');

         
        $plan_list = SubscriptionPlan::orderBy('id')->paginate(10);

        
        return view('admin.pages.plan.list',compact('page_title','plan_list'));
    }
    
    public function addSubscriptionPlan()    { 
        
        if(Auth::User()->usertype!="Admin")
        {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('dashboard');
            
        }

        $page_title=trans('words.add_plan');
        $plan_list = SubscriptionPlan::orderBy('id')->get();
 

        return view('admin.pages.plan.addedit',compact('page_title','plan_list'));
    }
    
    public function addnew(Request $request)
    { 
        $data =  \Request::except(array('_token')) ;
        $inputs = $request->all();

        if(!empty($inputs['id'])){

                $rule=array(
                'plan_name' => 'required',
                'plan_duration' => 'required|integer|min:1',
                'plan_duration_type' => 'required|in:1,30,365',
                'plan_price' => 'required',
                'plan_device_limit' => 'required|integer|min:1',
                'included_plan_ids' => 'nullable|array',
                'included_plan_ids.*' => 'exists:subscription_plan,id',
                'features' => 'nullable|array',
                'is_default_signup_plan' => 'nullable|in:0,1',
                'ads_on_off' => 'required|in:0,1',
                'status' => 'required|in:0,1'
                 );
        }else
        {
            $rule=array(
                'plan_name' => 'required',
                'plan_duration' => 'required|integer|min:1',
                'plan_duration_type' => 'required|in:1,30,365',
                'plan_price' => 'required',
                'plan_device_limit' => 'required|integer|min:1',
                'included_plan_ids' => 'nullable|array',
                'included_plan_ids.*' => 'exists:subscription_plan,id',
                'features' => 'nullable|array',
                'is_default_signup_plan' => 'nullable|in:0,1',
                'ads_on_off' => 'required|in:0,1',
                'status' => 'required|in:0,1'
                 );
        }

        
        
         $validator = \Validator::make($data,$rule);
 
        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages());
        } 

        $featureKeys = array_keys(SubscriptionPlan::AVAILABLE_FEATURES);
        $selectedFeatures = array_values(array_intersect($inputs['features'] ?? [], $featureKeys));
        $includedPlanIds = array_values(array_unique(array_filter(array_map('intval', $inputs['included_plan_ids'] ?? []))));
        $isDefaultSignupPlan = (int) ($inputs['is_default_signup_plan'] ?? 0) === 1;
        
        if(!empty($inputs['id'])){
           
            $plan_obj = SubscriptionPlan::findOrFail($inputs['id']);

        }else{

            $plan_obj = new SubscriptionPlan;

        }

        if ($plan_obj->wouldCreateInheritanceLoop($includedPlanIds)) {
            return redirect()->back()->withErrors(['included_plan_ids' => 'Selected included plans would create a loop.'])->withInput();
        }

        if ($isDefaultSignupPlan && (int) $inputs['status'] !== 1) {
            return redirect()->back()->withErrors(['is_default_signup_plan' => 'Default signup plan must be active.'])->withInput();
        }

        $inheritedFeatures = [];
        if (!empty($includedPlanIds)) {
            $includedPlans = SubscriptionPlan::whereIn('id', $includedPlanIds)->get();
            foreach ($includedPlans as $includedPlan) {
                $inheritedFeatures = array_merge($inheritedFeatures, $includedPlan->getEffectiveFeatureKeys());
            }
            $inheritedFeatures = array_values(array_unique($inheritedFeatures));
        }

         $plan_days_final=$inputs['plan_duration']*$inputs['plan_duration_type'];
         
         $plan_obj->plan_name = $inputs['plan_name'];
         $plan_obj->plan_duration = $inputs['plan_duration']; 
         $plan_obj->plan_duration_type = $inputs['plan_duration_type']; 
         $plan_obj->plan_days = $plan_days_final;           
         $plan_obj->plan_price = $inputs['plan_price'];
         
         $plan_obj->plan_device_limit = $inputs['plan_device_limit'];
         $plan_obj->included_plan_id = !empty($includedPlanIds) ? $includedPlanIds[0] : null;
         $plan_obj->included_plan_ids = $includedPlanIds;
         $planFeatures = array_values(array_diff($selectedFeatures, $inheritedFeatures));

         if ($isDefaultSignupPlan) {
            $planFeatures[] = SubscriptionPlan::DEFAULT_SIGNUP_FEATURE_FLAG;
         }

         $plan_obj->features = array_values(array_unique($planFeatures));
         $plan_obj->ads_on_off = (int) $inputs['ads_on_off'];

         $plan_obj->status = (int) $inputs['status']; 
         
         $plan_obj->save();

         if ($isDefaultSignupPlan) {
            SubscriptionPlan::where('id', '!=', $plan_obj->id)->get()->each(function ($otherPlan) {
                $otherFeatures = array_values(array_diff((array) $otherPlan->features, [SubscriptionPlan::DEFAULT_SIGNUP_FEATURE_FLAG]));

                if ($otherFeatures !== (array) $otherPlan->features) {
                    $otherPlan->features = $otherFeatures;
                    $otherPlan->save();
                }
            });
         } else if ($plan_obj->isDefaultSignupPlan()) {
            $plan_obj->features = array_values(array_diff((array) $plan_obj->features, [SubscriptionPlan::DEFAULT_SIGNUP_FEATURE_FLAG]));
            $plan_obj->save();
         }
         
        
        if(!empty($inputs['id'])){

            \Session::flash('flash_message', trans('words.successfully_updated'));

            return \Redirect::back();
        }else{

            \Session::flash('flash_message', trans('words.added'));

            return \Redirect::back();

        }            
        
         
    }     
   
    
    public function editSubscriptionPlan($plan_id)    
    {     
        if(Auth::User()->usertype!="Admin")
        {

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('dashboard');
            
         }  

          $page_title=trans('words.edit_plan');

          $plan_info = SubscriptionPlan::findOrFail($plan_id);
          $plan_list = SubscriptionPlan::where('id', '!=', $plan_id)->orderBy('id')->get();
 
          return view('admin.pages.plan.addedit',compact('page_title','plan_info','plan_list'));
        
    }	 
    
    public function delete($plan_id)
    {
    	if(Auth::User()->usertype=="Admin")
        {
        	
            $plan_obj = SubscriptionPlan::findOrFail($plan_id);
            $plan_obj->delete();

            \Session::flash('flash_message', trans('words.delete'));
            return redirect()->back();
        }
        else
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');            
        
        }
    }

     
     
    	
}
