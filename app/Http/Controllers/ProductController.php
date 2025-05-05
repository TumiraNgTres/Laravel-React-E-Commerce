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
        // Initialize the query
        $query = Product::query()->published();

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Price range filter
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Sorting
        if ($request->sort === 'price_asc') {
            $query->orderBy('price');
        } elseif ($request->sort === 'price_desc') {
            $query->orderByDesc('price');
        } elseif ($request->sort === 'latest') {
            $query->latest();
        }

        // Paginate products
        $products = $query->paginate(12)->withQueryString();

        // Fetch categories with product count
        $categories = Category::select('id', 'name')
            ->withCount('products')  // Counts the number of products in each category
            ->has('products')        // Filters categories that have products
            ->get();

        return Inertia::render('Product/Shop', [
            // 'products' => ProductListResource::collection($products),
            'products' => [
                'data' => ProductListResource::collection($products)->resolve(),
                'links' => $products->toArray()['links'],
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
            ],
            'filters' => [
                'categories' => $categories,
            ],
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
