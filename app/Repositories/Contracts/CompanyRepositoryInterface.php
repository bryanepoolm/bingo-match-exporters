<?php

namespace App\Repositories\Contracts;

use App\Domain\Models\Company;

interface CompanyRepositoryInterface
{
    public function create(array $data): Company;
    public function findByUserId(int $userId): ?Company;
}
