<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Transactions;
use App\SubscriptionPlan;
use App\Coupons;

use App\Http\Requests;
use Illuminate\Http\Request;
use Validator;
use URL;
use Session;
use Redirect;
use Input;
use DB;
 
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    //private $config;

    public function __construct()
    {
        //parent::__construct();
        
        $client_id=getPaymentGatewayInfo(1,'paypal_client_id');
        $secret=getPaymentGatewayInfo(1,'paypal_secret');
        $mode=getPaymentGatewayInfo(1,'mode'); 

         $this->config = [
                    'mode'    => $mode,
                    'sandbox' => [
                        'client_id'         => $client_id,
                        'client_secret'     => $secret,
                        'app_id'            => '',
                     ],
                    'live' => [
                        'client_id'         => $client_id,
                        'client_secret'     => $secret,
                        'app_id'            => '',
                    ],

                    'payment_action' => 'Sale',
                    'currency'       => 'USD',
                    'notify_url'     => '',
                    'locale'         => 'en_US',
                    'validate_ssl'   => true,
                ];
    }

    protected function hasPaypalCredentials()
    {
        $mode = $this->config['mode'] === 'live' ? 'live' : 'sandbox';

        return !empty($this->config[$mode]['client_id']) && !empty($this->config[$mode]['client_secret']);
    }

    protected function paypalGatewayMessage()
    {
        return 'PayPal is not configured right now. Please choose another payment method or contact support.';
    }

    protected function redirectToPaymentScreen($planId = null)
    {
        $planId = $planId ?: Session::get('plan_id');

        if ($planId) {
            return redirect('payment_method/' . $planId);
        }

        return redirect('membership_plan');
    }

    protected function redirectWithGatewayError($message, $planId = null)
    {
        Session::flash('error_flash_message', $message);

        return $this->redirectToPaymentScreen($planId);
    }
 

     /**
     * process transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function paypal_pay(Request $request)
    {

        $currency_code=getcong('currency_code')?getcong('currency_code'):'USD';

        $plan_id=$request->get('plan_id');
        $plan_name=$request->get('plan_name');
        $plan_amount=$request->get('amount');
  
        $success_url=\URL::to('paypal/success/');
        $fail_url=\URL::to('paypal/fail/');   

        if (!$this->hasPaypalCredentials()) {
            return $this->redirectWithGatewayError($this->paypalGatewayMessage(), $plan_id);
        }

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials($this->config);
            $provider->getAccessToken();

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => $success_url,
                    "cancel_url" => $fail_url,
                ],
                "purchase_units" => [
                    0 => [
                        "amount" => [
                            "currency_code" => $currency_code,
                            "value" => $plan_amount
                        ],
                        "description" => $plan_name,
                    ]
                ]
            ]);
        } catch (\Throwable $e) {
            \Log::warning('PayPal payment initiation failed: ' . $e->getMessage());

            return $this->redirectWithGatewayError($this->paypalGatewayMessage(), $plan_id);
        }

         
        if (isset($response['id']) && $response['id'] != null) {

            // redirect to approve href
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }

            \Log::warning('PayPal approval link missing for order: ' . $response['id']);

            return $this->redirectWithGatewayError('Unable to start the PayPal payment right now. Please try again.', $plan_id);
 

        } else {

            \Log::warning('PayPal order creation failed.', ['response' => $response]);

            return $this->redirectWithGatewayError($response['message'] ?? 'Unable to start the PayPal payment right now. Please try again.', $plan_id);
 
        }
    }

    /**
     * success transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function paypal_success(Request $request)
    {
        $plan_id = Session::get('plan_id');

        if (!$this->hasPaypalCredentials()) {
            return $this->redirectWithGatewayError($this->paypalGatewayMessage(), $plan_id);
        }

        if (!$request->filled('token')) {
            return $this->redirectWithGatewayError(trans('words.payment_failed'), $plan_id);
        }

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials($this->config);
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request['token']);
        } catch (\Throwable $e) {
            \Log::warning('PayPal payment capture failed: ' . $e->getMessage());

            return $this->redirectWithGatewayError('We could not verify the PayPal payment right now. Please try again.', $plan_id);
        }
 

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            
            $payment_id= $response['purchase_units'][0]['payments']['captures'][0]['id'];

            $user_id=Auth::user()->id;
            $user_email=Auth::user()->email;           
            $user = User::findOrFail($user_id);

            $plan_info = SubscriptionPlan::active()->where('id',$plan_id)->first();

            if (!$plan_info) {
                return $this->redirectWithGatewayError('Selected plan is no longer available.', $plan_id);
            }

            $plan_days=$plan_info->plan_days;
 
            if(Session::get('coupon_percentage'))
            {   
                //If coupon used
                $discount_price_less =  $plan_info->plan_price * Session::get('coupon_percentage') / 100;

                $plan_amount=$plan_info->plan_price - $discount_price_less;

                $coupon_code= Session::get('coupon_code');
                $coupon_percentage= Session::get('coupon_percentage');

                //Update Counpon Used
                Coupons::where('coupon_code', $coupon_code)->update([
                    'coupon_used'=> DB::raw('coupon_used+1') 
                ]);

            }
            else
            {
                //If no coupon used
                $plan_amount=$plan_info->plan_price;
                $coupon_code= NULL;
                $coupon_percentage= NULL;
            }

            $user->plan_id = $plan_id;
            $user->start_date = strtotime(date('m/d/Y'));             
            $user->exp_date = strtotime(date('m/d/Y', strtotime("+$plan_days days")));
             
            $user->plan_amount = $plan_amount;

            //$user->subscription_status = 0;
            $user->save();
 

            $payment_trans = new Transactions;

            $payment_trans->user_id = $user_id;
            $payment_trans->email = $user_email;
            $payment_trans->plan_id = $plan_id;
            $payment_trans->gateway = 'Paypal';
            $payment_trans->payment_amount = $plan_amount;
            $payment_trans->payment_id = $payment_id;

            $payment_trans->coupon_code = $coupon_code;
            $payment_trans->coupon_percentage = $coupon_percentage;

            $payment_trans->date = strtotime(date('m/d/Y H:i:s'));
            
            $payment_trans->save();

            Session::flash('coupon_code',Session::get('coupon_code'));
            Session::flash('coupon_percentage',Session::get('coupon_percentage'));

            Session::flash('plan_id',Session::get('plan_id'));

            //Subscription Create Email
            $user_full_name=$user->name;

            $data_email = array(
                'name' => $user_full_name
                 );    

             
            try{

                \Mail::send('emails.subscription_created', $data_email, function($message) use ($user,$user_full_name){
                    $message->to($user->email, $user_full_name)
                        ->from(getcong('site_email'), getcong('site_name')) 
                        ->subject('Subscription Created');
                });
        
            }catch (\Throwable $e) {
             
                \Log::info($e->getMessage());                                 
            }


            \Session::flash('success',trans('words.payment_success'));
            return redirect('dashboard');
             
        } else {
            
            return $this->redirectWithGatewayError(trans('words.payment_failed'), $plan_id);
        
        }
    }

    /**
     * cancel transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function paypal_fail()
    {
            return $this->redirectWithGatewayError(trans('words.payment_failed'));
 
    }

}
