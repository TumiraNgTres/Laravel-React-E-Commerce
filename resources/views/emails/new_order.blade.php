<x-mail::message>
<h1 style="text-align: center; font-size: 24px;">Congratulations! You have new Order.</h1>

<x-mail::button :url="$order->id">View Order Details</x-mail::button>

<h3 style="font-size: 20px;margin-bottom:15px;">Order Summary</h3>

<x-mail::table>
<table>
<tbody>
<tr>
<td>Order #</td>
<td>{{ $order->id }}</td>
</tr>
<tr>
<td>Order Date</td>
<td>{{ $order->created_at }}</td>
</tr>
<tr>
<td>Order Total</td>
<td>{{ Number::currency($order->total_price) }}</td>
</tr>
<tr>
<td>Payment Processing Fee</td>
<td>{{ Number::currency($order->online_payment_commission ?: 0) }}</td>
</tr>
<tr>
<td>Platform Fee</td>
<td>{{ Number::currency($order->website_commission ?: 0) }}</td>
</tr>
<tr>
<td>Your Earnings</td>
<td>{{ Number::currency($order->vendor_subtotal ?: 0) }}</td>
</tr>
</tbody>
</table>
</x-mail::table>

<hr>

{{-- ✅ Include reusable order item table --}}
@include('emails.components.order-items-table', ['order' => $order])

<x-mail::panel>
Thank you for having business with us.
</x-mail::panel>

Thanks,<br />
{{ config('app.name') }}
</x-mail::message>
