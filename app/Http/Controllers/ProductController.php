<?php

namespace App\Http\Controllers;

use App\Domain\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Domain\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    public function index(Request $request)
    {
        // Ensure user is a producer
        $producer = $request->user()->company->producer;
        
        if (!$producer) {
            abort(403, 'Only producers can access this area.');
        }

        $products = $this->productService->listProductsForProducer($producer->id);

        return Inertia::render('Product/Index', [
            'products' => $products,
        ]);
    }

    public function create()
    {
        $categories = \App\Domain\Models\ProductCategory::whereNull('parent_id')
            ->with('children')
            ->where('is_active', true)
            ->get();

        $certifications = \App\Domain\Models\Certification::all();
            
        // Transform for TreeSelect if needed, or pass as is (Vue can handle recursion)
        
        return Inertia::render('Product/Create', [
            'categories' => $categories,
            'certifications' => $certifications,
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $producer = $request->user()->company->producer;
         if (!$producer) {
            abort(403, 'Only producers can create products.');
        }
        
        $this->productService->createProduct(
            $request->validated(), 
            $producer->id, 
            $request->user()->company->id
        );

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        // Authorization check (Policy recommended)
        if (request()->user()->company->producer->id !== $product->producer_id) {
            abort(403);
        }
        
        $categories = \App\Domain\Models\ProductCategory::whereNull('parent_id')
            ->with('children')
            ->where('is_active', true)
            ->get();
        
        return Inertia::render('Product/Edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->productService->updateProduct($product, $request->validated());

        return redirect()->back()
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->productService->deleteProduct($product);

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'company']);
        
        return Inertia::render('Product/Show', [
            'product' => $product,
        ]);
    }
    public function searchHsCodes(Request $request)
    {
        $query = $request->input('query');
        
        $codes = \App\Domain\Models\HsCode::query()
            ->where('code', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->limit(10)
            ->get();
            
        return response()->json($codes);
    }
}
