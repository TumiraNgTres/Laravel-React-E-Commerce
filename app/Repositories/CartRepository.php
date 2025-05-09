<?php

namespace App\Repositories;

use App\Interface\Repositories\CartRepositoryInterface;
use App\Models\CartItem;

class CartRepository implements CartRepositoryInterface
{
    /**
     * Find a cart item by user, product, and variation option IDs.
     */
    public function getCartItemByUserIdAndProductIdAndVariant(int $userId, int $productId, array $optionIds): ?CartItem
    {
        return CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('variation_type_option_ids', json_encode($optionIds))
            ->first();
    }

    public function deleteCartItemByUserIdAndProductIdAndVariant(int $userId, int $productId, array $optionIds): bool
    {
        return CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('variation_type_option_ids', json_encode($optionIds))
            ->delete();
    }

    public function getCartItemsByUserId(int $userId): array
    {
        return CartItem::where('user_id', $userId)->get()
            ->map(function ($cartItem) {
                return [
                    'id' => $cartItem->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'option_ids' => json_decode($cartItem->variation_type_option_ids, true)
                ];
            })
            ->toArray();
    }
}
