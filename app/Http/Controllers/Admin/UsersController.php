<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\SubscriptionPlan;
use App\Transactions;

use Carbon\Carbon;
use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use App\UserPromotionalCampaign;
use App\Services\UserPromotionalEmailService;
use Illuminate\Support\Facades\Mail;

class UsersController extends MainAdminController
{
	public function __construct()
    {
		 $this->middleware('auth');

		 parent::__construct();
         check_verify_purchase();

    }
    public function user_list(){

        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');

        }

        $page_title=trans('words.users');

        if(isset($_GET['s']))
        {
            $keyword = $_GET['s'];
            $user_list = User::where("usertype", "=", "User")
                ->where(function ($query) use ($keyword) {
                    $query->where("name", "LIKE", "%$keyword%")
                        ->orWhere("email", "LIKE", "%$keyword%");
                })
                ->orderBy('id','DESC')
                ->paginate(10);

            $user_list->appends(\Request::only('s'))->links();
        }
        else if(isset($_GET['plan_id']))
        {
            $plan_id = $_GET['plan_id'];
            $user_list = User::where("usertype", "=", "User")->where("plan_id", "=",$plan_id)->orderBy('id','DESC')->paginate(10);

            $user_list->appends(\Request::only('plan_id'))->links();
        }
        else
        {

            $user_list = User::where('usertype', '=', 'User')->orderBy('id','DESC')->paginate(10);
        }

