<x-mail::table>
    <table>
        <thead>
            <tr>
                <th>SI No.</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $orderItem)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <table>
                            <tbody>
                                <tr>
                                    <td style="padding: 5px;">
                                        <img src="{{ $orderItem->product->getImageForOptions($orderItem->variation_type_option_ids) }}"
                                            alt="" style="min-width: 60px; max-width: 60px;">
                                    </td>
                                    <td style="font-size: 13px; padding: 5px;">
                                        {{ $orderItem->product->title }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td>{{ $orderItem->quantity }}</td>
                    <td>{{ Number::currency($orderItem->price) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-mail::table>
