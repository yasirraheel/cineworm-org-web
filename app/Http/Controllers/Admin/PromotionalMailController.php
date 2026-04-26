<?php

namespace App\Http\Controllers\Admin;

use App\PromotionalSendingDomain;
use App\PromotionalSmtpServer;
use App\PromotionalTrackingDomain;
use App\Services\PromotionalMailDnsService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

class PromotionalMailController extends MainAdminController
{
    protected $dnsService;

    public function __construct()
    {
        $this->middleware('auth');

        parent::__construct();
        $this->dnsService = new PromotionalMailDnsService();
    }

    protected function ensureAdminAccess()
    {
        if (Auth::User()->usertype != 'Admin' && Auth::User()->usertype != 'Sub_Admin') {
            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
        }

        return null;
    }

    public function servers()
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $page_title = 'Promotional SMTP Servers';
        $servers = PromotionalSmtpServer::orderBy('is_default', 'desc')->orderBy('server_name')->paginate(10);
        $sendingDomainsCount = PromotionalSendingDomain::count();
        $trackingDomainsCount = PromotionalTrackingDomain::count();

        return view('admin.pages.promo_mail.servers', compact(
            'page_title',
            'servers',
            'sendingDomainsCount',
            'trackingDomainsCount'
        ));
    }

    public function addServer()
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $page_title = 'Add SMTP Server';

        return view('admin.pages.promo_mail.server_form', compact('page_title'));
    }

    public function editServer($id)
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $server = PromotionalSmtpServer::findOrFail($id);
        $page_title = 'Edit SMTP Server';

        return view('admin.pages.promo_mail.server_form', compact('page_title', 'server'));
    }

    public function saveServer(Request $request)
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $data = \Request::except(array('_token'));

        $rule = array(
            'server_name' => 'required',
            'sender_email' => 'required|email',
            'host' => 'required',
            'port' => 'required|integer|min:1',
            'username' => 'required',
            'gateway_type' => 'required',
        );

        if (empty($request->id)) {
            $rule['smtp_password'] = 'required';
        }

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $inputs = $request->all();
        $server = !empty($inputs['id']) ? PromotionalSmtpServer::findOrFail($inputs['id']) : new PromotionalSmtpServer;

        $isDefault = !empty($inputs['is_default']) ? 1 : 0;

        if ($isDefault) {
            PromotionalSmtpServer::query()->update(['is_default' => 0]);
        }

        $server->server_name = $inputs['server_name'];
        $server->gateway_type = $inputs['gateway_type'];
        $server->from_name = $inputs['from_name'] ?? null;
        $server->sender_email = $inputs['sender_email'];
        $server->reply_to_email = $inputs['reply_to_email'] ?? null;
        $server->host = $inputs['host'];
        $server->port = (int) $inputs['port'];
        $server->encryption = $inputs['encryption'] ?? null;
        $server->username = $inputs['username'];
        $server->ehlo_domain = $inputs['ehlo_domain'] ?? null;
        $server->min_delay_per_message = (int) ($inputs['min_delay_per_message'] ?? 0);
        $server->max_delay_per_message = (int) ($inputs['max_delay_per_message'] ?? 0);
        $server->pause_after_messages = (int) ($inputs['pause_after_messages'] ?? 0);
        $server->pause_duration = (int) ($inputs['pause_duration'] ?? 0);
        $server->reset_counter_after_messages = (int) ($inputs['reset_counter_after_messages'] ?? 0);
        $server->max_messages_per_day = (int) ($inputs['max_messages_per_day'] ?? 0);
        $server->status = (int) ($inputs['status'] ?? 0);
        $server->is_default = $isDefault;

        if (!empty($inputs['smtp_password'])) {
            $server->smtp_password = Crypt::encrypt($inputs['smtp_password']);
        }

        $server->save();

        \Session::flash('flash_message', !empty($inputs['id']) ? trans('words.successfully_updated') : trans('words.added'));

        return redirect('admin/promo_mail/servers');
    }

    public function deleteServer($id)
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $server = PromotionalSmtpServer::findOrFail($id);
        PromotionalSendingDomain::where('smtp_server_id', $server->id)->update(['smtp_server_id' => null]);
        PromotionalTrackingDomain::where('smtp_server_id', $server->id)->update(['smtp_server_id' => null]);
        $server->delete();

        \Session::flash('flash_message', trans('words.deleted'));

        return redirect('admin/promo_mail/servers');
    }

    public function testServer($id, Request $request)
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $server = PromotionalSmtpServer::findOrFail($id);
        $testEmail = trim($request->get('test_email'));

        if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'resp_status' => 'failed',
                'resp_msg' => 'Please enter a valid test email address.',
            ]);
        }

        if (!$server->status) {
            return response()->json([
                'resp_status' => 'failed',
                'resp_msg' => 'Please activate this SMTP server before sending a test email.',
            ]);
        }

        if (empty($server->decrypted_password)) {
            return response()->json([
                'resp_status' => 'failed',
                'resp_msg' => 'SMTP password is missing for this server.',
            ]);
        }

        try {
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', $server->host);
            Config::set('mail.mailers.smtp.port', $server->port);
            Config::set('mail.mailers.smtp.encryption', $server->encryption ?: null);
            Config::set('mail.mailers.smtp.username', $server->username);
            Config::set('mail.mailers.smtp.password', $server->decrypted_password);
            Config::set('mail.mailers.smtp.local_domain', $server->ehlo_domain ?: null);
            Config::set('mail.from.address', $server->sender_email);
            Config::set('mail.from.name', $server->from_name ?: $server->server_name);

            if (app()->bound('mail.manager') && method_exists(app('mail.manager'), 'forgetMailers')) {
                app('mail.manager')->forgetMailers();
            }

            $userName = 'Promotional SMTP Test';
            $dataEmail = ['name' => $userName];

            \Mail::mailer('smtp')->send('emails.test_smtp', $dataEmail, function ($message) use ($testEmail, $userName, $server) {
                $message->to($testEmail, $userName)
                    ->from($server->sender_email, $server->from_name ?: $server->server_name)
                    ->subject('Test Promotional SMTP');

                if (!empty($server->reply_to_email)) {
                    $message->replyTo($server->reply_to_email);
                }
            });

            return response()->json([
                'resp_status' => 'success',
                'resp_msg' => 'Test email sent successfully.',
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'resp_status' => 'failed',
                'resp_msg' => $exception->getMessage(),
            ]);
        }
    }

    public function sendingDomains()
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $page_title = 'Sending Domains';
        $domains = PromotionalSendingDomain::with('smtpServer')->orderBy('verified_at', 'desc')->orderBy('domain')->paginate(10);

        return view('admin.pages.promo_mail.sending_domains', compact('page_title', 'domains'));
    }

    public function addSendingDomain()
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $page_title = 'Add Sending Domain';
        $servers = PromotionalSmtpServer::where('status', 1)->orderBy('server_name')->get();

        return view('admin.pages.promo_mail.sending_domain_form', compact('page_title', 'servers'));
    }

    public function editSendingDomain($id)
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $page_title = 'Edit Sending Domain';
        $servers = PromotionalSmtpServer::where('status', 1)->orderBy('server_name')->get();
        $domain = PromotionalSendingDomain::findOrFail($id);

        return view('admin.pages.promo_mail.sending_domain_form', compact('page_title', 'servers', 'domain'));
    }

    public function sendingDomainDns($id)
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $page_title = 'DNS Records';
        $domain = PromotionalSendingDomain::with('smtpServer')->findOrFail($id);
        $this->dnsService->initializeDomain($domain);
        $domain = $domain->fresh(['smtpServer']);
        $dnsRecords = $this->dnsService->getDnsRecords($domain);
        $opensslCheck = empty($dnsRecords['dkim']['value']) ? $this->dnsService->checkOpenSslReadiness() : null;

        return view('admin.pages.promo_mail.sending_domain_dns', compact(
            'page_title',
            'domain',
            'dnsRecords',
            'opensslCheck'
        ));
    }

    public function saveSendingDomain(Request $request)
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $data = \Request::except(array('_token'));

        $rule = array(
            'domain' => 'required|regex:/^[a-zA-Z0-9][a-zA-Z0-9.-]*\.[a-zA-Z]{2,}$/|unique:promotional_sending_domains,domain,'.(!empty($request->id) ? (int) $request->id : 'NULL').',id',
            'selector' => 'required',
            'dkim_type' => 'required',
            'dmarc_policy' => 'required',
        );

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $inputs = $request->all();
        $domain = !empty($inputs['id']) ? PromotionalSendingDomain::findOrFail($inputs['id']) : new PromotionalSendingDomain;
        $isNewDomain = empty($inputs['id']);

        $domain->smtp_server_id = !empty($inputs['smtp_server_id']) ? (int) $inputs['smtp_server_id'] : null;
        $domain->domain = strtolower(trim($inputs['domain']));
        $domain->selector = trim($inputs['selector']);
        $domain->dkim_type = $inputs['dkim_type'];
        $domain->return_path_subdomain = $inputs['return_path_subdomain'] ?? null;
        $domain->dmarc_policy = $inputs['dmarc_policy'];
        $domain->dmarc_report_email = $inputs['dmarc_report_email'] ?? null;
        $domain->dmarc_alignment = $inputs['dmarc_alignment'] ?? 'relaxed';

        $requiresReverification = $isNewDomain || $domain->isDirty([
            'smtp_server_id',
            'domain',
            'selector',
            'return_path_subdomain',
            'dmarc_policy',
            'dmarc_report_email',
            'dmarc_alignment',
        ]);

        if ($requiresReverification) {
            $domain->dkim_status = 0;
            $domain->spf_status = 0;
            $domain->dmarc_status = 0;
            $domain->verified_at = null;
            $domain->dns_checked_at = null;
            $domain->status = 0;
        }

        $domain->save();
        $this->dnsService->initializeDomain($domain);

        \Session::flash('flash_message', !empty($inputs['id']) ? trans('words.successfully_updated') : trans('words.added'));

        return redirect('admin/promo_mail/sending-domains/dns/'.$domain->id);
    }

    public function verifySendingDomain($id)
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        try {
            $domain = PromotionalSendingDomain::findOrFail($id);
            $this->dnsService->initializeDomain($domain);
            $results = $this->dnsService->verifyDns($domain->fresh());

            return response()->json([
                'resp_status' => 'success',
                'resp_msg' => $results['dkim']
                    ? 'DNS verification completed.'
                    : 'Verification is incomplete. Please check your DNS records and try again.',
                'dkim' => $results['dkim'],
                'spf' => $results['spf'],
                'dmarc' => $results['dmarc'],
                'messages' => $results['messages'],
                'verified_at' => optional($domain->fresh()->verified_at)->format('M d, Y h:i A'),
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'resp_status' => 'failed',
                'resp_msg' => $exception->getMessage(),
            ]);
        }
    }

    public function regenerateSendingDomainKeys($id)
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        try {
            $domain = PromotionalSendingDomain::findOrFail($id);
            $this->dnsService->generateKeyPair($domain, $domain->selector ?: 'xsender');
            \Session::flash('flash_message', 'DKIM keys regenerated successfully.');
        } catch (\Throwable $exception) {
            \Session::flash('error_flash_message', $exception->getMessage());
        }

        return redirect('admin/promo_mail/sending-domains/dns/'.$id);
    }

    public function deleteSendingDomain($id)
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        PromotionalSendingDomain::findOrFail($id)->delete();

        \Session::flash('flash_message', trans('words.deleted'));

        return redirect('admin/promo_mail/sending-domains');
    }

    public function trackingDomains()
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $page_title = 'Tracking Domains';
        $domains = PromotionalTrackingDomain::with('smtpServer')->orderBy('verified_at', 'desc')->orderBy('domain')->paginate(10);

        return view('admin.pages.promo_mail.tracking_domains', compact('page_title', 'domains'));
    }

    public function addTrackingDomain()
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $page_title = 'Add Tracking Domain';
        $servers = PromotionalSmtpServer::where('status', 1)->orderBy('server_name')->get();

        return view('admin.pages.promo_mail.tracking_domain_form', compact('page_title', 'servers'));
    }

    public function editTrackingDomain($id)
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $page_title = 'Edit Tracking Domain';
        $servers = PromotionalSmtpServer::where('status', 1)->orderBy('server_name')->get();
        $domain = PromotionalTrackingDomain::findOrFail($id);

        return view('admin.pages.promo_mail.tracking_domain_form', compact('page_title', 'servers', 'domain'));
    }

    public function saveTrackingDomain(Request $request)
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $data = \Request::except(array('_token'));

        $rule = array(
            'domain' => 'required',
            'cname_target' => 'required',
        );

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $inputs = $request->all();
        $domain = !empty($inputs['id']) ? PromotionalTrackingDomain::findOrFail($inputs['id']) : new PromotionalTrackingDomain;

        $isVerified = !empty($inputs['is_verified']);

        $domain->smtp_server_id = !empty($inputs['smtp_server_id']) ? (int) $inputs['smtp_server_id'] : null;
        $domain->domain = trim($inputs['domain']);
        $domain->cname_target = trim($inputs['cname_target']);
        $domain->status = (int) ($inputs['status'] ?? 0);
        $domain->verified_at = $isVerified ? ($domain->verified_at ?: Carbon::now()) : null;
        $domain->save();

        \Session::flash('flash_message', !empty($inputs['id']) ? trans('words.successfully_updated') : trans('words.added'));

        return redirect('admin/promo_mail/tracking-domains');
    }

    public function deleteTrackingDomain($id)
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        PromotionalTrackingDomain::findOrFail($id)->delete();

        \Session::flash('flash_message', trans('words.deleted'));

        return redirect('admin/promo_mail/tracking-domains');
    }
}