        return view('admin.pages.users.list',compact('page_title','user_list'));
    }

    public function addUser()    {

        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');

        }

        $page_title=trans('words.add_user');

        $plan_list = SubscriptionPlan::active()->orderBy('id')->get();

        return view('admin.pages.users.addedit',compact('page_title','plan_list'));
    }

    public function addnew(Request $request)
    {

    	$data =  \Request::except(array('_token')) ;

	    $inputs = $request->all();

	    if(!empty($inputs['id']))
	    {
			$rule=array(
		        'name' => 'required',
		        'email' => ['required', 'email', 'max:255', User::uniqueEmailRule($inputs['id'])],
                'subscription_plan' => 'nullable|exists:subscription_plan,id',
                'user_image' => 'mimes:jpg,jpeg,gif,png'
		   		 );

		}
		else
		{
			$rule=array(
		        'name' => 'required',
		        'email' => ['required', 'email', 'max:255', User::uniqueEmailRule()],
		        'password' => 'required|min:8|max:15',
                'subscription_plan' => 'nullable|exists:subscription_plan,id',
                'user_image' => 'mimes:jpg,jpeg,gif,png'
		   		 );
		}



	   	 $validator = \Validator::make($data,$rule);

        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages());
        }

		if(!empty($inputs['id'])){

            $user = User::findOrFail($inputs['id']);

        }else{

            $user = new User;

        }

        $icon = $request->file('user_image');

        if($icon){
            //$tmpFilePath = 'upload/';
            $tmpFilePath = public_path('/upload/');

            $hardPath =  Str::slug($inputs['name'], '-',null).'-'.md5(time());

            $img = Image::make($icon);

            $img->fit(250, 250)->save($tmpFilePath.$hardPath.'-b.jpg');
            //$img->fit(80, 80)->save($tmpFilePath.$hardPath. '-s.jpg');

            $user->user_image = $hardPath.'-b.jpg';
        }

        $plan_id = !empty($inputs['subscription_plan']) ? (int) $inputs['subscription_plan'] : null;
        $plan_info = null;

        if ($plan_id) {
            $plan_info = SubscriptionPlan::active()->where('id', $plan_id)->first();

            if (!$plan_info) {
                return redirect()->back()->withErrors(['subscription_plan' => 'Selected plan is invalid or inactive.'])->withInput();
            }
        }

        $emailChanged = $user->exists && $user->email !== $inputs['email'];

		$user->name = $inputs['name'];
		$user->email = $inputs['email'];

        if($inputs['password'])
        {
            $user->password= bcrypt($inputs['password']);
        }
        $user->phone = $inputs['phone'];
        $user->user_address = $inputs['user_address'];

        if (!empty($inputs['exp_date'])) {
            $parsedDate = strtotime($inputs['exp_date']);
            $user->exp_date = ($parsedDate !== false && $parsedDate > 0) ? $parsedDate : 0;
        } elseif ($plan_info && !empty($plan_info->plan_days)) {
            $user->exp_date = strtotime('+' . (int) $plan_info->plan_days . ' days');
        } else {
            if (empty($user->exp_date)) {
                $user->exp_date = 0;
            }
        }

        $allowedUserTypes = ['Admin', 'Moderator', 'Sub_Admin', 'User'];
        if (!empty($inputs['usertype']) && in_array($inputs['usertype'], $allowedUserTypes, true)) {
            $user->usertype = $inputs['usertype'];
        } elseif (empty($inputs['id']) && empty($user->usertype)) {
            $user->usertype = 'User';
        }


        if ($plan_info) {
            $user->plan_id = $plan_info->id;
            $user->plan_amount = $plan_info->plan_price;
        }

        if (array_key_exists('email_verified', $inputs)) {
            if ($inputs['email_verified'] == '1') {
                if (empty($user->email_verified_at) || $emailChanged) {
                    $user->email_verified_at = Carbon::now();
                }
            } else {
                $user->email_verified_at = null;
            }
        }

        $user->status = $inputs['status'];
	    $user->save();

		if(!empty($inputs['id'])){

            \Session::flash('flash_message', trans('words.successfully_updated'));

            return \Redirect::back();
        }else{

            \Session::flash('flash_message', trans('words.added'));

            return \Redirect::back();

        }


    }

    public function editUser($id)
    {
    	  if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');

        }
    	  $page_title=trans('words.edit_user');

          $user = User::findOrFail($id);

          $plan_list = SubscriptionPlan::active()->orderBy('id')->get();

          return view('admin.pages.users.addedit',compact('page_title','user','plan_list'));

    }



    public function user_history($id)
    {
          if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');

        }
          $page_title=trans('words.user_history');

          $user = User::findOrFail($id);

          $user_id=$user->id;

          $transactions_list = Transactions::where('user_id',$user_id)->orderBy('id','DESC')->paginate(10);


          return view('admin.pages.users.history',compact('page_title','user','transactions_list'));

    }

    public function user_export()
    {
        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');

        }

          return Excel::download(new UsersExport, 'users.xlsx');

    }

    //Sub Admin

    public function admin_user_list()    {

        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');

        }

        $page_title=trans('words.admin_list');

        if(isset($_GET['s']))
        {
            $keyword = $_GET['s'];
            $user_list = User::where("usertype", "!=","User")->where('id', '!=', 1)->where("name", "LIKE","%$keyword%")->where("email", "LIKE","%$keyword%")->orderBy('id','DESC')->paginate(10);

            $user_list->appends(\Request::only('s'))->links();
        }
        else
        {

            $user_list = User::where('usertype', '!=', 'User')->where('usertype','!=','Sub_Admin')->where('id', '!=', 1)->orderBy('id')->paginate(10);
        }

        return view('admin.pages.users.admin_list',compact('page_title','user_list'));
    }

    public function admin_addUser()    {

        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');

        }

        $page_title=trans('words.add_admin');

        return view('admin.pages.users.addeditadmin',compact('page_title'));
    }

    public function admin_addnew(Request $request)
    {

        $data =  \Request::except(array('_token')) ;

        $inputs = $request->all();

        if(!empty($inputs['id']))
        {
            $rule=array(
                'name' => 'required',
                'email' => ['required', 'email', 'max:255', User::uniqueEmailRule($inputs['id'])],
                'user_image' => 'mimes:jpg,jpeg,gif,png'
                 );

        }
        else
        {
            $rule=array(
                'name' => 'required',
                'email' => ['required', 'email', 'max:255', User::uniqueEmailRule()],
                'password' => 'required|min:8|max:15',
                'user_image' => 'mimes:jpg,jpeg,gif,png'
                 );
        }



         $validator = \Validator::make($data,$rule);

        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages());
        }

        if(!empty($inputs['id'])){

            $user = User::findOrFail($inputs['id']);

        }else{

            $user = new User;

        }

        $icon = $request->file('user_image');

        if($icon){
            $tmpFilePath = public_path('/upload/');

            $hardPath =  Str::slug($inputs['name'], '-').'-'.md5(time());

            $img = Image::make($icon);

            $img->fit(250, 250)->save($tmpFilePath.$hardPath.'-b.jpg');
            //$img->fit(80, 80)->save($tmpFilePath.$hardPath. '-s.jpg');

            $user->user_image = $hardPath.'-b.jpg';
        }


        $allowedAdminTypes = ['Admin', 'Moderator', 'Sub_Admin'];
        $user->usertype = in_array($inputs['usertype'], $allowedAdminTypes, true) ? $inputs['usertype'] : 'Moderator';
        $user->name = $inputs['name'];
        $user->email = $inputs['email'];

        if($inputs['password'])
        {
            $user->password= bcrypt($inputs['password']);
        }
        $user->phone = $inputs['phone'];
        $user->status = $inputs['status'];
        $user->save();

        if(!empty($inputs['id'])){

            \Session::flash('flash_message', trans('words.successfully_updated'));

            return \Redirect::back();
        }else{

            \Session::flash('flash_message', trans('words.added'));

            return \Redirect::back();

        }


    }

    public function admin_editUser($id)
    {
          if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');

        }
          $page_title=trans('words.edit_admin');

          $user = User::findOrFail($id);

          return view('admin.pages.users.addeditadmin',compact('page_title','user'));

    }


    public function deleted_user_list(){

        if(Auth::User()->usertype!="Admin"){

            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');

        }

        $page_title=trans('words.users');

        if(isset($_GET['s']))
        {
            $keyword = $_GET['s'];
            $user_list = User::onlyTrashed()
                ->where("usertype", "=", "User")
                ->where(function ($query) use ($keyword) {
                    $query->where("name", "LIKE", "%$keyword%")
                        ->orWhere("email", "LIKE", "%$keyword%");
                })
                ->orderBy('id','DESC')
                ->paginate(10);

            $user_list->appends(\Request::only('s'))->links();
        }
        else
        {

            $user_list = User::onlyTrashed()->where('usertype', '=', 'User')->orderBy('id','DESC')->paginate(10);
        }

        return view('admin.pages.users.deleted_list',compact('page_title','user_list'));
    }



    public function promotionalEmailView(Request $request)
    {
        if (Auth::User()->usertype != "Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }

        $page_title = 'Send Promotional Email';

        $total_users = User::whereNotNull('email')->where('email', '!=', '')
            ->where(function ($q) {
                $q->where('usertype', 'User')
                  ->orWhereNull('usertype')
                  ->orWhere('usertype', '');
            })->count();

        $active_users = User::whereNotNull('email')->where('email', '!=', '')
            ->where('status', 1)
            ->where(function ($q) {
                $q->where('usertype', 'User')
                  ->orWhereNull('usertype')
                  ->orWhere('usertype', '');
            })->count();

        $campaigns = UserPromotionalCampaign::orderBy('id', 'desc')->paginate(10);

        // Pre-selected user(s) from request (e.g. ?user_id=123 or ?user_ids=1,2,3)
        $selectedUsers = collect();
        $userId = $request->input('user_id');
        $userIds = $request->input('user_ids');

        $idsToFetch = [];
        if (!empty($userId)) {
            $idsToFetch[] = (int) $userId;
        }
        if (!empty($userIds)) {
            $exploded = is_array($userIds) ? $userIds : explode(',', $userIds);
            foreach ($exploded as $eid) {
                $eid = (int) trim($eid);
                if ($eid > 0) {
                    $idsToFetch[] = $eid;
                }
            }
        }

        $idsToFetch = array_values(array_unique($idsToFetch));

        if (!empty($idsToFetch)) {
            $selectedUsers = User::whereIn('id', $idsToFetch)->get()->map(function ($u) {
                $planName = 'No Plan';
                if ($u->plan_id) {
                    $plan = \App\SubscriptionPlan::find($u->plan_id);
                    if ($plan) {
                        $planName = $plan->plan_name . ((float)$plan->plan_price > 0 ? ' ($' . $plan->plan_price . ')' : ' (Free)');
                    }
                }
                return [
                    'id' => $u->id,
                    'name' => $u->name ?: 'User #' . $u->id,
                    'email' => $u->email,
                    'phone' => $u->phone ?: '',
                    'status' => (int) $u->status,
                    'plan_name' => $planName,
                    'created_at' => $u->created_at ? $u->created_at->format('M d, Y') : 'N/A',
                    'avatar' => !empty($u->user_image) ? asset('upload/' . $u->user_image) : null,
                ];
            });
        }

        return view('admin.pages.users.promotional_email', compact('page_title', 'total_users', 'active_users', 'campaigns', 'selectedUsers'));
    }

    public function searchUsersForEmail(Request $request)
    {
        if (Auth::User()->usertype != "Admin") {
            return response()->json(['results' => []], 403);
        }

        $term = trim($request->input('q', ''));
        if (strlen($term) < 1) {
            return response()->json(['results' => []]);
        }

        $users = User::whereNotNull('email')->where('email', '!=', '')
            ->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('email', 'LIKE', "%{$term}%")
                  ->orWhere('phone', 'LIKE', "%{$term}%");
            })
            ->where(function ($q) {
                $q->where('usertype', 'User')
                  ->orWhereNull('usertype')
                  ->orWhere('usertype', '');
            })
            ->limit(20)
            ->get();

        $results = $users->map(function ($u) {
            $planName = 'No Plan';
            if ($u->plan_id) {
                $plan = \App\SubscriptionPlan::find($u->plan_id);
                if ($plan) {
                    $planName = $plan->plan_name . ((float)$plan->plan_price > 0 ? ' ($' . $plan->plan_price . ')' : ' (Free)');
                }
            }

            return [
                'id' => $u->id,
                'name' => $u->name ?: 'User #' . $u->id,
                'email' => $u->email,
                'phone' => $u->phone ?: '',
                'status' => (int) $u->status,
                'plan_name' => $planName,
                'created_at' => $u->created_at ? $u->created_at->format('M d, Y') : 'N/A',
                'avatar' => !empty($u->user_image) ? asset('upload/' . $u->user_image) : null,
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function sendPromotionalEmail(Request $request)
    {
        if (Auth::User()->usertype != "Admin") {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'audience' => 'required|string|in:all,active_only,specific_users',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer',
        ]);

        $subject = trim($request->input('subject'));
        $content = $request->input('content');
        $audience = $request->input('audience');
        $specificUserIds = (array) $request->input('user_ids', []);

        if ($audience === 'specific_users' && empty($specificUserIds)) {
            \Session::flash('flash_message', 'Please select or search at least one recipient user.');
            return redirect()->back()->withInput();
        }

        $campaign = (new UserPromotionalEmailService())->queueCampaign(
            $subject,
            $subject,
            $content,
            $audience,
            Auth::id(),
            $specificUserIds
        );

        if ($campaign->total_recipients === 0) {
            \Session::flash('flash_message', 'No valid user emails found for the selected recipient criteria.');
            return redirect('admin/users/promotional-email');
        }

        if ($audience === 'specific_users') {
            \Session::flash('flash_message', "Promotional email successfully dispatched/queued to {$campaign->total_recipients} selected user(s)!");
        } else {
            \Session::flash('flash_message', "Promotional campaign queued successfully! {$campaign->total_recipients} user email(s) have been added to the queue and will be delivered in rate-limited batches via the server cron.");
        }

        return redirect('admin/users/promotional-email');
    }

    public function testPromotionalEmail(Request $request)
    {
        if (Auth::User()->usertype != "Admin") {
            return response()->json(['status' => 'error', 'message' => trans('words.access_denied')], 403);
        }

        $request->validate([
            'test_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $testEmail = trim($request->input('test_email'));
        $subject = '[TEST] ' . trim($request->input('subject'));
        $bodyContent = $request->input('content');

        try {
            Mail::send('emails.newsletter', [
                'subject' => $subject,
                'name' => 'Admin Tester',
                'body_content' => $bodyContent,
                'unsubscribe_url' => url('/'),
            ], function ($message) use ($testEmail, $subject) {
                $message->to($testEmail)
                        ->from(getcong('site_email'), getcong('site_name'))
                        ->subject($subject);
            });

            return response()->json([
                'status' => 'success',
                'message' => "Test promotional email delivered to {$testEmail}!",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send test email: ' . $e->getMessage(),
            ], 500);
        }
    }
}

