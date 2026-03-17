<?php

namespace App\Http\Controllers;

use App\Domain\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $exporter = $request->user()->company->exporter;
        
        if (!$exporter) {
            abort(403, 'Only exporters can manage services.');
        }

        $services = Service::where('exporter_id', $exporter->id)
            ->latest()
            ->paginate(10);

        return Inertia::render('Service/Index', [
            'services' => $services,
        ]);
    }

    public function create()
    {
        return Inertia::render('Service/Create');
    }

    public function store(Request $request)
    {
        $exporter = $request->user()->company->exporter;
         if (!$exporter) {
            abort(403, 'Only exporters can create services.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'weight_limit' => 'nullable|string|max:255',
            'destinations' => 'nullable|array',
            'status' => 'required|in:active,inactive,draft',
        ]);

        $validated['exporter_id'] = $exporter->id;
        $validated['company_id'] = $request->user()->company->id;

        Service::create($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        if (request()->user()->company->exporter->id !== $service->exporter_id) {
            abort(403);
        }
        
        return Inertia::render('Service/Edit', [
            'service' => $service,
        ]);
    }

    public function update(Request $request, Service $service)
    {
        if ($request->user()->company->exporter->id !== $service->exporter_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'weight_limit' => 'nullable|string|max:255',
            'destinations' => 'nullable|array',
            'status' => 'required|in:active,inactive,draft',
        ]);

        $service->update($validated);

        return redirect()->back()
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        if (request()->user()->company->exporter->id !== $service->exporter_id) {
            abort(403);
        }

        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully.');
    }
}
