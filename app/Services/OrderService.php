<?php

namespace App\Services;

use App\Enum\OrderStatusEnum;
use App\Interface\OrderInterface;
use App\Models\Order;
use App\Models\OrderItem;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class OrderService implements OrderInterface
{
    /**
     * @param \App\Models\User $user
     * @param array<int, array{
     *     user: \App\Models\User,
     *     items: array<int, array{
     *         product_id: int,
     *         quantity: int,
     *         price: float,
     *         variation_type_option_ids: array<int>,
     *         title: string,
     *         images: string,
     *         options: array<int, array{
     *             name: string,
     *             type: array{name: string}
     *         }>
     *     }>,
     *     totalPrice: float
     * }> $cartItems
     * @param int|null $vendorId
     *
     * @return array{orders: array<\App\Models\Order>, session: \Stripe\Checkout\Session}
     *
     * @throws \Stripe\Exception\ApiErrorException
     */

    public function createOrdersAndStripeSession(object $user, array $cartItems, ?int $vendorId = null): array
    {
        Stripe::setApiKey(config('services.stripe.secret_key'));

        $orders = [];
        $lineItems = [];

        foreach ($cartItems as $item) {
            $vendorUser = $item['user'];
            $cartItems = $item['items'];

            // Order creation
            $order = Order::create([
                'stripe_session_id' => null,
                'user_id' => $user->id,
                'vendor_user_id' => $vendorUser['id'],
                'total_price' => $item['totalPrice'],
                'status' => OrderStatusEnum::Draft->value,
                'payment_method' => 'stripe',
            ]);

            $orders[] = $order;

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem['product_id'],
                    'quantity' => $cartItem['quantity'],
                    'price' => $cartItem['price'],
                    'variation_type_option_ids' => $cartItem['option_ids'],
                ]);

                // option datas convert to collections from array

                // creates a single description string by combining each option’s type and name, separated by commas.
                // eg: "Color: Red,Size: Large,Material: Cotton"
                $description = collect($cartItem['options'])->map(function ($item) {
                    return "{$item['type']['name']}: {$item['name']}";
                })->implode(',');

                // for stripe checkout - each item created to lineitem
                $lineItem = [
                    'price_data' => [
                        'currency' => config('app.currency'),
                        'product_data' => [
                            'name' => $cartItem['title'],
                            'images' => [$cartItem['image']],
                        ],
                        'unit_amount' => $cartItem['price'] * 100 // convert to cents
                    ],
                    'quantity' => $cartItem['quantity'],
                ];

                if ($description) {
                    $lineItem['price_data']['product_data']['description'] = $description;
                }

                $lineItems[] = $lineItem;
            }
        }

        // creating stripe session from checkout
        $session = Session::create([
            'customer_email' => $user->email,
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('stripe.success', []) . "?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => route('stripe.failure', []),
        ]);

        foreach ($orders as $order) {
            $order->update(['stripe_session_id' => $session->id]);
        }

        return [
            'orders' => $orders,
            'session' => $session
        ];
    }
}
