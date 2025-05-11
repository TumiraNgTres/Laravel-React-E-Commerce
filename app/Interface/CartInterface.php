<?php

namespace App\Interface;

use App\Models\Product;

interface CartInterface
{
    public function addItemToCart(Product $product, int $quantity = 1, $optionIds = null);
    public function updateItemQuantity(int $productId, int $quantity, $optionIds = null);
    public function removeItemFromCart(int $productId, $optionIds = null);

    public function getCartItems(): array;

    public function getTotalQuantity(): int;

    public function getTotalPrice(): float;

    public function getCartItemsGrouped(): array;
}
