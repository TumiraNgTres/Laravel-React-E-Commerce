<?php

namespace App\Interface\Repositories;

use App\Models\CartItem;

interface CartRepositoryInterface
{
    public function getCartItemByUserIdAndProductIdAndVariant(int $userId, int $productId, array $optionIds): ?CartItem;

    public function deleteCartItemByUserIdAndProductIdAndVariant(int $userId, int $productId, array $optionIds): bool;

    public function getCartItemsByUserId(int $userId): array;
}
