<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function destroy(Company $company)
    {
        DB::transaction(function () use ($company) {
            // Delete related products and posts
            $company->products()->delete();
            $company->posts()->delete();

            // Delete specific profile (Producer/Exporter)
            if ($company->producer) {
                $company->producer->delete();
            }
            if ($company->exporter) {
                $company->exporter->delete();
            }

            // Delete the user associated with the company
            if ($company->user) {
                $company->user->delete();
            }

            // Finally, delete the company itself
            // Matches will remain but their relationships will return null/deleted models (if withTrashed is used)
            // Or just null if standard query.
            $company->delete();
        });

        return redirect()->back()->with('success', 'Company deleted successfully.');
    }
}
