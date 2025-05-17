<?php

namespace App\Http\Controllers;

use App\Interface\CartInterface;
use App\Interface\OrderInterface;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CartController extends Controller
{
    protected $cartService;
    protected $orderService;

    public function __construct(CartInterface $cartService, OrderInterface $orderService)
    {
        $this->cartService = $cartService;
        $this->orderService = $orderService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Cart/Index', [
            'cartItems' => $this->cartService->getCartItemsGrouped(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product)
    {
        $request->mergeIfMissing([
            'quantity' => 1
        ]);

        $data = $request->validate([
            'option_ids' => ['nullable', 'array'],
            'quantity' => ['required', 'integer', 'min:1']
        ]);

        $this->cartService->addItemToCart(
            $product,
            $data['quantity'],
            $data['option_ids'] ?: []
        );

        return redirect()->back()->with('success', 'Product added to cart successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => ['integer', 'min:1']
        ]);

        $optionIds = $request->input('option_ids') ?: [];
        $quantity = $request->input('quantity');

        $this->cartService->updateItemQuantity($product->id, $quantity, $optionIds);

        return redirect()->back()->with('success', 'Quantity was updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product)
    {
        $optionIds = $request->input('option_ids');

        $this->cartService->removeItemFromCart($product->id, $optionIds);

        return back()->with('success', 'Product was removed from cart');
    }

    public function checkout(Request $request)
    {
        $vendorId = $request->input('vendor_id');

        $allCartItems = $this->cartService->getCartItemsGrouped();

        DB::beginTransaction();

        try {
            $checkoutCartItems = $vendorId ? $allCartItems[$vendorId] : $allCartItems;

            // Use OrderService to create orders and Stripe session
            $result = $this->orderService->createOrdersAndStripeSession($request->user(), $checkoutCartItems, $vendorId);

            DB::commit();
            return redirect($result['session']->url);
        } catch (Exception $e) {
            Log::error($e);
            DB::rollBack();
            return back()->with('error', $e->getMessage() ?: 'Something went wrong');
        }
    }
}
