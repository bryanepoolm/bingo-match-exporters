<?php

namespace App\Repositories\Eloquent;

use App\Domain\Models\Company;
use App\Repositories\Contracts\CompanyRepositoryInterface;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function create(array $data): Company
    {
        return Company::create($data);
    }

    public function findByUserId(int $userId): ?Company
    {
        return Company::where('user_id', $userId)->first();
    }
}
