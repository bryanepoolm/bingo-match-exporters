<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function create()
    {
        return Inertia::render('Company/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tax_id' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:producer,exporter,logistics,both',
        ]);

        $company = $request->user()->company()->create($validated);

        if (in_array($validated['type'], ['producer', 'both'])) {
            $company->producer()->create([]);
        }

        if (in_array($validated['type'], ['exporter', 'both'])) {
            $company->exporter()->create([]);
        }

        return redirect()->route('dashboard');
    }

    public function edit(Request $request)
    {
        $company = $request->user()->company;

        if (!$company) {
            return redirect()->route('company.create');
        }

        // Eager load exporter capabilities if the company is an exporter
        if ($company->exporter) {
            $company->load('exporter.capabilities');
        }

        $capabilities = \App\Domain\Models\Capability::all();

        return Inertia::render('Company/Edit', [
            'company' => $company,
            'capabilities' => $capabilities,
        ]);
    }

    public function update(Request $request)
    {
        $company = $request->user()->company;

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'tax_id' => 'sometimes|required|string|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|required|in:producer,exporter,logistics,both',
            'logo' => 'nullable|image|max:2048', // 2MB max
            'verification_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($company->logo_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($company->logo_path);
            }

            $path = $request->file('logo')->store('company-logos', 'public');
            $validated['logo_path'] = $path;
        }

        // Handle File Uploads
        if ($request->hasFile('verification_documents')) {
            $documents = $company->verification_documents ?? [];
            
            foreach ($request->file('verification_documents') as $file) {
                $originalName = $file->getClientOriginalName();
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
                $path = $file->storeAs('company-documents', $filename, 'public');
                $documents[] = $path;
            }

            $validated['verification_documents'] = $documents;
        }

        $company->update($validated);

        // Sync Capabilities if Exporter
        if ($request->has('capabilities') && ($company->type === 'exporter' || $company->type === 'both')) {
             // Ensure exporter record exists
            $exporter = $company->exporter;
            if (!$exporter) {
                $exporter = $company->exporter()->create([]);
            }
            
            $exporter->capabilities()->sync($request->input('capabilities'));
        }

        return redirect()->route('company.edit')->with('success', 'Profile updated successfully.');
    }

    public function destroyDocument(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $company = $request->user()->company;
        $pathToRemove = $request->path;
        $documents = $company->verification_documents ?? [];

        // Remove from array
        $updatedDocuments = array_values(array_filter($documents, function ($doc) use ($pathToRemove) {
            return $doc !== $pathToRemove;
        }));

        // Delete file from storage if it exists
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($pathToRemove)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($pathToRemove);
        }

        // Update company
        $company->verification_documents = $updatedDocuments;
        $company->save();

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }
}
