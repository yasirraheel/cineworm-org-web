<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsappCampaignService;
use App\Services\WhatsappServerService;
use App\WhatsappCampaign;
use App\WhatsappCampaignSend;
use App\WhatsappContact;
use App\WhatsappContactList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class WhatsappController extends Controller
{
    protected $campaignService;

    public function __construct()
    {
        $this->campaignService = new WhatsappCampaignService();
    }

    public function index()
    {
        app(WhatsappServerService::class)->ensureRunning();

        $page_title = 'WhatsApp Web Dashboard';
        $status = $this->requestServer('get', '/status');

        if (in_array($status['status'] ?? 'unavailable', ['disconnected', 'logged_out'], true)) {
            $status = $this->requestServer('post', '/connect');
        }

        $listsCount = WhatsappContactList::count();
        $contactsCount = WhatsappContact::count();
        $campaignsCount = WhatsappCampaign::count();
        $runningCampaignsCount = WhatsappCampaign::where('status', WhatsappCampaign::STATUS_RUNNING)->count();
        $campaigns = WhatsappCampaign::with('contactList')->latest()->limit(8)->get();
        $recentSends = WhatsappCampaignSend::with(['campaign', 'contact'])->latest()->limit(10)->get();

        return view('admin.pages.whatsapp.index', compact(
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

    public function lists()
    {
        $page_title = 'WhatsApp Contact Lists';
        $lists = WhatsappContactList::withCount('contacts')->latest()->paginate(15);

        return view('admin.pages.whatsapp.lists', compact('page_title', 'lists'));
    }

    public function saveList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'nullable|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $list = $request->filled('id') ? WhatsappContactList::findOrFail($request->id) : new WhatsappContactList();
        $list->user_id = Auth::id();
        $list->name = trim($request->name);
        $list->description = $request->description;
        $list->status = (int) $request->input('status', 1);
        $list->save();

        Session::flash('flash_message', $request->filled('id') ? trans('words.successfully_updated') : trans('words.added'));

        return redirect('admin/whatsapp/lists');
    }

    public function contacts($listId)
    {
        $page_title = 'WhatsApp Contacts';
        $list = WhatsappContactList::findOrFail($listId);
        $contacts = WhatsappContact::where('contact_list_id', $list->id)->latest()->paginate(25);

        return view('admin.pages.whatsapp.contacts', compact('page_title', 'list', 'contacts'));
    }

    public function saveContact(Request $request, $listId)
    {
        $list = WhatsappContactList::findOrFail($listId);

        $validator = Validator::make($request->all(), [
            'phone' => 'required|max:32',
            'name' => 'nullable|max:255',
            'company' => 'nullable|max:255',
            'tags' => 'nullable|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        $phone = $this->normalizePhone($request->phone);
        if (!$this->isValidPhone($phone)) {
            Session::flash('error_flash_message', 'Please enter a valid phone number with country code.');
            return redirect()->back()->withInput();
        }

        $contact = WhatsappContact::updateOrCreate(
            ['contact_list_id' => $list->id, 'phone' => $phone],
            [
                'name' => trim((string) $request->name),
                'company' => $request->company,
                'tags' => $request->tags,
                'status' => (int) $request->input('status', 1),
                'opt_out_at' => $request->boolean('opt_out') ? now() : null,
            ]
        );

        Session::flash('flash_message', $contact->wasRecentlyCreated ? trans('words.added') : trans('words.successfully_updated'));

        return redirect()->back();
    }

    public function importContacts(Request $request, $listId)
    {
        $list = WhatsappContactList::findOrFail($listId);
        $lines = [];

        if ($request->hasFile('csv_file')) {
            $lines = file($request->file('csv_file')->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        } elseif ($request->filled('import_source')) {
            $lines = preg_split("/\r\n|\n|\r/", trim($request->import_source));
        }

        if (empty($lines)) {
            Session::flash('error_flash_message', 'Please upload a CSV file or paste contact rows.');
            return redirect()->back();
        }

        $imported = 0;
        $skipped = 0;

        foreach ($lines as $index => $line) {
            $columns = str_getcsv($line);
            $firstColumn = strtolower(trim($columns[0] ?? ''));

            if ($index === 0 && in_array($firstColumn, ['phone', 'mobile', 'number', 'name'], true)) {
                continue;
            }

            $phone = isset($columns[1]) ? $this->normalizePhone($columns[1]) : $this->normalizePhone($columns[0] ?? '');
            $name = isset($columns[1]) ? trim($columns[0]) : '';
            $company = trim($columns[2] ?? '');
            $tags = trim($columns[3] ?? '');

            if (!$this->isValidPhone($phone)) {
                $skipped++;
                continue;
            }

            WhatsappContact::updateOrCreate(
                ['contact_list_id' => $list->id, 'phone' => $phone],
                [
                    'name' => $name,
                    'company' => $company,
                    'tags' => $tags,
                    'status' => 1,
                    'opt_out_at' => null,
                ]
            );

            $imported++;
        }

        Session::flash('flash_message', $imported . ' WhatsApp contacts imported. ' . $skipped . ' rows skipped.');

        return redirect()->back();
    }

    public function downloadSampleContactsFile($listId)
    {
        $list = WhatsappContactList::findOrFail($listId);
        $filename = 'sample_whatsapp_contacts_list_' . $list->id . '.csv';
        $rows = [
            ['name', 'phone', 'company', 'tags'],
            ['Ali Khan', '923001234567', 'Cineworm', 'vip'],
            ['Sara Ahmed', '923211234567', 'Partner Studio', 'partner,press'],
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    public function deleteContact($listId, $contactId)
    {
        WhatsappContact::where('contact_list_id', $listId)->where('id', $contactId)->delete();
        Session::flash('flash_message', trans('words.deleted'));

        return redirect()->back();
    }

    public function campaigns()
    {
        $page_title = 'WhatsApp Campaigns';
        $campaigns = WhatsappCampaign::with('contactList')->latest()->paginate(15);

        return view('admin.pages.whatsapp.campaigns', compact('page_title', 'campaigns'));
    }

    public function campaignForm($id = null)
    {
        $page_title = $id ? 'Edit WhatsApp Campaign' : 'Create WhatsApp Campaign';
        $campaign = $id ? WhatsappCampaign::findOrFail($id) : null;
        $lists = WhatsappContactList::withCount(['contacts' => function ($query) {
            $query->where('status', 1)->whereNull('opt_out_at');
        }])->where('status', 1)->orderBy('name')->get();

        return view('admin.pages.whatsapp.campaign_form', compact('page_title', 'campaign', 'lists'));
    }

    public function saveCampaign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'contact_list_id' => 'required|integer',
            'message' => 'required|max:5000',
            'scheduled_at' => 'nullable|date',
            'batch_size' => 'required|integer|min:1|max:50',
            'min_delay_seconds' => 'required|integer|min:5|max:3600',
            'max_delay_seconds' => 'required|integer|min:5|max:3600',
            'pause_after_messages' => 'required|integer|min:1|max:500',
            'pause_duration_seconds' => 'required|integer|min:60|max:86400',
            'daily_limit' => 'required|integer|min:1|max:5000',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        if ((int) $request->min_delay_seconds > (int) $request->max_delay_seconds) {
            Session::flash('error_flash_message', 'Minimum delay cannot be greater than maximum delay.');
            return redirect()->back()->withInput();
        }

        $list = WhatsappContactList::findOrFail($request->contact_list_id);
        $campaign = $request->filled('id') ? WhatsappCampaign::findOrFail($request->id) : new WhatsappCampaign();
        $messageChanged = $campaign->exists && $campaign->message !== $request->message;
        $listChanged = $campaign->exists && (int) $campaign->contact_list_id !== (int) $list->id;

        $campaign->user_id = Auth::id();
        $campaign->contact_list_id = $list->id;
        $campaign->name = trim($request->name);
        $campaign->message = trim($request->message);
        $campaign->scheduled_at = $request->scheduled_at ? date('Y-m-d H:i:s', strtotime($request->scheduled_at)) : null;
        $campaign->batch_size = (int) $request->batch_size;
        $campaign->min_delay_seconds = (int) $request->min_delay_seconds;
        $campaign->max_delay_seconds = (int) $request->max_delay_seconds;
        $campaign->pause_after_messages = (int) $request->pause_after_messages;
        $campaign->pause_duration_seconds = (int) $request->pause_duration_seconds;
        $campaign->daily_limit = (int) $request->daily_limit;
        $campaign->quiet_hours_start = $request->quiet_hours_start;
        $campaign->quiet_hours_end = $request->quiet_hours_end;
        $campaign->status = WhatsappCampaign::STATUS_DRAFT;
        $campaign->started_at = null;
        $campaign->completed_at = null;
        $campaign->total_contacts = 0;
        $campaign->processed_contacts = 0;
        $campaign->success_count = 0;
        $campaign->failed_count = 0;
        $campaign->skipped_count = 0;
        $campaign->last_error = null;
        $campaign->save();

        if ($messageChanged || $listChanged || $request->filled('id')) {
            WhatsappCampaignSend::where('campaign_id', $campaign->id)->delete();
        }

        Session::flash('flash_message', $request->filled('id') ? trans('words.successfully_updated') : trans('words.added'));

        return redirect('admin/whatsapp/campaigns/' . $campaign->id);
    }

    public function showCampaign($id)
    {
        $page_title = 'WhatsApp Campaign Details';
        $campaign = WhatsappCampaign::with('contactList')->findOrFail($id);
        $recentSends = WhatsappCampaignSend::with('contact')
            ->where('campaign_id', $campaign->id)
            ->latest()
            ->paginate(25);

        return view('admin.pages.whatsapp.campaign_show', compact('page_title', 'campaign', 'recentSends'));
    }

    public function launchCampaign($id)
    {
        $campaign = WhatsappCampaign::findOrFail($id);
        $contactsCount = WhatsappContact::where('contact_list_id', $campaign->contact_list_id)
            ->where('status', 1)
            ->whereNull('opt_out_at')
            ->count();

        if ($contactsCount === 0) {
            Session::flash('error_flash_message', 'This campaign cannot start because the selected list has no active WhatsApp contacts.');
            return redirect('admin/whatsapp/campaigns/' . $campaign->id);
        }

        $this->campaignService->launchCampaign($campaign);

        Session::flash('flash_message', $campaign->scheduled_at && strtotime($campaign->scheduled_at) > time()
            ? 'WhatsApp campaign scheduled successfully.'
            : 'WhatsApp campaign started successfully.');

        return redirect('admin/whatsapp/campaigns/' . $campaign->id);
    }

    public function pauseCampaign($id)
    {
        $campaign = WhatsappCampaign::findOrFail($id);
        $campaign->status = WhatsappCampaign::STATUS_PAUSED;
        $campaign->save();

        Session::flash('flash_message', 'WhatsApp campaign paused successfully.');

        return redirect('admin/whatsapp/campaigns/' . $campaign->id);
    }

    public function resumeCampaign($id)
    {
        $campaign = WhatsappCampaign::findOrFail($id);
        $campaign->status = WhatsappCampaign::STATUS_RUNNING;
        $campaign->started_at = $campaign->started_at ?: now();
        $campaign->save();
        $this->campaignService->processCampaignBatch($campaign->fresh(), min(3, (int) $campaign->batch_size));

        Session::flash('flash_message', 'WhatsApp campaign resumed successfully.');

        return redirect('admin/whatsapp/campaigns/' . $campaign->id);
    }

    public function processCampaign($id)
    {
        $campaign = WhatsappCampaign::findOrFail($id);
        $this->campaignService->processCampaignBatch($campaign, min(5, (int) $campaign->batch_size));

        Session::flash('flash_message', 'WhatsApp campaign batch processed.');

        return redirect('admin/whatsapp/campaigns/' . $campaign->id);
    }

    public function connect()
    {
        $response = $this->requestServer('post', '/connect');

        if (!($response['ok'] ?? false)) {
            Session::flash('error_flash_message', $response['error'] ?? 'Unable to start WhatsApp server connection.');
        } else {
            Session::flash('flash_message', 'WhatsApp connection started. Scan the QR code when it appears.');
        }

        return redirect('admin/whatsapp');
    }

    public function status()
    {
        app(WhatsappServerService::class)->ensureRunning();

        return response()->json($this->requestServer('get', '/status'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'number' => 'required|string|max:30',
            'message' => 'required|string|max:5000',
        ]);

        $response = $this->requestServer('post', '/send', [
            'number' => $request->input('number'),
            'message' => $request->input('message'),
            'validateNumber' => true,
            'typingPresence' => true,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($response, ($response['ok'] ?? false) ? 200 : 422);
        }

        if (!($response['ok'] ?? false)) {
            Session::flash('error_flash_message', $response['error'] ?? 'Message could not be sent.');
        } else {
            Session::flash('flash_message', 'WhatsApp message sent successfully.');
        }

        return redirect('admin/whatsapp');
    }

    public function logout()
    {
        $response = $this->requestServer('post', '/logout');

        if (!($response['ok'] ?? false)) {
            Session::flash('error_flash_message', $response['error'] ?? 'Unable to logout WhatsApp session.');
        } else {
            Session::flash('flash_message', 'WhatsApp session logged out.');
        }

        return redirect('admin/whatsapp');
    }

    private function requestServer($method, $path, array $payload = [])
    {
        try {
            $url = rtrim(config('whatsapp.server_url'), '/') . $path;
            $client = Http::timeout((int) config('whatsapp.timeout'))
                ->acceptJson()
                ->withHeaders([
                    'x-api-key' => config('whatsapp.api_key'),
                ]);

            $response = $method === 'post'
                ? $client->post($url, $payload)
                : $client->get($url);

            if (!$response->successful()) {
                return [
                    'ok' => false,
                    'status' => 'unavailable',
                    'error' => $response->json('error') ?: 'WhatsApp server returned HTTP ' . $response->status(),
                ];
            }

            return $response->json() ?: ['ok' => false, 'status' => 'unavailable'];
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'status' => 'unavailable',
                'error' => $exception->getMessage(),
            ];
        }
    }

    private function normalizePhone($value): string
    {
        return ltrim(preg_replace('/[^\d]/', '', (string) $value), '0');
    }

    private function isValidPhone($phone): bool
    {
        return preg_match('/^[1-9][0-9]{7,15}$/', (string) $phone) === 1;
    }
}
