<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function home()
    {
        $products = Product::query()
            ->published()
            ->paginate(12);

        return Inertia::render('Home', [
            'products' => ProductListResource::collection($products),
        ]);
    }

   public function shop(Request $request)
    {
        $query = Product::query()->published();

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->sort === 'price_asc') {
            $query->orderBy('price');
        } elseif ($request->sort === 'price_desc') {
            $query->orderByDesc('price');
        } elseif ($request->sort === 'latest') {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::select('id', 'name')->get();

        return Inertia::render('Product/Shop', [
            'products' => ProductListResource::collection($products),
            'filters' => [
                'categories' => $categories,
            ],
            // 'meta' => [
            //     'current_page' => $products->currentPage(),
            //     'last_page' => $products->lastPage(),
            //     'from' => $products->firstItem(),
            //     'to' => $products->lastItem(),
            //     'per_page' => $products->perPage(),
            //     'total' => $products->total(),
            // ],
            // 'links' => $products->linkCollection()->toArray(),
        ]);
    }

    public function show(Product $product)
    {
        return Inertia::render('Product/Show', [
            'product' => new ProductResource($product),
            'variationOptions' => request('options', [])
        ]);
    }
}
