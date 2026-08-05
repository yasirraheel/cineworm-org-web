<?php

namespace App\Http\Controllers;

use App\Services\WhatsappCampaignService;
use App\Services\WhatsappServerService;
use App\SubscriptionPlan;
use App\WhatsappCampaign;
use App\WhatsappCampaignSend;
use App\WhatsappContact;
use App\WhatsappContactList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserWhatsappController extends Controller
{
    protected $campaignService;

    public function __construct()
    {
        $this->middleware('auth');
        $this->campaignService = new WhatsappCampaignService();
    }

    protected function getSessionId(): string
    {
        return 'user_' . Auth::id();
    }

    protected function ensureWhatsappAccess()
    {
        if (!Auth::check()) {
            Session::flash('error_flash_message', trans('words.access_denied'));
            return redirect('login');
        }

        if (in_array(Auth::user()->usertype, ['Admin', 'Sub_Admin', 'Moderator'], true)) {
            return redirect('admin/dashboard');
        }

        $user = Auth::user();
        $plan = !empty($user->plan_id) ? SubscriptionPlan::find($user->plan_id) : null;
        $features = $plan ? $plan->getEffectiveFeatureKeys() : [];

        if (!in_array('whatsapp_marketing_access', $features, true)) {
            Session::flash('error_flash_message', 'Your current subscription plan does not include WhatsApp Web & Campaigns.');
            return redirect('dashboard');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return $redirect;
        }

        app(WhatsappServerService::class)->ensureRunning();

        $userId = Auth::id();
        $sessionId = $this->getSessionId();
        $page_title = 'WhatsApp Web & Campaigns';

        $status = $this->campaignService->requestServer('get', '/status', [], $sessionId);

        $listsCount = WhatsappContactList::where('user_id', $userId)->count();
        $contactsCount = WhatsappContact::where('user_id', $userId)->count();
        $campaignsCount = WhatsappCampaign::where('user_id', $userId)->count();
        $runningCampaignsCount = WhatsappCampaign::where('user_id', $userId)
            ->where('status', WhatsappCampaign::STATUS_RUNNING)
            ->count();
        $campaigns = WhatsappCampaign::with('contactList')
            ->where('user_id', $userId)
            ->latest()
            ->limit(8)
            ->get();
        $recentSends = WhatsappCampaignSend::whereHas('campaign', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->with(['campaign', 'contact'])
            ->latest()
            ->limit(10)
            ->get();

        return view('pages.user.whatsapp.index', compact(
            'page_title',
            'status',
            'listsCount',
            'contactsCount',
            'campaignsCount',
            'runningCampaignsCount',
            'campaigns',
            'recentSends'
        ));
    }

    public function status()
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return response()->json(['ok' => false, 'error' => 'Access Denied'], 403);
        }

        $status = $this->campaignService->requestServer('get', '/status', [], $this->getSessionId());
        return response()->json($status);
    }

    public function connect()
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return response()->json(['ok' => false, 'error' => 'Access Denied'], 403);
        }

        app(WhatsappServerService::class)->ensureRunning();
        $status = $this->campaignService->requestServer('post', '/connect', [], $this->getSessionId());
        return response()->json($status);
    }

    public function qr()
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return response()->json(['ok' => false, 'error' => 'Access Denied'], 403);
        }

        $qrData = $this->campaignService->requestServer('get', '/qr', [], $this->getSessionId());
        return response()->json($qrData);
    }

    public function logoutSession()
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return response()->json(['ok' => false, 'error' => 'Access Denied'], 403);
        }

        $status = $this->campaignService->requestServer('post', '/logout', [], $this->getSessionId());
        return response()->json($status);
    }

    public function lists()
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return $redirect;
        }

        $page_title = 'WhatsApp Contact Lists';
        $lists = WhatsappContactList::withCount('contacts')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('pages.user.whatsapp.lists', compact('page_title', 'lists'));
    }

    public function saveList(Request $request)
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return $redirect;
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'nullable|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $userId = Auth::id();
        $list = $request->filled('id')
            ? WhatsappContactList::where('user_id', $userId)->findOrFail($request->id)
            : new WhatsappContactList();

        $list->user_id = $userId;
        $list->name = trim($request->name);
        $list->description = $request->description;
        $list->status = (int) $request->input('status', 1);
        $list->save();

        Session::flash('flash_message', $request->filled('id') ? trans('words.successfully_updated') : trans('words.added'));

        return redirect('user/whatsapp/lists');
    }

    public function contacts($listId)
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return $redirect;
        }

        $page_title = 'WhatsApp Contacts';
        $list = WhatsappContactList::where('user_id', Auth::id())->findOrFail($listId);
        $contacts = WhatsappContact::where('contact_list_id', $list->id)->latest()->paginate(25);

        return view('pages.user.whatsapp.contacts', compact('page_title', 'list', 'contacts'));
    }

    public function saveContact(Request $request, $listId)
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return $redirect;
        }

        $list = WhatsappContactList::where('user_id', Auth::id())->findOrFail($listId);

        $validator = Validator::make($request->all(), [
            'phone' => 'required|max:50',
            'name' => 'nullable|max:255',
            'company' => 'nullable|max:255',
            'tags' => 'nullable|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $phone = preg_replace('/[^\d]/', '', $request->phone);
        if (strlen($phone) < 8) {
            return redirect()->back()->withErrors(['phone' => 'Please provide a valid phone number with country code.'])->withInput();
        }

        $contact = $request->filled('id')
            ? WhatsappContact::where('user_id', Auth::id())->findOrFail($request->id)
            : new WhatsappContact();

        $contact->user_id = Auth::id();
        $contact->contact_list_id = $list->id;
        $contact->phone = $phone;
        $contact->name = $request->name;
        $contact->company = $request->company;
        $contact->tags = $request->tags;
        $contact->status = (int) $request->input('status', 1);
        $contact->save();

        Session::flash('flash_message', $request->filled('id') ? trans('words.successfully_updated') : trans('words.added'));

        return redirect('user/whatsapp/lists/' . $list->id . '/contacts');
    }

    public function importContacts(Request $request, $listId)
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return $redirect;
        }

        $list = WhatsappContactList::where('user_id', Auth::id())->findOrFail($listId);

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return redirect()->back()->withErrors(['file' => 'Unable to read the uploaded CSV file.']);
        }

        $header = fgetcsv($handle, 2000, ',');
        if (!$header) {
            fclose($handle);
            return redirect()->back()->withErrors(['file' => 'The uploaded CSV file is empty.']);
        }

        $headerMap = [];
        foreach ($header as $index => $colName) {
            $normalized = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', $colName)));
            $headerMap[$normalized] = $index;
        }

        $phoneIdx = $headerMap['phone'] ?? $headerMap['mobile'] ?? $headerMap['whatsapp'] ?? 0;
        $nameIdx = $headerMap['name'] ?? $headerMap['fullname'] ?? null;
        $companyIdx = $headerMap['company'] ?? $headerMap['business'] ?? null;
        $tagsIdx = $headerMap['tags'] ?? $headerMap['tag'] ?? null;

        $imported = 0;
        $updated = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle, 2000, ',')) !== false) {
            $rawPhone = $row[$phoneIdx] ?? '';
            $phone = preg_replace('/[^\d]/', '', $rawPhone);

            if (strlen($phone) < 8) {
                $skipped++;
                continue;
            }

            $name = $nameIdx !== null ? trim($row[$nameIdx] ?? '') : null;
            $company = $companyIdx !== null ? trim($row[$companyIdx] ?? '') : null;
            $tags = $tagsIdx !== null ? trim($row[$tagsIdx] ?? '') : null;

            $contact = WhatsappContact::where('contact_list_id', $list->id)
                ->where('phone', $phone)
                ->first();

            if ($contact) {
                $contact->name = $name ?: $contact->name;
                $contact->company = $company ?: $contact->company;
                $contact->tags = $tags ?: $contact->tags;
                $contact->save();
                $updated++;
            } else {
                WhatsappContact::create([
                    'user_id' => Auth::id(),
                    'contact_list_id' => $list->id,
                    'phone' => $phone,
                    'name' => $name,
                    'company' => $company,
                    'tags' => $tags,
                    'status' => 1,
                ]);
                $imported++;
            }
        }

        fclose($handle);

        Session::flash('flash_message', "CSV imported successfully. Added: {$imported}, Updated: {$updated}, Skipped: {$skipped}.");

        return redirect('user/whatsapp/lists/' . $list->id . '/contacts');
    }

    public function downloadSampleContactsFile()
    {
        $csvHeader = ['phone', 'name', 'company', 'tags'];
        $csvRows = [
            ['15551234567', 'John Doe', 'Acme Corp', 'VIP Client'],
            ['447700900077', 'Jane Smith', 'Film Studio', 'Creator'],
        ];

        $output = implode(',', $csvHeader) . "\n";
        foreach ($csvRows as $row) {
            $output .= implode(',', array_map(function ($field) {
                return '"' . str_replace('"', '""', $field) . '"';
            }, $row)) . "\n";
        }

        return Response::make($output, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sample_whatsapp_contacts.csv"',
        ]);
    }

    public function deleteContact($listId, $contactId)
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return $redirect;
        }

        $list = WhatsappContactList::where('user_id', Auth::id())->findOrFail($listId);
        $contact = WhatsappContact::where('contact_list_id', $list->id)->findOrFail($contactId);
        $contact->delete();

        Session::flash('flash_message', trans('words.deleted'));

        return redirect('user/whatsapp/lists/' . $list->id . '/contacts');
    }

    public function campaigns()
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return $redirect;
        }

        $page_title = 'WhatsApp Campaigns';
        $campaigns = WhatsappCampaign::with('contactList')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('pages.user.whatsapp.campaigns', compact('page_title', 'campaigns'));
    }

    public function campaignForm(Request $request, $id = null)
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return $redirect;
        }

        $userId = Auth::id();
        $campaign = $id ? WhatsappCampaign::where('user_id', $userId)->findOrFail($id) : new WhatsappCampaign();
        $page_title = $campaign->exists ? 'Edit WhatsApp Campaign' : 'Create WhatsApp Campaign';
        $lists = WhatsappContactList::where('user_id', $userId)->where('status', 1)->orderBy('name')->get();

        return view('pages.user.whatsapp.campaign_form', compact('page_title', 'campaign', 'lists'));
    }

    public function saveCampaign(Request $request)
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return $redirect;
        }

        $userId = Auth::id();
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'contact_list_id' => 'required|exists:whatsapp_contact_lists,id',
            'message' => 'required',
            'min_delay_seconds' => 'nullable|integer|min:1|max:120',
            'max_delay_seconds' => 'nullable|integer|min:1|max:180',
            'batch_size' => 'nullable|integer|min:1|max:50',
            'daily_limit' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $list = WhatsappContactList::where('user_id', $userId)->findOrFail($request->contact_list_id);

        $campaign = $request->filled('id')
            ? WhatsappCampaign::where('user_id', $userId)->findOrFail($request->id)
            : new WhatsappCampaign();

        $campaign->user_id = $userId;
        $campaign->title = trim($request->title);
        $campaign->contact_list_id = $list->id;
        $campaign->message = $request->message;
        $campaign->min_delay_seconds = (int) $request->input('min_delay_seconds', 4);
        $campaign->max_delay_seconds = (int) $request->input('max_delay_seconds', 12);
        $campaign->batch_size = (int) $request->input('batch_size', 10);
        $campaign->daily_limit = (int) $request->input('daily_limit', 500);
        $campaign->pause_after_messages = (int) $request->input('pause_after_messages', 50);
        $campaign->pause_duration_seconds = (int) $request->input('pause_duration_seconds', 300);
        $campaign->quiet_hours_start = $request->filled('quiet_hours_start') ? $request->quiet_hours_start : null;
        $campaign->quiet_hours_end = $request->filled('quiet_hours_end') ? $request->quiet_hours_end : null;

        if ($request->filled('scheduled_at')) {
            $campaign->scheduled_at = \Carbon\Carbon::parse($request->scheduled_at);
            $campaign->status = WhatsappCampaign::STATUS_SCHEDULED;
        } elseif (!$campaign->exists || $campaign->status === WhatsappCampaign::STATUS_DRAFT) {
            $campaign->status = WhatsappCampaign::STATUS_DRAFT;
        }

        $campaign->save();

        Session::flash('flash_message', $request->filled('id') ? trans('words.successfully_updated') : trans('words.added'));

        return redirect('user/whatsapp/campaigns');
    }

    public function showCampaign($id)
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return $redirect;
        }

        $userId = Auth::id();
        $campaign = WhatsappCampaign::where('user_id', $userId)->with('contactList')->findOrFail($id);
        $page_title = 'Campaign Details: ' . $campaign->title;
        $sends = WhatsappCampaignSend::where('campaign_id', $campaign->id)
            ->with('contact')
            ->latest()
            ->paginate(30);

        return view('pages.user.whatsapp.show_campaign', compact('page_title', 'campaign', 'sends'));
    }

    public function launchCampaign($id)
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return $redirect;
        }

        $campaign = WhatsappCampaign::where('user_id', Auth::id())->findOrFail($id);
        $this->campaignService->launchCampaign($campaign);

        Session::flash('flash_message', 'WhatsApp Campaign launched successfully.');

        return redirect('user/whatsapp/campaigns/' . $campaign->id);
    }

    public function pauseCampaign($id)
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return $redirect;
        }

        $campaign = WhatsappCampaign::where('user_id', Auth::id())->findOrFail($id);
        $campaign->status = WhatsappCampaign::STATUS_PAUSED;
        $campaign->save();

        Session::flash('flash_message', 'WhatsApp Campaign paused.');

        return redirect()->back();
    }

    public function resumeCampaign($id)
    {
        if ($redirect = $this->ensureWhatsappAccess()) {
            return $redirect;
        }

        $campaign = WhatsappCampaign::where('user_id', Auth::id())->findOrFail($id);
        $campaign->status = WhatsappCampaign::STATUS_RUNNING;
        $campaign->save();

        $this->campaignService->processCampaignBatch($campaign, min(3, (int) $campaign->batch_size));

        Session::flash('flash_message', 'WhatsApp Campaign resumed.');

        return redirect()->back();
    }
}
