<?php

namespace App\Interface;

interface OrderInterface
{
    public function createOrdersAndStripeSession(object $user, array $cartItems, ?int $vendorId = null): array;
}
