<?php

namespace App\Repositories\Eloquent;

use App\Domain\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findByProducerId(int $producerId): Collection
    {
        return Product::where('producer_id', $producerId)->get();
    }

    public function paginateByProducerId(int $producerId, int $perPage = 15): LengthAwarePaginator
    {
        return Product::where('producer_id', $producerId)
            ->latest()
            ->paginate($perPage); // Returns LengthAwarePaginator, which is compatible with array/json response
    }
}
