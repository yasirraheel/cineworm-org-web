<?php

namespace App\Http\Controllers;

use App\FilmPurchase;
use App\Movies;
use App\Services\CreatorFilmService;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class CreatorFilmController extends Controller
{
    public function __construct(private CreatorFilmService $films)
    {
    }

    public function index()
    {
        $films = Movies::where('status', 1)
            ->where('upcoming', 0)
            ->where('creator_sale_enabled', 1)
            ->where('creator_sale_status', 'approved')
            ->where('creator_price', '>', 0)
            ->orderByDesc('id')
            ->paginate(18);

        return view('pages.movies.cineflix', compact('films'));
    }

    public function show(string $slug, int $id)
    {
        $movie = $this->approvedFilm($id);
        $hasAccess = Auth::check() && $this->films->canWatch($movie, (int) Auth::id());
        $totals = $this->films->totals($movie);

        return view('pages.movies.creator', compact('movie', 'hasAccess', 'totals'));
    }

    public function checkout(int $id)
    {
        $movie = $this->approvedFilm($id);

        if ($this->films->canWatch($movie, (int) Auth::id())) {
            return redirect()->route('creator-films.watch', [
                'slug' => $movie->video_slug,
                'id' => $movie->id,
            ]);
        }

        $totals = $this->films->totals($movie);
        $paypalEnabled = (int) optional(getPaymentGatewayInfo(1))->status === 1;
        $stripeEnabled = (int) optional(getPaymentGatewayInfo(2))->status === 1
            && filled(getPaymentGatewayInfo(2, 'stripe_secret_key'));

        return view('pages.movies.creator_checkout', compact('movie', 'totals', 'paypalEnabled', 'stripeEnabled'));
    }

    public function stripePay(int $id)
    {
        $movie = $this->approvedFilm($id);
        $this->ensureNotAlreadyPaid($movie);

        $secret = getPaymentGatewayInfo(2, 'stripe_secret_key');
        if (blank($secret)) {
            return $this->paymentError($movie, 'Stripe payment is not configured yet.');
        }

        $purchase = $this->newPurchase($movie, 'stripe');
        $totals = $this->films->totals($movie);

        try {
            \Stripe\Stripe::setApiKey($secret);
            $session = \Stripe\Checkout\Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($totals['currency']),
                        'product_data' => ['name' => stripslashes($movie->video_title)],
                        'unit_amount' => (int) round($totals['total_amount'] * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'metadata' => ['purchase_id' => (string) $purchase->id],
                'success_url' => route('creator-films.stripe.success') . '?purchase_id=' . $purchase->id . '&session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('creator-films.checkout', $movie->id),
            ]);

            $purchase->provider_order_id = $session->id;
            $purchase->save();

            return Redirect::away($session->url);
        } catch (\Throwable $e) {
            $this->films->markFailed($purchase, $e->getMessage());
            Log::warning('Creator film Stripe payment initiation failed', ['error' => $e->getMessage()]);
            return $this->paymentError($movie, 'Unable to start payment. Please try again.');
        }
    }

    public function stripeSuccess(Request $request)
    {
        $purchase = $this->ownedPurchase($request->integer('purchase_id'), 'stripe');
        if (!$purchase || blank($request->string('session_id')->toString())) {
            return redirect()->route('movies')->with('error_flash_message', 'Payment could not be verified.');
        }

        try {
            \Stripe\Stripe::setApiKey(getPaymentGatewayInfo(2, 'stripe_secret_key'));
            $session = \Stripe\Checkout\Session::retrieve($request->string('session_id')->toString());
            $paid = $session->status === 'complete'
                && ($session->payment_status ?? null) === 'paid'
                && (string) ($session->metadata->purchase_id ?? '') === (string) $purchase->id
                && (string) $purchase->provider_order_id === (string) $session->id;

            if (!$paid) {
                $this->films->markFailed($purchase, 'Stripe session was not completed or did not match the purchase.');
                return redirect()->route('creator-films.checkout', $purchase->movie_id)
                    ->with('error_flash_message', 'Payment could not be verified.');
            }

            $this->films->markPaid($purchase, (string) $session->payment_intent, ['stripe_session_id' => $session->id]);
            $movie = $purchase->movie;
            return redirect()->route('creator-films.watch', ['slug' => $movie->video_slug, 'id' => $movie->id])
                ->with('flash_message', 'Payment confirmed. Your film is ready to watch.');
        } catch (\Throwable $e) {
            $this->films->markFailed($purchase, $e->getMessage());
            Log::warning('Creator film Stripe payment verification failed', ['error' => $e->getMessage()]);
            return redirect()->route('creator-films.checkout', $purchase->movie_id)
                ->with('error_flash_message', 'Payment could not be verified.');
        }
    }

    public function paypalPay(int $id)
    {
        $movie = $this->approvedFilm($id);
        $this->ensureNotAlreadyPaid($movie);

        $clientId = getPaymentGatewayInfo(1, 'paypal_client_id');
        $secret = getPaymentGatewayInfo(1, 'paypal_secret');
        $mode = getPaymentGatewayInfo(1, 'mode');
        if (blank($clientId) || blank($secret)) {
            return $this->paymentError($movie, 'PayPal payment is not configured yet.');
        }

        $purchase = $this->newPurchase($movie, 'paypal');
        $totals = $this->films->totals($movie);
        $config = [
            'mode' => $mode ?: 'sandbox',
            'sandbox' => ['client_id' => $clientId, 'client_secret' => $secret, 'app_id' => ''],
            'live' => ['client_id' => $clientId, 'client_secret' => $secret, 'app_id' => ''],
            'payment_action' => 'Sale',
            'currency' => $totals['currency'],
            'notify_url' => '',
            'locale' => 'en_US',
            'validate_ssl' => true,
        ];

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials($config);
            $provider->getAccessToken();
            $response = $provider->createOrder([
                'intent' => 'CAPTURE',
                'application_context' => [
                    'return_url' => route('creator-films.paypal.success', ['purchase_id' => $purchase->id]),
                    'cancel_url' => route('creator-films.paypal.fail', ['purchase_id' => $purchase->id]),
                ],
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => $totals['currency'],
                        'value' => number_format($totals['total_amount'], 2, '.', ''),
                    ],
                    'description' => stripslashes($movie->video_title),
                ]],
            ]);

            if (empty($response['id'])) {
                throw new \RuntimeException('PayPal did not return an order id.');
            }

            $purchase->provider_order_id = $response['id'];
            $purchase->save();
            foreach (($response['links'] ?? []) as $link) {
                if (($link['rel'] ?? '') === 'approve' && !empty($link['href'])) {
                    return Redirect::away($link['href']);
                }
            }
            throw new \RuntimeException('PayPal approval link was missing.');
        } catch (\Throwable $e) {
            $this->films->markFailed($purchase, $e->getMessage());
            Log::warning('Creator film PayPal payment initiation failed', ['error' => $e->getMessage()]);
            return $this->paymentError($movie, 'Unable to start payment. Please try again.');
        }
    }

    public function paypalSuccess(Request $request)
    {
        $purchase = $this->ownedPurchase($request->integer('purchase_id'), 'paypal');
        $token = $request->string('token')->toString();
        if (!$purchase || blank($token) || $purchase->provider_order_id !== $token) {
            return redirect()->route('movies')->with('error_flash_message', 'Payment could not be verified.');
        }

        try {
            $clientId = getPaymentGatewayInfo(1, 'paypal_client_id');
            $secret = getPaymentGatewayInfo(1, 'paypal_secret');
            $mode = getPaymentGatewayInfo(1, 'mode');
            $config = [
                'mode' => $mode ?: 'sandbox',
                'sandbox' => ['client_id' => $clientId, 'client_secret' => $secret, 'app_id' => ''],
                'live' => ['client_id' => $clientId, 'client_secret' => $secret, 'app_id' => ''],
                'payment_action' => 'Sale', 'currency' => $purchase->currency,
                'notify_url' => '', 'locale' => 'en_US', 'validate_ssl' => true,
            ];
            $provider = new PayPalClient;
            $provider->setApiCredentials($config);
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($token);
            $capture = $response['purchase_units'][0]['payments']['captures'][0] ?? [];
            if (($response['status'] ?? '') !== 'COMPLETED' || ($capture['status'] ?? '') !== 'COMPLETED') {
                throw new \RuntimeException('PayPal capture was not completed.');
            }

            $this->films->markPaid($purchase, $capture['id'] ?? null, ['paypal_order_id' => $token]);
            $movie = $purchase->movie;
            return redirect()->route('creator-films.watch', ['slug' => $movie->video_slug, 'id' => $movie->id])
                ->with('flash_message', 'Payment confirmed. Your film is ready to watch.');
        } catch (\Throwable $e) {
            $this->films->markFailed($purchase, $e->getMessage());
            Log::warning('Creator film PayPal payment verification failed', ['error' => $e->getMessage()]);
            return redirect()->route('creator-films.checkout', $purchase->movie_id)
                ->with('error_flash_message', 'Payment could not be verified.');
        }
    }

    public function paypalFail(Request $request)
    {
        $purchase = $this->ownedPurchase($request->integer('purchase_id'), 'paypal');
        if ($purchase) {
            $this->films->markFailed($purchase, 'PayPal payment was cancelled.');
            return redirect()->route('creator-films.checkout', $purchase->movie_id)
                ->with('error_flash_message', 'Payment was cancelled.');
        }

        return redirect()->route('movies');
    }

    public function watch(string $slug, int $id)
    {
        $movie = $this->approvedFilm($id);
        if (!$this->films->canWatch($movie, (int) Auth::id())) {
            return redirect()->route('creator-films.show', ['slug' => $movie->video_slug, 'id' => $movie->id])
                ->with('error_flash_message', 'Purchase this film before watching it.');
        }

        return view('pages.movies.creator_watch', compact('movie'));
    }

    public function stream(int $id, Request $request)
    {
        $movie = $this->approvedFilm($id);
        abort_unless($this->films->canWatch($movie, (int) Auth::id()), 403, 'Purchase required.');
        return $this->films->streamFromDrive($movie, $request);
    }

    private function approvedFilm(int $id): Movies
    {
        $movie = Movies::where('status', 1)->whereKey($id)->firstOrFail();
        abort_unless($movie->isCreatorPaidFilm(), 404);
        return $movie;
    }

    private function newPurchase(Movies $movie, string $gateway): FilmPurchase
    {
        return $this->films->createPurchase($movie, (int) Auth::id(), $gateway);
    }

    private function ownedPurchase(int $id, string $gateway): ?FilmPurchase
    {
        return FilmPurchase::whereKey($id)
            ->where('buyer_id', Auth::id())
            ->where('gateway', $gateway)
            ->with('movie')
            ->first();
    }

    private function ensureNotAlreadyPaid(Movies $movie): void
    {
        if ($this->films->canWatch($movie, (int) Auth::id())) {
            abort(redirect()->route('creator-films.watch', ['slug' => $movie->video_slug, 'id' => $movie->id]));
        }
    }

    private function paymentError(Movies $movie, string $message)
    {
        return redirect()->route('creator-films.checkout', $movie->id)
            ->with('error_flash_message', $message);
    }
}
