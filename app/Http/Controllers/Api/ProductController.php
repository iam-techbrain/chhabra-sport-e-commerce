<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Tag;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // 1. Database Category Filter
        if ($request->filled('category') && $request->category !== 'all') {
            $cat = strtolower(trim($request->category));
            $query->whereRaw('LOWER(category) = ?', [$cat]);
        }

        // 2. Database Brand Filter
        if ($request->filled('brand') && $request->brand !== 'all') {
            $brand = strtolower(trim($request->brand));
            $query->whereRaw('LOWER(brand) = ?', [$brand]);
        }

        // 3. Database Tag Filter
        if ($request->filled('tag') && $request->tag !== 'all') {
            $tag = strtolower(trim($request->tag));
            $query->whereRaw('LOWER(tag) = ?', [$tag]);
        }

        // 4. Database Stock Filter
        if ($request->filled('stock') && $request->stock !== 'all') {
            if ($request->stock === 'in_stock') {
                $query->where('in_stock', true);
            } else if ($request->stock === 'out_of_stock') {
                $query->where('in_stock', false);
            }
        }

        // 5. Database Product Type Filter
        if ($request->filled('type') && $request->type !== 'all') {
            if ($request->type === 'variable') {
                $query->where('is_variable', true);
            } else if ($request->type === 'simple') {
                $query->where(function($q) {
                    $q->where('is_variable', false)->orWhereNull('is_variable');
                });
            }
        }

        // 6. Database Full Search (Name, SKU, Brand, Category, Specs, Tag)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code_id', 'LIKE', "%{$search}%")
                  ->orWhere('brand', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%")
                  ->orWhere('specs', 'LIKE', "%{$search}%")
                  ->orWhere('tag', 'LIKE', "%{$search}%");
            });
        }

        // 7. Database Sorting Engine
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc')->orderBy('id', 'asc');
                    break;
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
                    $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $total = $query->count();

        // Return unpaginated if explicitly requested via ?all=true
        if ($request->has('all') && ($request->all === 'true' || $request->all === '1')) {
            $products = $query->get();
            return response()->json([
                'success' => true,
                'count' => $total,
                'total' => $total,
                'data' => $products
            ]);
        }

        $perPage = max((int) $request->get('per_page', 10), 1);
        $page = max((int) $request->get('page', 1), 1);

        $lastPage = max((int) ceil($total / $perPage), 1);
        if ($page > $lastPage) {
            $page = $lastPage;
        }

        $products = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        return response()->json([
            'success' => true,
            'count' => $products->count(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
            'data' => $products
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable',
            'code_id' => 'nullable|string',
            'category' => 'nullable|string',
            'brand' => 'nullable|string'
        ]);

        $inStock = true;
        if ($request->has('in_stock')) {
            $inStock = filter_var($request->in_stock, FILTER_VALIDATE_BOOLEAN);
        } else if ($request->has('stockStatus')) {
            $inStock = $request->stockStatus === 'In stock';
        }

        $brandName = $request->filled('brand') ? trim($request->brand) : 'Generic';

        // Auto-create Brand in database if not existing
        if (!empty($brandName) && strtolower($brandName) !== 'generic') {
            Brand::firstOrCreate(
                ['slug' => Str::slug($brandName)],
                ['name' => $brandName, 'description' => 'Official Sports Brand']
            );
        }

        // Auto-create Category and Subcategory in database if hierarchy string (e.g., "Cricket > Batting Gloves")
        $rawCategory = $request->filled('category') ? trim($request->category) : 'badminton';
        $categorySlug = Str::slug($rawCategory);

        if (str_contains($rawCategory, '>')) {
            $catParts = array_map('trim', explode('>', $rawCategory));
            $parentCatName = $catParts[0];
            $subCatName = $catParts[count($catParts) - 1];

            $parentCategory = Category::firstOrCreate(
                ['slug' => Str::slug($parentCatName)],
                ['name' => $parentCatName, 'icon' => '📦']
            );

            $subCat = Category::firstOrCreate(
                ['slug' => Str::slug($subCatName)],
                [
                    'name' => $subCatName,
                    'parent_id' => $parentCategory->id,
                    'icon' => '📦'
                ]
            );
            $categorySlug = $subCat->slug;
        } else if (!empty($rawCategory)) {
            $createdCat = Category::firstOrCreate(
                ['slug' => Str::slug($rawCategory)],
                ['name' => ucfirst($rawCategory), 'icon' => '📦']
            );
            $categorySlug = $createdCat->slug;
        }

        // Base64 Image Processing
        $imgUrl = $request->img ?? 'https://images.unsplash.com/photo-1708312604109-16c0be9326cd?w=600&q=80';
        if (!empty($request->img) && str_starts_with($request->img, 'data:image/')) {
            try {
                if (preg_match('/data:image\/(?<type>\w+);base64,(?<data>.+)/i', $request->img, $matches)) {
                    $imageType = strtolower($matches['type']);
                    $imageData = base64_decode($matches['data']);
                    $fileName = 'prod_' . time() . '_' . Str::random(6) . '.' . $imageType;
                    
                    $uploadPath = public_path('uploads/products');
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    file_put_contents($uploadPath . '/' . $fileName, $imageData);
                    $imgUrl = '/uploads/products/' . $fileName;
                }
            } catch (\Exception $e) {
                // Fallback to placeholder if base64 decoding fails
                $imgUrl = 'https://images.unsplash.com/photo-1708312604109-16c0be9326cd?w=600&q=80';
            }
        }

        $product = null;

        // 1. Search by DB primary key 'id' if provided
        if ($request->has('id') && !empty($request->id) && is_numeric($request->id)) {
            $product = Product::find($request->id);
        }

        // 2. Search by 'code_id' if not found by id
        if (!$product && $request->filled('code_id')) {
            $product = Product::where('code_id', trim($request->code_id))->first();
        }

        // 3. Search by exact 'name' if still not found
        if (!$product && $request->filled('name')) {
            $product = Product::where('name', trim($request->name))->first();
        }

        // 4. Create new product if no match found
        if (!$product) {
            $product = new Product();
        }

        // Code / SKU generation and unique collision check
        $targetCodeId = $request->filled('code_id') ? trim($request->code_id) : ('SKU-' . strtoupper(Str::random(6)));
        $existingCodeMatch = Product::where('code_id', $targetCodeId)->where('id', '!=', $product->id ?? 0)->first();
        if ($existingCodeMatch) {
            if (!$request->filled('id')) {
                // If uploading a new item without explicitly providing an existing database ID, update existing matched SKU product or append suffix
                $product = $existingCodeMatch;
            } else {
                $targetCodeId = $targetCodeId . '-' . rand(10, 99);
            }
        }

        // Price sanitization
        $rawPrice = $request->price;
        $cleanPrice = is_numeric($rawPrice) ? (float)$rawPrice : (float)preg_replace('/[^0-9.]/', '', (string)$rawPrice);
        if ($cleanPrice <= 0) $cleanPrice = 1000;

        $cleanOldPrice = null;
        if ($request->filled('old_price')) {
            $rawOld = $request->old_price;
            $parsedOld = is_numeric($rawOld) ? (float)$rawOld : (float)preg_replace('/[^0-9.]/', '', (string)$rawOld);
            if ($parsedOld > 0) $cleanOldPrice = $parsedOld;
        }

        $product->code_id = $targetCodeId;
        $product->name = trim($request->name);
        $product->category = $categorySlug;
        $product->brand = $brandName;
        $product->price = $cleanPrice;
        $product->old_price = $cleanOldPrice;
        $product->rating = $request->rating ?? 5.0;
        $product->reviews = $request->reviews ?? 1;
        $product->tag = $request->tag ?? 'NEW';
        $product->specs = $request->specs ?? '';
        $product->img = $imgUrl;
        $product->in_stock = $inStock;
        $product->is_variable = $request->isVariable ?? $request->is_variable ?? false;
        $product->variations = $request->variations ?? [];
        $product->save();

        return response()->json([
            'success' => true,
            'message' => "Product '{$product->name}' saved successfully!",
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
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        // SAFETY RULE 2: If any customer order exists for this product, block deletion!
        $isOrdered = false;
        $allOrders = Order::select('id', 'order_number', 'items')->get();
        foreach ($allOrders as $ord) {
            if (is_array($ord->items)) {
                foreach ($ord->items as $item) {
                    $itemId = $item['id'] ?? $item['product_id'] ?? null;
                    $itemCode = $item['code_id'] ?? null;
                    $itemName = $item['name'] ?? null;

                    if (
                        ($itemId && ((string)$itemId === (string)$product->id || (string)$itemId === (string)$product->code_id)) ||
                        ($itemCode && (string)$itemCode === (string)$product->code_id) ||
                        ($itemName && strtolower(trim($itemName)) === strtolower(trim($product->name)))
                    ) {
                        $isOrdered = true;
                        break 2;
                    }
                }
            }
        }

        if ($isOrdered) {
            return response()->json([
                'success' => false,
                'message' => "Product '{$product->name}' cannot be deleted because it exists in past customer order records!"
            ], 422);
        }

        $name = $product->name;
        $product->delete();
        return response()->json(['success' => true, 'message' => "Product '{$name}' deleted from database"]);
    }

    // --- CATEGORY DATABASE ENDPOINTS ---
    public function categories()
    {
        $allCategories = Category::with('parent')->get();
        $tree = Category::with('children')->whereNull('parent_id')->get();
        return response()->json([
            'success' => true,
            'data' => $allCategories,
            'tree' => $tree
        ]);
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $parentId = $request->filled('parent_id') ? $request->parent_id : null;

        $cat = Category::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $request->name,
                'parent_id' => $parentId,
                'icon' => $request->icon ?? '📦',
                'description' => $request->description ?? ''
            ]
        );

        return response()->json(['success' => true, 'data' => $cat->load('parent')]);
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate(['name' => 'required|string']);
        $cat = Category::find($id);
        if (!$cat) {
            $cat = Category::where('slug', urldecode($id))->orWhere('name', urldecode($id))->first();
        }

        $name = $request->name;
        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($name);
        $parentId = null;
        if ($request->has('parent_id') && $request->parent_id && $request->parent_id !== 'null') {
            $parentId = $request->parent_id;
        }

        if ($cat) {
            $oldSlug = $cat->slug;
            $oldName = $cat->name;

            $cat->name = $name;
            $cat->slug = $slug;
            $cat->parent_id = $parentId;
            if ($request->has('icon')) $cat->icon = $request->icon;
            $cat->save();

            // Cascade category update to products if slug/name changed
            if ($oldSlug !== $slug || strtolower($oldName) !== strtolower($name)) {
                Product::where('category', $oldSlug)
                       ->orWhere('category', $oldName)
                       ->orWhereRaw('LOWER(category) = ?', [strtolower($oldName)])
                       ->update(['category' => $slug]);
            }
        } else {
            $cat = Category::create([
                'name' => $name,
                'slug' => $slug,
                'parent_id' => $parentId,
                'icon' => $request->icon ?? '📦'
            ]);
        }

        return response()->json(['success' => true, 'data' => $cat->load('parent')]);
    }

    public function destroyCategory($id)
    {
        $cat = Category::find($id);
        if (!$cat) {
            $cat = Category::where('slug', $id)->orWhere('name', $id)->first();
        }

        if (!$cat) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }

        // SAFETY RULE 1: If any product is listed under this category, block deletion!
        $catNameLower = strtolower(trim($cat->name));
        $catSlugLower = strtolower(trim($cat->slug));

        $productCount = Product::whereRaw('LOWER(category) = ?', [$catNameLower])
            ->orWhereRaw('LOWER(category) = ?', [$catSlugLower])
            ->count();

        if ($productCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Category '{$cat->name}' cannot be deleted because {$productCount} product(s) are currently listed under it!"
            ], 422);
        }

        $cat->delete();
        return response()->json(['success' => true, 'message' => "Category '{$cat->name}' deleted successfully!"]);
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

    public function updateBrand(Request $request, $id)
    {
        $request->validate(['name' => 'required|string']);
        $brand = Brand::find($id);
        if (!$brand) {
            $brand = Brand::where('name', $id)->orWhere('slug', Str::slug($id))->first();
        }

        $name = $request->name;
        $slug = Str::slug($name);
        $desc = $request->desc ?? $request->description ?? 'Official Sports Brand';

        if ($brand) {
            $brand->name = $name;
            $brand->slug = $slug;
            $brand->description = $desc;
            $brand->save();
        } else {
            $brand = Brand::create([
                'name' => $name,
                'slug' => $slug,
                'description' => $desc
            ]);
        }

        return response()->json(['success' => true, 'data' => $brand]);
    }

    public function destroyBrand($id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            $brand = Brand::where('name', $id)->orWhere('slug', $id)->first();
        }
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

    public function updateTag(Request $request, $id)
    {
        $request->validate(['name' => 'required|string']);
        $tag = Tag::find($id);
        if (!$tag) {
            $tag = Tag::where('name', $id)->orWhere('slug', $id)->first();
        }

        $name = strtoupper(trim($request->name));
        $slug = Str::slug($name);

        if ($tag) {
            $tag->name = $name;
            $tag->slug = $slug;
            $tag->save();
        } else {
            $tag = Tag::create(['name' => $name, 'slug' => $slug]);
        }

        return response()->json(['success' => true, 'data' => $tag]);
    }

    public function destroyTag($id)
    {
        $tag = Tag::find($id);
        if (!$tag) {
            $tag = Tag::where('name', $id)->orWhere('slug', $id)->first();
        }
        if ($tag) $tag->delete();
        return response()->json(['success' => true]);
    }
}
