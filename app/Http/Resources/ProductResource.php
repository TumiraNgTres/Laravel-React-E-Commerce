<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //  to avoid product image change glitch when variant image selected when refresh
        // because from here before send all images like below seperate and in view page they change that using to take options.
        //  but now based on the selected options, that image will pass - otherwise prdouct image
        // so product change glitch avoids
        $options = $request->input('options') ?: [];

        if ($options) {
            $images = $this->getImagesForOptions($options);
        } else {
            $images = $this->getImages();
        }

        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'meta_title'     => $this->meta_title,
            'meta_description' => $this->meta_description,
            'price'          => number_format($this->price, 2),
            'quantity'       => $this->quantity,
            'image'          => $this->getFirstMediaUrl('images'),
            'images'         => $images->map(function ($image) {
                return [
                    'id'    => $image->id,
                    'thumb' => $image->getUrl('thumb'),
                    'small' => $image->getUrl('small'),
                    'large' => $image->getUrl('large'),
                ];
            }),
            'user'           => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
                'store_name' => $this->user->vendor->store_name,
            ],
            'department'     => [
                'id'   => $this->department->id,
                'name' => $this->department->name,
                'slug' => $this->department->slug,
            ],
            'category'       => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ],
            // product variation types added to that product
            'variationTypes' => $this->variationTypes->map(function ($variationType) {
                return [
                    'id'      => $variationType->id,
                    'name'    => $variationType->name,
                    'type'    => $variationType->type,
                    'options' => $variationType->options->map(function ($option) {
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
                        ];
                    }),
                ];
            }),

            // product vriations combinations
            'variations'     => $this->variations->map(function ($variation) {
                return [
                    'id'                        => $variation->id,
                    'variation_type_option_ids' => $variation->variation_type_option_ids,
                    'quantity'                  => $variation->quantity,
                    'price'                     => number_format($variation->price, 2),
                ];
            })
        ];
    }
}
