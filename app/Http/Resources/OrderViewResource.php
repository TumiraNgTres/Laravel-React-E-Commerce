<?php

namespace App\Http\Resources;

use App\Models\VariationTypeOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderViewResource extends JsonResource
{
    // /**
    //  * Transform the resource into an array.
    //  *
    //  * @return array<string, mixed>
    //  */
    public function toArray($request)
    {
        $orderItems = $this->orderItems;

        // Collect all unique variation_type_option_ids from all order items
        $allOptionIds = $orderItems
            ->pluck('variation_type_option_ids')
            ->flatten()
            ->unique()
            ->filter()
            ->values()
            ->all();

        // Preload all variation options with media to avoid queries inside the loop
        $preloadedVariationOptions = VariationTypeOption::with('media')
            ->whereIn('id', $allOptionIds)
            ->get();

        return [
            'id' => $this->id,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'vendorUser' => new VendorUserResource($this->vendorUser),
            'orderItems' => $orderItems->map(function ($item) use ($preloadedVariationOptions) {
                return [
                    'id' => $item->id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'variation_type_option_ids' => $item->variation_type_option_ids,
                    'product' => [
                        'id' => $item->product->id,
                        'title' => $item->product->title,
                        'slug' => $item->product->slug,
                        'description' => $item->product->description,
                        'image' => $item->product->getImageForOptions(
                            $item->variation_type_option_ids ?: [],
                            $preloadedVariationOptions
                        ),
                    ],
                ];
            }),
        ];
    }
}
