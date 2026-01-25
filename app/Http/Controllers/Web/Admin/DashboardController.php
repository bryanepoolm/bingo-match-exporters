<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Models\Company;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'companies' => Company::with(['producer', 'exporter', 'user'])->latest()->get(),
        ]);
    }
}
