<?php

namespace App\Http\Controllers;

use App\PromotionalCampaign;
use App\PromotionalCampaignSend;
use App\PromotionalContact;
use App\PromotionalContactList;
use App\PromotionalSendingDomain;
use App\PromotionalSmtpServer;
use App\PromotionalTrackingDomain;
use App\Services\PromotionalCampaignService;
use App\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserPromotionController extends Controller
{
    protected $campaignService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->campaignService = new PromotionalCampaignService();
    }

    protected function ensurePromotionAccess()
    {
        if (!Auth::check()) {
            \Session::flash('error_flash_message', trans('words.access_denied'));
            return redirect('login');
        }

        if (in_array(Auth::user()->usertype, ['Admin', 'Sub_Admin', 'Moderator'], true)) {
            return redirect('admin/dashboard');
        }

        $plan = $this->currentUserPlan();
        $features = $plan ? $plan->getEffectiveFeatureKeys() : [];

        if (!in_array('promotion_services', $features, true)) {
            \Session::flash('error_flash_message', 'Your current subscription plan does not include Promotion Services.');
            return redirect('dashboard');
        }

        return null;
    }

    protected function currentUserPlan()
    {
        $user = Auth::user();
        if (empty($user->plan_id)) {
            return null;
        }

        return SubscriptionPlan::find($user->plan_id);
    }

    public function index()
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        $userId = Auth::id();
        $page_title = 'Promotion Services';
        $listsCount = PromotionalContactList::where('user_id', $userId)->count();
        $contactsCount = PromotionalContact::where('user_id', $userId)->count();
        $campaignsCount = PromotionalCampaign::where('user_id', $userId)->count();
        $runningCampaignsCount = PromotionalCampaign::where('user_id', $userId)
            ->where('status', PromotionalCampaign::STATUS_RUNNING)
            ->count();
        $campaigns = PromotionalCampaign::with('contactList')
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);

        return view('pages.user.promotions.index', compact(
            'page_title',
            'listsCount',
            'contactsCount',
            'campaignsCount',
            'runningCampaignsCount',
            'campaigns'
        ));
    }

    public function lists()
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        $page_title = 'Email Lists';
        $lists = PromotionalContactList::withCount('contacts')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('pages.user.promotions.lists', compact('page_title', 'lists'));
    }

    public function saveList(Request $request)
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|max:255',
            'description' => 'nullable|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $list = !empty($request->id)
            ? PromotionalContactList::where('user_id', Auth::id())->findOrFail($request->id)
            : new PromotionalContactList();

        $list->user_id = Auth::id();
        $list->name = trim($request->name);
        $list->description = $request->description;
        $list->status = 1;
        $list->save();

        \Session::flash('flash_message', !empty($request->id) ? trans('words.successfully_updated') : trans('words.added'));

        return redirect('promotions/lists');
    }

    public function contacts($listId)
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        $page_title = 'Manage Contacts';
        $list = PromotionalContactList::where('user_id', Auth::id())->findOrFail($listId);
        $contacts = PromotionalContact::where('user_id', Auth::id())
            ->where('contact_list_id', $list->id)
            ->latest()
            ->paginate(20);

        return view('pages.user.promotions.contacts', compact('page_title', 'list', 'contacts'));
    }

    public function saveContact(Request $request, $listId)
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        $list = PromotionalContactList::where('user_id', Auth::id())->findOrFail($listId);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'name' => 'nullable|max:255',
            'company' => 'nullable|max:255',
            'tags' => 'nullable|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $contact = PromotionalContact::updateOrCreate(
            [
                'contact_list_id' => $list->id,
                'email' => strtolower(trim($request->email)),
            ],
            [
                'user_id' => Auth::id(),
                'name' => trim((string) $request->name),
                'company' => $request->company,
                'tags' => $request->tags,
                'status' => 1,
            ]
        );

        \Session::flash('flash_message', $contact->wasRecentlyCreated ? trans('words.added') : trans('words.successfully_updated'));

        return redirect()->back();
    }

    public function importContacts(Request $request, $listId)
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        $list = PromotionalContactList::where('user_id', Auth::id())->findOrFail($listId);
        $lines = [];

        if ($request->hasFile('csv_file')) {
            $lines = file($request->file('csv_file')->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        } elseif ($request->filled('import_source')) {
            $lines = preg_split("/\r\n|\n|\r/", trim($request->import_source));
        }

        if (empty($lines)) {
            \Session::flash('error_flash_message', 'Please provide CSV rows or upload a CSV file.');
            return redirect()->back();
        }

        $imported = 0;
        foreach ($lines as $index => $line) {
            $columns = str_getcsv($line);
            if ($index === 0 && isset($columns[0]) && stripos($columns[0], 'email') !== false) {
                continue;
            }

            $email = isset($columns[1]) ? trim($columns[1]) : trim($columns[0] ?? '');
            $name = isset($columns[1]) ? trim($columns[0]) : '';
            $company = trim($columns[2] ?? '');
            $tags = trim($columns[3] ?? '');

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            PromotionalContact::updateOrCreate(
                [
                    'contact_list_id' => $list->id,
                    'email' => strtolower($email),
                ],
                [
                    'user_id' => Auth::id(),
                    'name' => $name,
                    'company' => $company,
                    'tags' => $tags,
                    'status' => 1,
                ]
            );

            $imported++;
        }

        \Session::flash('flash_message', $imported . ' contacts imported successfully.');

        return redirect()->back();
    }

    public function downloadSampleContactsFile($listId)
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        $list = PromotionalContactList::where('user_id', Auth::id())->findOrFail($listId);
        $filename = 'sample_contacts_list_' . $list->id . '.csv';
        $rows = [
            ['name', 'email', 'company', 'tags'],
            ['John Doe', 'john@example.com', 'Studio X', 'vip,investor'],
            ['Jane Smith', 'jane@example.com', 'Press House', 'press,media'],
            ['Alex Brown', 'alex@example.com', 'Brand Partner', 'partner,sponsor'],
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function deleteContact($listId, $contactId)
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        PromotionalContact::where('user_id', Auth::id())
            ->where('contact_list_id', $listId)
            ->where('id', $contactId)
            ->delete();

        \Session::flash('flash_message', trans('words.deleted'));

        return redirect()->back();
    }

    public function campaigns()
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        $page_title = 'Email Campaigns';
        $campaigns = PromotionalCampaign::with('contactList')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);

        return view('pages.user.promotions.campaigns', compact('page_title', 'campaigns'));
    }

    public function campaignForm($id = null)
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        $page_title = $id ? 'Edit Campaign' : 'Create Campaign';
        $campaign = $id ? PromotionalCampaign::where('user_id', Auth::id())->findOrFail($id) : null;
        $lists = PromotionalContactList::withCount('contacts')->where('user_id', Auth::id())->where('status', 1)->orderBy('name')->get();
        $servers = PromotionalSmtpServer::where('status', 1)->orderBy('is_default', 'desc')->orderBy('server_name')->get();
        $sendingDomains = PromotionalSendingDomain::where('status', 1)->where('dkim_status', 1)->orderBy('domain')->get();
        $trackingDomains = PromotionalTrackingDomain::where('status', 1)->orderBy('domain')->get();

        return view('pages.user.promotions.campaign_form', compact(
            'page_title',
            'campaign',
            'lists',
            'servers',
            'sendingDomains',
            'trackingDomains'
        ));
    }

    public function saveCampaign(Request $request)
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'contact_list_id' => 'required|integer',
            'smtp_server_id' => 'required|integer',
            'subject' => 'required|max:255',
            'from_name' => 'required|max:255',
            'from_email' => 'required|email|max:255',
            'reply_to_email' => 'nullable|email|max:255',
            'html_content' => 'required',
            'scheduled_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $list = PromotionalContactList::where('user_id', Auth::id())->findOrFail($request->contact_list_id);
        $server = PromotionalSmtpServer::where('status', 1)->findOrFail($request->smtp_server_id);
        $sendingDomain = !empty($request->sending_domain_id) ? PromotionalSendingDomain::where('status', 1)->findOrFail($request->sending_domain_id) : null;
        $trackingDomain = !empty($request->tracking_domain_id) ? PromotionalTrackingDomain::where('status', 1)->findOrFail($request->tracking_domain_id) : null;

        if ($sendingDomain && !str_ends_with(strtolower($request->from_email), '@' . strtolower($sendingDomain->domain))) {
            \Session::flash('error_flash_message', 'From email must match the selected sending domain.');
            return redirect()->back()->withInput();
        }

        if ($sendingDomain && !empty($sendingDomain->smtp_server_id) && (int) $sendingDomain->smtp_server_id !== (int) $server->id) {
            \Session::flash('error_flash_message', 'Selected sending domain belongs to a different SMTP server.');
            return redirect()->back()->withInput();
        }

        $campaign = !empty($request->id)
            ? PromotionalCampaign::where('user_id', Auth::id())->findOrFail($request->id)
            : new PromotionalCampaign();

        $campaign->user_id = Auth::id();
        $campaign->smtp_server_id = $server->id;
        $campaign->sending_domain_id = $sendingDomain ? $sendingDomain->id : null;
        $campaign->tracking_domain_id = $trackingDomain ? $trackingDomain->id : null;
        $campaign->contact_list_id = $list->id;
        $campaign->name = trim($request->name);
        $campaign->subject = trim($request->subject);
        $campaign->preview_text = $request->preview_text;
        $campaign->from_name = trim($request->from_name);
        $campaign->from_email = strtolower(trim($request->from_email));
        $campaign->reply_to_email = $request->reply_to_email;
        $campaign->html_content = $request->html_content;
        $campaign->plain_text = trim(strip_tags($request->html_content));
        $campaign->scheduled_at = $request->scheduled_at ? date('Y-m-d H:i:s', strtotime($request->scheduled_at)) : null;
        $campaign->status = PromotionalCampaign::STATUS_DRAFT;
        $campaign->started_at = null;
        $campaign->completed_at = null;
        $campaign->total_contacts = 0;
        $campaign->processed_contacts = 0;
        $campaign->success_count = 0;
        $campaign->failed_count = 0;
        $campaign->last_error = null;
        $campaign->save();

        PromotionalCampaignSend::where('campaign_id', $campaign->id)->delete();

        \Session::flash('flash_message', !empty($request->id) ? trans('words.successfully_updated') : trans('words.added'));

        return redirect('promotions/campaigns/' . $campaign->id);
    }

    public function showCampaign($id)
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        $page_title = 'Campaign Details';
        $campaign = PromotionalCampaign::with(['contactList', 'smtpServer', 'sendingDomain', 'trackingDomain'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        $recentSends = PromotionalCampaignSend::with('contact')
            ->where('campaign_id', $campaign->id)
            ->latest()
            ->paginate(20);

        return view('pages.user.promotions.campaign_show', compact('page_title', 'campaign', 'recentSends'));
    }

    public function launchCampaign($id)
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        $campaign = PromotionalCampaign::where('user_id', Auth::id())->findOrFail($id);
        $contactsCount = PromotionalContact::where('user_id', Auth::id())
            ->where('contact_list_id', $campaign->contact_list_id)
            ->where('status', 1)
            ->count();

        if ($contactsCount === 0) {
            \Session::flash('error_flash_message', 'This campaign cannot start because the selected email list has no active contacts.');
            return redirect('promotions/campaigns/' . $campaign->id);
        }

        $this->campaignService->launchCampaign($campaign);

        \Session::flash('flash_message', $campaign->scheduled_at && strtotime($campaign->scheduled_at) > time()
            ? 'Campaign scheduled successfully.'
            : 'Campaign started successfully.');

        return redirect('promotions/campaigns/' . $campaign->id);
    }

    public function pauseCampaign($id)
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        $campaign = PromotionalCampaign::where('user_id', Auth::id())->findOrFail($id);
        $campaign->status = PromotionalCampaign::STATUS_PAUSED;
        $campaign->save();

        \Session::flash('flash_message', 'Campaign paused successfully.');

        return redirect('promotions/campaigns/' . $campaign->id);
    }

    public function resumeCampaign($id)
    {
        if ($redirect = $this->ensurePromotionAccess()) {
            return $redirect;
        }

        $campaign = PromotionalCampaign::where('user_id', Auth::id())->findOrFail($id);
        $campaign->status = PromotionalCampaign::STATUS_RUNNING;
        $campaign->started_at = $campaign->started_at ?: now();
        $campaign->save();
        $this->campaignService->processCampaignBatch($campaign->fresh(), 10);

        \Session::flash('flash_message', 'Campaign resumed successfully.');

        return redirect('promotions/campaigns/' . $campaign->id);
    }
}
