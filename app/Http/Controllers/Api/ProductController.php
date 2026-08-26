<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Tag;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', strtolower($request->category));
        }

        if ($request->filled('brand') && $request->brand !== 'all') {
            $query->where('brand', 'LIKE', '%' . $request->brand . '%');
        }

        if ($request->filled('type') && $request->type !== 'all') {
            if ($request->type === 'variable') {
                $query->where('is_variable', true);
            } else if ($request->type === 'simple') {
                $query->where(function($q) {
                    $q->where('is_variable', false)->orWhereNull('is_variable');
                });
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('brand', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%")
                  ->orWhere('specs', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price-low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price-high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('rating', 'desc');
                    break;
                default:
                    $query->orderBy('id', 'asc');
            }
        } else {
            $query->orderBy('id', 'asc');
        }

        $products = $query->get();

        return response()->json([
            'success' => true,
            'count' => $products->count(),
            'data' => $products
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code_id' => 'required|string',
            'category' => 'required|string',
            'brand' => 'required|string',
            'price' => 'required|numeric'
        ]);

        $product = Product::updateOrCreate(
            ['code_id' => $request->code_id],
            [
                'name' => $request->name,
                'category' => $request->category,
                'brand' => $request->brand,
                'price' => $request->price,
                'old_price' => $request->old_price ?? ($request->price + 1500),
                'rating' => $request->rating ?? 5.0,
                'reviews' => $request->reviews ?? 1,
                'tag' => $request->tag ?? 'NEW',
                'specs' => $request->specs ?? '',
                'img' => $request->img ?? 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&q=80',
                'in_stock' => true,
                'is_variable' => $request->isVariable ?? $request->is_variable ?? false,
                'variations' => $request->variations ?? []
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Product '{$product->name}' with variations saved to SQLite database successfully!",
            'data' => $product
        ]);
    }

    public function show($id)
    {
        $product = Product::where('code_id', $id)->orWhere('id', $id)->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function destroy($id)
    {
        $product = Product::where('code_id', $id)->orWhere('id', $id)->first();
        if ($product) {
            $name = $product->name;
            $product->delete();
            return response()->json(['success' => true, 'message' => "Product '{$name}' deleted from database"]);
        }
        return response()->json(['success' => false, 'message' => 'Product not found'], 404);
    }

    // --- CATEGORY DATABASE ENDPOINTS ---
    public function categories()
    {
        $categories = Category::all();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);

        $cat = Category::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $request->name,
                'icon' => $request->icon ?? '📦',
                'description' => $request->description ?? ''
            ]
        );

        return response()->json(['success' => true, 'data' => $cat]);
    }

    public function destroyCategory($id)
    {
        $cat = Category::find($id);
        if ($cat) $cat->delete();
        return response()->json(['success' => true]);
    }

    // --- BRAND DATABASE ENDPOINTS ---
    public function brands()
    {
        $brands = Brand::all();
        return response()->json([
            'success' => true,
            'data' => $brands
        ]);
    }

    public function storeBrand(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        $slug = Str::slug($request->name);

        $brand = Brand::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $request->name,
                'description' => $request->desc ?? $request->description ?? 'Official Sports Brand'
            ]
        );

        return response()->json(['success' => true, 'data' => $brand]);
    }

    public function destroyBrand($id)
    {
        $brand = Brand::find($id);
        if ($brand) $brand->delete();
        return response()->json(['success' => true]);
    }

    // --- TAG DATABASE ENDPOINTS ---
    public function tags()
    {
        $tags = Tag::all();
        return response()->json([
            'success' => true,
            'data' => $tags
        ]);
    }

    public function storeTag(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        $name = strtoupper(trim($request->name));
        $slug = Str::slug($name);

        $tag = Tag::updateOrCreate(['slug' => $slug], ['name' => $name]);

        return response()->json(['success' => true, 'data' => $tag]);
    }

    public function destroyTag($id)
    {
        $tag = Tag::find($id);
        if ($tag) $tag->delete();
        return response()->json(['success' => true]);
    }
}
