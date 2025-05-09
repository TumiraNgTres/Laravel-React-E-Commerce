<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cookie;

class CommonHelper
{
    protected const COOKIE_NAME = 'cartItems';
    public static function getCartItemsFromCookies(): array
    {
        // true mean convert  cookie data to asscociative array
        // associative array mean string index instaed of numeric index
        // means like this
        /*
        $user = [
            'name' => 'Archana',
            'role' => 'Developer'
        ];
        */
        return json_decode(Cookie::get(self::COOKIE_NAME), true) ?? [];
    }
}
