<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class OnboardingController extends Controller
{
    public function index()
    {
        return Inertia::render('Onboarding/Wizard');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tax_id' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:producer,exporter,logistics',
        ]);

        $company = $request->user()->company()->create($validated);

        if ($validated['type'] === 'producer') {
            $company->producer()->create([]);
        }

        if ($validated['type'] === 'exporter') {
            $company->exporter()->create([]);
        }
        
        // Logistics logic can be added here if needed

        return redirect()->route('dashboard');
    }
}
