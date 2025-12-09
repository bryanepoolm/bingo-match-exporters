<?php

namespace App\Http\Controllers\Web;

use App\Domain\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Domain\Models\BusinessMatch;

class ExplorerController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::query()
            ->with(['exporter.capabilities'])
            ->when($request->user()->company, function ($query) use ($request) {
                // Exclude current user's company
                return $query->where('id', '!=', $request->user()->company->id);
            })
            ->latest()
            ->paginate(12);

        return Inertia::render('Explorer/Index', [
            'companies' => $companies,
        ]);
    }

    public function show(Request $request, Company $company)
    {
        $company->load(['producer', 'exporter.capabilities']);
        
        // Load visible products separately to filter
        $products = $company->products()
            ->where('visibility', 'public')
            ->latest()
            ->get();

        $existingMatch = null;
        $user = $request->user();
        
        if ($user && $user->company && $user->company->producer && $company->exporter) {
            $existingMatch = BusinessMatch::where('producer_id', $user->company->producer->id)
                ->where('exporter_id', $company->exporter->id)
                ->first();
        }

        return Inertia::render('Explorer/Show', [
            'company' => $company,
            'products' => $products,
            'existingMatch' => $existingMatch,
        ]);
    }
}
