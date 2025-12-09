<?php

namespace App\Services\Company;

use App\Repositories\Contracts\CompanyRepositoryInterface;
use App\Domain\Models\Company;

class CompanyService
{
    public function __construct(
        protected CompanyRepositoryInterface $companyRepository
    ) {}

    public function createCompany(int $userId, array $data): Company
    {
        $data['user_id'] = $userId;
        return $this->companyRepository->create($data);
    }
}
