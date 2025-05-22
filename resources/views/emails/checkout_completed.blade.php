<x-mail::message>
<h1 style="text-align: center; font-size: 24px;">Payment was Completed Successfully!</h1>

@foreach ($orders as $order)
<x-mail::table>
<table>
<tbody>
<tr>
<td>Seller</td>
<td>
<a href="{{ url('/') }}">{{ $order->vendorUser->vendor->store_name }}</a>
</td>
</tr>
<tr>
<td>Order #</td>
<td>{{ $order->id }}</td>
</tr>
<tr>
<td>Items</td>
<td>{{ $order->orderItems->count() }}</td>
</tr>
<tr>
<td>Total</td>
<td>{{ Number::currency($order->total_price) }}</td>
</tr>
</tbody>
</table>
</x-mail::table>

{{-- ✅ Include reusable order item table --}}
@include('emails.components.order-items-table', ['order' => $order])

<x-mail::button :url="$order->id">View Order Details</x-mail::button>
@endforeach

<x-mail::subcopy>
If you have any questions about this order, feel free to contact our support team.

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore
magna aliqua.
Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
</x-mail::subcopy>

<x-mail::panel>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore.
</x-mail::panel>

Thanks,<br />
{{ config('app.name') }}
</x-mail::message>
