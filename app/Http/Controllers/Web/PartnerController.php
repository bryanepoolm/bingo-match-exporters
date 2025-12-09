<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Domain\Models\BusinessMatch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $userCompany = $user->company;

        if (!$userCompany) {
            return to_route('company.create');
        }
        
        $partners = [];
        
        if ($userCompany->type === 'exporter') {
             // Find producers where: 
             // 1. match.exporter_id is mine
             // 2. status is accepted
             // User wants: list of partners + counter of requests
             
             // We can get all accepted matches, group by producer.
             // Or better, query Producers who have at least one accepted match with me.
             // But simpler for now: Get unique partner IDs from accepted matches, then fetch details & counts.
             
             // Let's use Eloquent to get distinct producers from Accepted matches
             $matches = BusinessMatch::where('exporter_id', $userCompany->exporter->id)
                ->where('status', 'accepted')
                ->with('producer.company')
                ->get();
             
             $grouped = $matches->groupBy('producer_id');
             
             foreach ($grouped as $producerId => $partnerMatches) {
                 $firstMatch = $partnerMatches->first();
                 $producer = $firstMatch->producer;
                 
                  // Total requests count (including pending/rejected/archived? User said "counter of requests they have with you")
                  // Let's count ALL matches with this partner
                 $totalRequests = BusinessMatch::where('exporter_id', $userCompany->exporter->id)
                    ->where('producer_id', $producerId)
                    ->count();

                 $partners[] = [
                     'id' => $producer->id, // Partner ID (Producer ID)
                     'company_name' => $producer->company->name,
                     'logo_path' => $producer->company->logo_path,
                     'type' => 'Producer',
                     'match_count' => $totalRequests,
                     'accepted_count' => $partnerMatches->count(), // How many accepted deals
                     'latest_activity' => $partnerMatches->max('updated_at'),
                 ];
             }
        } elseif ($userCompany->type === 'producer') {
             $matches = BusinessMatch::where('producer_id', $userCompany->producer->id)
                ->where('status', 'accepted')
                ->with('exporter.company')
                ->get();

             $grouped = $matches->groupBy('exporter_id');
             
             foreach ($grouped as $exporterId => $partnerMatches) {
                 $firstMatch = $partnerMatches->first();
                 $exporter = $firstMatch->exporter;

                 $totalRequests = BusinessMatch::where('producer_id', $userCompany->producer->id)
                    ->where('exporter_id', $exporterId)
                    ->count();

                 $partners[] = [
                     'id' => $exporter->id,
                     'company_name' => $exporter->company->name,
                     'logo_path' => $exporter->company->logo_path,
                     'type' => 'Exporter',
                     'match_count' => $totalRequests,
                     'accepted_count' => $partnerMatches->count(),
                     'latest_activity' => $partnerMatches->max('updated_at'),
                 ];
             }
        }
        
        return Inertia::render('Partners/Index', [
            'partners' => $partners
        ]);
    }
}
