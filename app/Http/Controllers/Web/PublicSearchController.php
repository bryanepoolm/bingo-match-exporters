<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Domain\Models\Company;
use Illuminate\Http\Request;

class PublicSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $companies = Company::where(function($queryGroup) use ($query) {
                $queryGroup->where('name', 'like', "%{$query}%")
                           ->orWhere('description', 'like', "%{$query}%")
                           ->orWhereHas('products', function ($q) use ($query) {
                               $q->where('name', 'like', "%{$query}%")
                                 ->orWhere('tags', 'like', "%{$query}%");
                           });
            })
            ->with(['products' => function($q) {
                $q->select('id', 'company_id', 'name', 'tags');
            }])
            ->select('id', 'name', 'description', 'logo_path')
            ->limit(5)
            ->get();

        return response()->json($companies);
    }
}
