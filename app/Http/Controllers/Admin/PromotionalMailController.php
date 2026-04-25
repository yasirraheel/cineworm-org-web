<?php

namespace App\Http\Controllers\Admin;

use App\PromotionalSendingDomain;
use App\PromotionalSmtpServer;
use App\PromotionalTrackingDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class PromotionalMailController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');

        parent::__construct();
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
        $server->notes = $inputs['notes'] ?? null;

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

    public function saveSendingDomain(Request $request)
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $data = \Request::except(array('_token'));

        $rule = array(
            'domain' => 'required',
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

        $domain->smtp_server_id = !empty($inputs['smtp_server_id']) ? (int) $inputs['smtp_server_id'] : null;
        $domain->domain = trim($inputs['domain']);
        $domain->selector = trim($inputs['selector']);
        $domain->dkim_type = $inputs['dkim_type'];
        $domain->dkim_value = $inputs['dkim_value'] ?? null;
        $domain->return_path_subdomain = $inputs['return_path_subdomain'] ?? null;
        $domain->spf_value = $inputs['spf_value'] ?? null;
        $domain->dmarc_policy = $inputs['dmarc_policy'];
        $domain->dmarc_report_email = $inputs['dmarc_report_email'] ?? null;
        $domain->dmarc_alignment = $inputs['dmarc_alignment'] ?? 'relaxed';
        $domain->dkim_status = !empty($inputs['dkim_status']) ? 1 : 0;
        $domain->spf_status = !empty($inputs['spf_status']) ? 1 : 0;
        $domain->dmarc_status = !empty($inputs['dmarc_status']) ? 1 : 0;
        $domain->status = (int) ($inputs['status'] ?? 0);
        $domain->notes = $inputs['notes'] ?? null;
        $domain->verified_at = ($domain->dkim_status && $domain->spf_status && $domain->dmarc_status)
            ? ($domain->verified_at ?: Carbon::now())
            : null;
        $domain->save();

        \Session::flash('flash_message', !empty($inputs['id']) ? trans('words.successfully_updated') : trans('words.added'));

        return redirect('admin/promo_mail/sending-domains');
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
        $domain->notes = $inputs['notes'] ?? null;
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
