<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class ZoomOAuthController extends Controller
{
    public function connect()
    {
        $settings = \App\Settings::first();
        if (!$settings || empty($settings->zoom_client_id)) {
            \Session::flash('error_flash_message', 'Zoom API is not configured by the administrator.');
            return redirect()->back();
        }

        $client_id = $settings->zoom_client_id;
        $redirect_uri = urlencode(url('user/zoom/callback'));
        $zoom_oauth_url = "https://zoom.us/oauth/authorize?response_type=code&client_id={$client_id}&redirect_uri={$redirect_uri}";

        return redirect($zoom_oauth_url);
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            \Session::flash('error_flash_message', 'Zoom connection was denied or failed.');
            return redirect('user/live_broadcasts');
        }

        $code = $request->get('code');
        if (!$code) {
            \Session::flash('error_flash_message', 'Invalid authorization code.');
            return redirect('user/live_broadcasts');
        }

        $settings = \App\Settings::first();
        $client_id = $settings->zoom_client_id;
        $client_secret = $settings->zoom_client_secret;
        $redirect_uri = url('user/zoom/callback');

        try {
            $client = new Client();
            $response = $client->request('POST', 'https://zoom.us/oauth/token', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("{$client_id}:{$client_secret}"),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $redirect_uri,
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            
            if (isset($body['access_token'])) {
                $user = Auth::user();
                $user->zoom_access_token = $body['access_token'];
                $user->zoom_refresh_token = $body['refresh_token'];
                
                // Fetch user info to get account ID
                $userResponse = $client->request('GET', 'https://api.zoom.us/v2/users/me', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $body['access_token']
                    ]
                ]);
                $userInfo = json_decode($userResponse->getBody(), true);
                $user->zoom_account_id = $userInfo['account_id'] ?? null;
                $user->save();

                \Session::flash('flash_message', 'Zoom account connected successfully!');
            } else {
                \Session::flash('error_flash_message', 'Failed to retrieve access token from Zoom.');
            }
        } catch (\Exception $e) {
            \Session::flash('error_flash_message', 'Error connecting to Zoom: ' . $e->getMessage());
        }

        return redirect('user/live_broadcasts');
    }
}
