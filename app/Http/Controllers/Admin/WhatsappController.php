<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsappServerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class WhatsappController extends Controller
{
    public function index()
    {
        app(WhatsappServerService::class)->ensureRunning();

        $page_title = 'WhatsApp Web';
        $status = $this->requestServer('get', '/status');

        if (in_array($status['status'] ?? 'unavailable', ['disconnected', 'logged_out'], true)) {
            $status = $this->requestServer('post', '/connect');
        }

        return view('admin.pages.whatsapp.index', compact('page_title', 'status'));
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
}
