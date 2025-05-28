<?php

namespace App\Models;

use App\Enum\ProductStatusEnum;
use App\Enum\VendorStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    // make different version of each image uploading
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100);

        $this->addMediaConversion('small')
            ->width(480);

        $this->addMediaConversion('large')
            ->width(1200);
    }

    // ------------ relationships -------------------------
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all of the variationTypes for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variationTypes(): HasMany
    {
        return $this->hasMany(VariationType::class);
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'product_id');
    }

    //  will give all the variation type options of that product
    public function options(): HasManyThrough
    {
        return $this->hasManyThrough(
            VariationTypeOption::class, //target model
            VariationType::class, // intermediate model
            'product_id', //foreign key on VariationType mdel
            'variation_type_id', //foriegn key on options table
            'id', // local key on Product table
            'id' // Local key on VariationTypeOption table
        );
    }

    // ---------------- Scope --------------------------------------------

    public function scopeForVendor(Builder $query): Builder
    {
        return $query->where('created_by', auth()->user()->id);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('products.status', ProductStatusEnum::Published);
    }

    public function scopeForWebsite(Builder $query): Builder
    {
        return $query->published()->vendorApproved();
    }

    public function scopeVendorApproved(Builder $query)
    {
        return $query
            ->join('vendors', 'vendors.user_id', '=', 'products.created_by')
            ->where('vendors.status', VendorStatusEnum::Approved->value);
    }

    public function scopeSearchKeyword(Builder $query, $keyword)
    {
        return $query->when($keyword, function ($query, $keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('description', 'LIKE', "%{$keyword}%");
            });
        });
    }


    // ------------------------- Other functions --------------------------------
    public function getPriceForOptions($optionIds = [])
    {
        /*
        option id will be like thsi associative array
         [
            1 => 11, // Color => Red
            2 => 21  // Size => Small
           ]

           Converts the associative array to a simple indexed array of just the option IDs.

           $optionIds = [11, 21]; - using array_values
        */
        //  change to only take the option ids . not key - type id
        $optionIds = array_values($optionIds);
        sort($optionIds);

        foreach ($this->variations as $variation) {
            $variation_type_option_ids = $variation->variation_type_option_ids;

            if ($optionIds == $variation_type_option_ids) {
                return $variation->price !== null ? $variation->price : $this->price;
            }
        }

        return $this->price;
    }

    // In Product model
    public function getImageForOptions(array $optionIds = null, $preloadedVariationOptions = null)
    {
        if ($optionIds) {
            $optionIds = array_values($optionIds);
            sort($optionIds);

            if ($preloadedVariationOptions) {
                // Filter preloaded options by these IDs
                $options = $preloadedVariationOptions->whereIn('id', $optionIds);
            } else {
                $options = VariationTypeOption::whereIn('id', $optionIds)->get();
            }

            foreach ($options as $option) {
                $image = $option->getFirstMediaUrl('images', 'small');
                if ($image) {
                    return $image;
                }
            }
        }

        return $this->getFirstMediaUrl('images', 'small');
    }

    // get first variation combination price
    public function getPriceForFirstOptions(): float
    {
        $firstOptions = $this->getFirstOptionsMap();

        if ($firstOptions) {
            return $this->getPriceForOptions($firstOptions);
        }
        return $this->price;
    }

    public function getFirstImageUrl($collectionName = 'images', $conversion = 'small'): string
    {
        if ($this->options->count() > 0) {
            foreach ($this->options as $option) {
                $imageUrl = $option->getFirstMediaUrl($collectionName, $conversion);

                if ($imageUrl) {
                    return $imageUrl;
                }
            }
        }
        return $this->getFirstMediaUrl($collectionName, $conversion);
    }

    public function getFirstOptionsMap(): array
    {
        return $this->variationTypes
            ->mapWithKeys(fn($type) => [$type->id => $type->options[0]?->id])
            ->toArray();

        /* [
            1 => 11, // Color => Red
            2 => 21  // Size => Small
           ]
        */
    }

    public function getImages(): MediaCollection
    {
        if ($this->options->count() > 0) {
            foreach ($this->options as $option) {
                /** @var VariationTypeOption $option */
                $images = $option->getMedia('images');
                if ($images) {
                    return $images;
                }
            }
        }
        return $this->getMedia('images');
    }
}
