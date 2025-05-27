<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'slug'       => $this->slug,
            // 'price'      => number_format($this->price, 2),
            'price'      => number_format($this->getPriceForFirstOptions(), 2),
            'quantity'   => $this->quantity,
            'image'      => $this->getFirstImageUrl(),
            'user'       => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
                'store_name' => $this->user->vendor->store_name
            ],
            'department' => [
                'id'   => $this->department->id,
                'name' => $this->department->name,
                'slug' => $this->department->slug,
            ],
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],
            // --- VARIATION DATA ADDED ---
            'variationTypes' => $this->variationTypes->map(function ($variationType) {
                return [
                    'id'      => $variationType->id,
                    'name'    => $variationType->name,
                    'type'    => $variationType->type,
                    'options' => $variationType->options->map(function ($option) use ($variationType) {
                        return [
                            'id'     => $option->id,
                            'name'   => $option->name,
                            'images' => $option->getMedia('images')->map(function ($image) {
                                return [
                                    'id'    => $image->id,
                                    'thumb' => $image->getUrl('thumb'),
                                    'small' => $image->getUrl('small'),
                                    'large' => $image->getUrl('large'),
                                ];
                            }),
                            'type' => [
                                'id'   => $variationType->id,
                                'name' => $variationType->name,
                                'type' => $variationType->type,
                            ],
                        ];
                    }),
                ];
            }),
            'variations' => $this->variations->map(function ($variation) {
                return [
                    'id'                         => $variation->id,
                    'variation_type_option_ids' => $variation->variation_type_option_ids,
                    'quantity'                   => $variation->quantity,
                    'price'                      => number_format($variation->price, 2),
                ];
            }),
        ];
    }
}
