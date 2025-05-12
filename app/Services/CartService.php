<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Interface\CartInterface;
use App\Interface\Repositories\CartRepositoryInterface;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\VariationType;
use App\Models\VariationTypeOption;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CartService implements CartInterface
{
    private ?array $cachedCartItems = null;
    protected const COOKIE_NAME = 'cartItems';
    protected const COOKIE_LIFETIME = 60 * 24 * 365; // 1 year

    protected CartRepositoryInterface $cartRepo;
    public function __construct(CartRepositoryInterface $cartRepo)
    {
        $this->cartRepo = $cartRepo;
    }

    public function addItemToCart(Product $product, int $quantity = 1, $optionIds = null)
    {

        if ($optionIds === null) {
            //  variation type id is the key and option id is value
            $optionIds = $product->variationTypes()
                ->mapWithKeys(fn(VariationType $type) => [$type->id => $type->options[0]?->id])
                ->toArray();
        }

        $price = $product->getPriceForOptions($optionIds);

        if (Auth::check()) {
            $this->saveItemToDatabase($product->id, $quantity, $price, $optionIds);
        } else {
            $this->saveItemToCookies($product->id, $quantity, $price, $optionIds);
        }
    }

    public function updateItemQuantity(int $productId, int $quantity, $optionIds = null)
    {
        if (Auth::check()) {
            $this->updateItemQuantityInDatabase($productId, $quantity, $optionIds);
        } else {
            $this->updateItemQuantityInCookies($productId, $quantity, $optionIds);
        }
    }

    public function removeItemFromCart(int $productId, $optionIds = null)
    {
        if (Auth::check()) {
            $this->removeItemFromDatabase($productId, $optionIds);
        } else {
            $this->removeItemFromCookies($productId, $optionIds);
        }
    }

    public function getCartItems(): array
    {
        try {
            if ($this->cachedCartItems === null) {
                if (Auth::check()) {
                    $cartItems = $this->getCartItemsFromDatabase();
                } else {
                    $cartItems = $this->getCartItemsFromCookies();
                }

                // cartItems is array and convert this to collection using collect()
                $productIds = collect($cartItems)->map(fn($item) => $item['product_id']);

                $products = Product::whereIn('id', $productIds)
                    ->with('user.vendor')
                    ->forWebsite()
                    ->get()
                    ->keyBy('id');

                /*
                    reindex the cart items array
                    [
                        0 => Product { id: 101, name: 'T-Shirt' },
                        1 => Product { id: 205, name: 'Jeans' },
                    ]
                    to the product id. so easy access  - keyBy('id') doing that
                    [
                        101 => Product { id: 101, name: 'T-Shirt' },
                        205 => Product { id: 205, name: 'Jeans' },
                    ]
                */

                $cartItemData = [];
                foreach ($cartItems as $key => $cartItem) {
                    /* instead of
                        $product = $products[$cartItem['product_id']] ?? null;
                        or
                        $product = $products->get($cartItem['product_id']);
                        it is safe and avoids the errors "undefined index" or "property does not exist"
                    */
                    $product = data_get($products, $cartItem['product_id']);
                    if (!$product) continue;

                    $optionInfo = [];

                    $options = VariationTypeOption::with('variationType')
                        ->whereIn('id', $cartItem['option_ids'])
                        ->get()
                        ->keyBy('id');

                    $imageUrl = null;
                    foreach ($cartItem['option_ids'] as $optionId) {
                        // foreach ($optionIdValues as $optionId) {
                        $option = data_get($options, $optionId);
                        if (!$imageUrl) {
                            $imageUrl = $option->getFirstMediaUrl('images', 'small');
                        }

                        $optionInfo[] = [
                            'id' => $option->id,
                            'name' => $option->name,
                            'type' => [
                                'id' => $option->variationType->id,
                                'name' => $option->variationType->name,
                            ]
                        ];
                    }

                    $cartItemData[] = [
                        'id' => $cartItem['id'],
                        'product_id' => $product->id,
                        'title' => $product->title,
                        'slug' => $product->slug,
                        'price' => $cartItem['price'],
                        'quantity' => $cartItem['quantity'],
                        'option_ids' => $cartItem['option_ids'],
                        'options' => $optionInfo,
                        'image' => $imageUrl ?: $product->getFirstMediaUrl('images', 'small'),
                        'users' => [
                            'id' => $product->created_by,
                            'name' => $product->user->vendor->store_name,
                        ]
                    ];
                }

                $this->cachedCartItems = $cartItemData;
            }

            return $this->cachedCartItems;
        } catch (Exception $e) {
            throw $e;
            // Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            // return [];
        }
    }

    public function getTotalQuantity(): int
    {
        $totalQuantity = 0;
        foreach ($this->getCartItems() as $item) {
            $totalQuantity += $item['quantity'];
        }
        return $totalQuantity;
    }

    public function getTotalPrice(): float
    {
        $totalPrice = 0;
        foreach ($this->getCartItems() as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }
        return $totalPrice;
    }

    protected function updateItemQuantityInDatabase(int $productId, int $quantity, array $optionIds): void
    {
        $userId = Auth::id();
        $cartItem = $this->cartRepo->getCartItemByUserIdAndProductIdAndVariant($userId, $productId, $optionIds);

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $quantity,
            ]);
        }
    }
    protected function updateItemQuantityInCookies(int $productId, int $quantity, array $optionIds): void
    {
        $cartItems = $this->getCartItemsFromCookies();
        ksort($optionIds);

        // to read data from cookies create a unique id for cookies datas
        // use a unique key fro cookies using productid and option id like this
        // '1_[1,4]' means - productid(1)_[variation combination option ids]([1,4])
        $itemKey = $productId . '_' . json_encode($optionIds);

        if (isset($cartItems[$itemKey])) {
            $cartItems[$itemKey]['quantity'] = $quantity;
        }
        //  to add a cookie to the response. It queues the cookie to be sent on the next HTTP response.
        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems), self::COOKIE_LIFETIME);
    }

    protected function saveItemToDatabase(int $productId, int $quantity, $price, array $optionIds)
    {
        $userId = Auth::id();
        ksort($optionIds);

        $cartItem = $this->cartRepo->getCartItemByUserIdAndProductIdAndVariant($userId, $productId, $optionIds);

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $quantity,
                // 'price' => $price
            ]);
        } else {
            CartItem::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'variation_type_option_ids' => json_encode($optionIds),
            ]);
        }
    }
    protected function saveItemToCookies(int $productId, int $quantity, $price, array $optionIds)
    {
        $cartItems = $this->getCartItemsFromCookies();

        ksort($optionIds);

        $itemKey = $productId . '_' . json_encode($optionIds);

        if (isset($cartItems[$itemKey])) {
            $cartItems[$itemKey]['quantity'] += $quantity;
            $cartItems[$itemKey]['price'] = $price;
        } else {
            $cartItems[$itemKey] = [
                'id' => Str::uuid(),
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'option_ids' => $optionIds
            ];
        }

        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems), self::COOKIE_LIFETIME);
    }

    protected function removeItemFromDatabase(int $productId, array $optionIds): void
    {
        $userId = Auth::id();

        ksort($optionIds);

        $this->cartRepo->deleteCartItemByUserIdAndProductIdAndVariant($userId, $productId, $optionIds);
    }
    protected function removeItemFromCookies(int $productId, array $optionIds): void
    {
        $cartItems = $this->getCartItemsFromCookies();

        ksort($optionIds);

        $cartKey = $productId . '_' . json_encode($optionIds);
        // remove cart items from cart in cookies
        unset($cartItems[$cartKey]);

        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems), self::COOKIE_LIFETIME);
    }

    protected function getCartItemsFromDatabase()
    {
        $userId = Auth::id();
        $cartItems = $this->cartRepo->getCartItemsByUserId($userId);
        return $cartItems;
    }
    protected function getCartItemsFromCookies()
    {
        $cartItems = CommonHelper::getCartItemsFromCookies();
        return $cartItems;
    }

    public function getCartItemsGrouped(): array
    {
        $cartItems = $this->getCartItems();

        $data = collect($cartItems)
            ->groupBy(fn($item) => $item['users']['id'])
            ->map(fn($items, $userId) => [
                'user' => $items->first()['users'],
                'items' => $items->toArray(),
                'totalQuantity' => $items->sum('quantity'),
                'totalPrice' => $items->sum(fn($item) => ($item['price'] * $item['quantity']))
            ])
            ->toArray();

        return $data;
    }

    // move guest user cart item from cookies to database when login
    public function moveCartItemsToDatabase($userId): void
    {
        $cartItems = $this->getCartItemsFromCookies();

        foreach ($cartItems as $key => $cartItem) {
            $existingItem = $this->cartRepo->getCartItemByUserIdAndProductIdAndVariant($userId, $cartItem['product_id'], $cartItem['option_ids']);

            if ($existingItem) {
                // update existing item
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $cartItem['quantity'],
                    'price' => $cartItem['price'],
                ]);
            } else {
                CartItem::create([
                    'user_id' => $userId,
                    'product_id' => $cartItem['product_id'],
                    'quantity' => $cartItem['quantity'],
                    'price' => $cartItem['quantity'],
                    'variation_type_option_ids' => json_encode($cartItem['option_ids']),
                ]);
            }

            // delete all that cookie datas after transfering to database
            Cookie::queue(self::COOKIE_NAME, '', -1);
        }
    }
}
