<?php

namespace App\Http\Controllers;

use App\Enum\OrderStatusEnum;
use App\Http\Resources\OrderViewResource;
use App\Models\CartItem;
use App\Models\Order;
use App\Services\StripeWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Exception\UnexpectedValueException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeController extends Controller
{
    protected $stripeWebhookService;
    public function __construct(StripeWebhookService $stripeWebhookService)
    {
        $this->stripeWebhookService = $stripeWebhookService;
    }
    public function success(Request $request)
    {
        $user = auth()->user();
        $sessionId = $request->get('session_id');

        $orders = Order::with(['orderItems.product', 'vendorUser'])
            ->where('user_id', $user->id)
            ->where('stripe_session_id', $sessionId)
            ->get();

        if ($orders->isEmpty()) {
            abort(404);
        }

        return Inertia::render('Stripe/Success', [
            'orders' => OrderViewResource::collection($orders)->resolve(),
        ]);
    }
    public function failure()
    {
        return Inertia::render('Stripe/Failure');
    }
    public function webhook(Request $request)
    {
        $stripe = new StripeClient(config('services.stripe.secret_key'));
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Invalid payload: ' . $e->getMessage());
            return response('Invalid Payload', 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Invalid signature: ' . $e->getMessage());
            return response('Invalid Signature', 400);
        }

        $this->stripeWebhookService->handle($event, $stripe);

        return response('', 200);
    }
}
