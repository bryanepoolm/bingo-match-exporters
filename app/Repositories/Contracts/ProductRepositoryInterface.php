<?php

namespace App\Repositories\Contracts;

use App\Domain\Models\Product;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface
{
    public function create(array $data): Product;
    public function update(Product $product, array $data): Product;
    public function delete(Product $product): bool;
    public function findById(int $id): ?Product;
    public function findByProducerId(int $producerId): Collection;
    public function paginateByProducerId(int $producerId, int $perPage = 15);
}
