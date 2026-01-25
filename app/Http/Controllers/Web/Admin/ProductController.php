<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Domain\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['company'])->latest()->paginate(10);
        
        return Inertia::render('Admin/Products', [
            'products' => $products,
        ]);
    }
}
