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
 

        return view('admin.pages.plan.addedit',compact('page_title'));
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
                'ads_on_off' => 'required|in:0,1',
                'status' => 'required|in:0,1'
                 );
        }

        
        
         $validator = \Validator::make($data,$rule);
 
        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages());
        } 
        
        if(!empty($inputs['id'])){
           
            $plan_obj = SubscriptionPlan::findOrFail($inputs['id']);

        }else{

            $plan_obj = new SubscriptionPlan;

        }

         $plan_days_final=$inputs['plan_duration']*$inputs['plan_duration_type'];
         
         $plan_obj->plan_name = $inputs['plan_name'];
         $plan_obj->plan_duration = $inputs['plan_duration']; 
         $plan_obj->plan_duration_type = $inputs['plan_duration_type']; 
         $plan_obj->plan_days = $plan_days_final;           
         $plan_obj->plan_price = $inputs['plan_price'];
         
         $plan_obj->plan_device_limit = $inputs['plan_device_limit'];
         $plan_obj->ads_on_off = (int) $inputs['ads_on_off'];

         $plan_obj->status = (int) $inputs['status']; 
         
         $plan_obj->save();
         
        
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
 
          return view('admin.pages.plan.addedit',compact('page_title','plan_info'));
        
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
