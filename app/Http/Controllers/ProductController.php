<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\SystemLog;
use App\Http\Requests\ProductRequest;
use App\Helpers\ImageHelper;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * AJAX endpoint to generate a unique SKU.
     */
    public function generateSku(Request $request)
    {
        $type = $request->get('type', 'product');
        $defaultPrefix = ($type === 'service') ? 'SRV' : 'PRD';
        $prefix = strtoupper($request->get('prefix', $defaultPrefix));
        $prefix = preg_replace('/[^A-Z0-9]/', '', $prefix) ?: $defaultPrefix;
        
        $sku = $this->generateUniqueSkuString($prefix);

        return response()->json([
            'success' => true,
            'sku' => $sku
        ]);
    }

    /**
     * Generate a guaranteed unique SKU string in the database.
     */
    public function generateUniqueSkuString(string $prefix = 'PRD'): string
    {
        do {
            $randomNum = mt_rand(100000, 999999);
            $sku = $prefix . '-' . $randomNum;
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low_stock') {
                $query->where('type', 'product')
                      ->where('stock', '>', 0)
                      ->where(function ($q) {
                          $q->where(function ($sub) {
                              $sub->where('initial_stock', '>', 0)
                                  ->whereRaw('(stock / initial_stock) <= 0.30');
                          })->orWhere(function ($sub) {
                              $sub->where(function ($s) {
                                  $s->whereNull('initial_stock')->orWhere('initial_stock', '<=', 0);
                              })->where('stock', '<=', 5);
                          });
                      });
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('type', 'product')->where(function ($q) {
                    $q->whereNull('stock')->orWhere('stock', '<=', 0);
                });
            } elseif ($request->stock_status === 'in_stock') {
                $query->where('type', 'product')
                      ->where('stock', '>', 0)
                      ->where(function ($q) {
                          $q->where(function ($sub) {
                              $sub->where('initial_stock', '>', 0)
                                  ->whereRaw('(stock / initial_stock) > 0.30');
                          })->orWhere(function ($sub) {
                              $sub->where(function ($s) {
                                  $s->whereNull('initial_stock')->orWhere('initial_stock', '<=', 0);
                              })->where('stock', '>', 5);
                          });
                      });
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'latest');
        if ($sortBy === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sortBy === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sortBy === 'stock_asc') {
            $query->orderBy('stock', 'asc');
        } elseif ($sortBy === 'stock_desc') {
            $query->orderBy('stock', 'desc');
        } elseif ($sortBy === 'name_asc') {
            $query->orderBy('name', 'asc');
        } else {
            $query->latest('id');
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::where('status', true)->orderBy('name')->get();
        $units = Unit::where('status', true)->orderBy('name')->get();

        // Calculate quick summary stats
        $totalProducts = Product::where('type', 'product')->count();
        $lowStockCount = Product::where('type', 'product')
            ->where('stock', '>', 0)
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('initial_stock', '>', 0)
                        ->whereRaw('(stock / initial_stock) <= 0.30');
                })->orWhere(function ($sub) {
                    $sub->where(function ($s) {
                        $s->whereNull('initial_stock')->orWhere('initial_stock', '<=', 0);
                    })->where('stock', '<=', 5);
                });
            })
            ->count();
        $outOfStockCount = Product::where('type', 'product')
            ->where(function ($q) {
                $q->whereNull('stock')->orWhere('stock', '<=', 0);
            })
            ->count();
        $inStockCount = max(0, $totalProducts - $lowStockCount - $outOfStockCount);

        return view('admin.products.index', compact(
            'products',
            'categories',
            'units',
            'totalProducts',
            'inStockCount',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    public function create()
    {
        $categories = Category::where('status', true)->orderBy('name')->get();
        $units = Unit::where('status', true)->orderBy('name')->get();
        
        // Ensure default units exist if empty
        if ($units->isEmpty()) {
            app(UnitController::class)->index(request());
            $units = Unit::where('status', true)->orderBy('name')->get();
        }

        return view('admin.products.create', compact('categories', 'units'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        // Auto-generate SKU if left blank
        if (empty($data['sku'])) {
            $prefix = ($request->get('type') === 'service') ? 'SRV' : 'PRD';
            $data['sku'] = $this->generateUniqueSkuString($prefix);
        }

        // Align price & sale_price
        if (isset($data['sale_price']) && !isset($data['price'])) {
            $data['price'] = $data['sale_price'];
        } elseif (isset($data['price']) && !isset($data['sale_price'])) {
            $data['sale_price'] = $data['price'];
        }

        // Manage initial_stock and current stock
        $initial = isset($data['initial_stock']) ? (int) $data['initial_stock'] : 0;
        $current = isset($data['stock']) ? (int) $data['stock'] : null;

        if ($current === null) {
            $data['stock'] = $initial;
        }
        if ($initial === 0 && $data['stock'] > 0) {
            $data['initial_stock'] = $data['stock'];
        }

        // Handle single primary image
        if ($request->hasFile('image')) {
            $fileName = ImageHelper::compressAndSave($request->file('image'), 'uploads/products');
            $data['image'] = 'uploads/products/' . $fileName;
        }

        // Handle additional gallery images
        $galleryImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $fileName = ImageHelper::compressAndSave($file, 'uploads/products');
                $galleryImages[] = 'uploads/products/' . $fileName;
            }
        }
        $data['images'] = !empty($galleryImages) ? $galleryImages : null;

        $product = Product::create($data);

        SystemLog::create([
            'user_id' => auth()->id(),
            'action' => 'Created',
            'model_type' => 'Product',
            'description' => "Created Product: {$product->name} (SKU: {$product->sku})"
        ]);

        return redirect()->route('products.index')->with('success', __('Product created successfully.'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', true)->orderBy('name')->get();
        $units = Unit::where('status', true)->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'units'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();

        // Align prices
        if (isset($data['sale_price'])) {
            $data['price'] = $data['sale_price'];
        } elseif (isset($data['price'])) {
            $data['sale_price'] = $data['price'];
        }

        // Handle primary image update
        if ($request->hasFile('image')) {
            // Delete old primary image if exists
            if (!empty($product->image) && file_exists(public_path($product->image))) {
                @unlink(public_path($product->image));
            }
            $fileName = ImageHelper::compressAndSave($request->file('image'), 'uploads/products');
            $data['image'] = 'uploads/products/' . $fileName;
        }

        // Handle gallery images removal and addition
        $existingGallery = is_array($product->images) ? $product->images : [];

        if ($request->filled('removed_images')) {
            $removed = json_decode($request->removed_images, true) ?: [];
            foreach ($removed as $oldImg) {
                if (file_exists(public_path($oldImg))) {
                    @unlink(public_path($oldImg));
                }
                $existingGallery = array_diff($existingGallery, [$oldImg]);
            }
            $existingGallery = array_values($existingGallery);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $fileName = ImageHelper::compressAndSave($file, 'uploads/products');
                $existingGallery[] = 'uploads/products/' . $fileName;
            }
        }
        $data['images'] = !empty($existingGallery) ? array_values($existingGallery) : null;

        // Manage initial_stock if current stock exceeds initial
        if (isset($data['stock']) && isset($data['initial_stock'])) {
            if ($data['stock'] > $data['initial_stock']) {
                $data['initial_stock'] = $data['stock'];
            }
        }

        $product->update($data);

        SystemLog::create([
            'user_id' => auth()->id(),
            'action' => 'Updated',
            'model_type' => 'Product',
            'description' => "Updated Product: {$product->name} (SKU: {$product->sku})"
        ]);

        return redirect()->route('products.index')->with('success', __('Product updated successfully.'));
    }

    public function destroy(Product $product)
    {
        $name = $product->name;

        // Optionally delete files
        if (!empty($product->image) && file_exists(public_path($product->image))) {
            @unlink(public_path($product->image));
        }
        if (!empty($product->images) && is_array($product->images)) {
            foreach ($product->images as $img) {
                if (file_exists(public_path($img))) {
                    @unlink(public_path($img));
                }
            }
        }

        $product->delete();

        SystemLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted',
            'model_type' => 'Product',
            'description' => "Deleted Product: {$name}"
        ]);

        return redirect()->route('products.index')->with('success', __('Product deleted successfully.'));
    }
}
