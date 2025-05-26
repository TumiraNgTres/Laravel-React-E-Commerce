<?php

namespace App\Interface;

use Stripe\StripeClient;

interface StripeWebhookInterface
{
    /**
     * Handle the given Stripe event.
     *
     * @param object $event
     * @param StripeClient $stripe
     * @return void
     */
    public function handle(object $event, StripeClient $stripe): void;
}
