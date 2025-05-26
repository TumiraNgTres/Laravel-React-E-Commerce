<?php

namespace App\Services;

use App\Enum\OrderStatusEnum;
use App\Interface\StripeWebhookInterface;
use App\Mail\CheckoutCompleted;
use App\Mail\NewOrderMail;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;

class StripeWebhookService implements StripeWebhookInterface
{
    public function handle($event, StripeClient $stripe): void
    {
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event);
                break;

            case 'charge.updated': // give infor about how much stripe fee
                $this->handleChargeUpdated($event, $stripe);
                break;

            default:
                Log::info('Unhandled Stripe event type: ' . $event->type);
        }
    }

    protected function handleCheckoutSessionCompleted($event): void
    {
        $session = $event->data->object;
        $paymentIntent = $session['payment_intent'];

        $orders = Order::with(['orderItems.product.variations'])
            ->where('stripe_session_id', $session['id'])
            ->get();

        foreach ($orders as $order) {

            $order->payment_intent = $paymentIntent;
            $order->status = OrderStatusEnum::Paid->value;
            $order->save();

            $productIdsToDelete = [];

            foreach ($order->orderItems as $orderItem) {
                $product = $orderItem->product;
                $options = $orderItem->variation_type_option_ids;

                if (is_array($options) && !empty($options)) {
                    sort($options);
                    $variation = $product->variations()
                        ->whereJsonContains('variation_type_option_ids', $options)
                        ->first();

                    if ($variation && is_numeric($variation->quantity)) {
                        $variation->quantity -= $orderItem->quantity;
                        $variation->save();
                    }
                } elseif ($product && is_numeric($product->quantity)) {
                    $product->quantity -= $orderItem->quantity;
                    $product->save();
                }

                $productIdsToDelete[] = $orderItem->product_id;
            }

            CartItem::where('user_id', $order->user_id)
                ->whereIn('product_id', $productIdsToDelete)
                ->where('saved_for_later', false)
                ->delete();
        }
    }

    protected function handleChargeUpdated($event, StripeClient $stripe): void
    {
        $charge = $event->data->object; // contains a \Stripe\Charge
        $transactionId = $charge['balance_transaction'];
        $paymentIntentId = $charge['payment_intent'];

        $balanceTransaction = $stripe->balanceTransactions->retrieve($transactionId);

        $orders = Order::where('payment_intent', $paymentIntentId)->get();

        $totalAmount = $balanceTransaction['amount'];

        $stripeFee = collect($balanceTransaction['fee_details'])
            ->where('type', 'stripe_fee')
            ->last()['amount'] ?? 0;

        $platformFeePercent = config('services.stripe.platform_fee_pct');

        foreach ($orders as $order) {
            $vendorShare = $order->total_price / $totalAmount;

            $order->online_payment_commission = $vendorShare * $stripeFee;
            $order->website_commission = ($order->total_price - $order->online_payment_commission) * ($platformFeePercent / 100);
            $order->vendor_subtotal = $order->total_price - $order->online_payment_commission - $order->website_commission;

            $order->save();

            Mail::to($order->vendorUser)->send(new NewOrderMail($order));
        }

        Mail::to($orders[0]->user)->send(new CheckoutCompleted($orders));
    }
}
