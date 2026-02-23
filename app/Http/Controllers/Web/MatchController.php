<?php

namespace App\Http\Controllers\Web;

use App\Domain\Models\BusinessMatch;
use App\Domain\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    public function create(Request $request, Company $company)
    {
        $user = $request->user();
        
        if (!$user->company) {
            return redirect()->route('dashboard')->with('error', 'You need a company profile to initiate connections.');
        }

        $initiatorType = $user->company->type;
        $targetType = $company->type;

        if (($initiatorType === 'exporter' && $targetType === 'exporter') || ($initiatorType === 'producer' && $targetType === 'producer')) {
            return redirect()->route('dashboard')->with('error', 'You can only connect with companies of complementary types.');
        }

        // Allow multiple connections to the same company by removing the $exists check

        // If the initiator is an exporter, they are selecting the producer's (target's) products
        if ($initiatorType === 'exporter' || ($initiatorType === 'both' && $targetType === 'producer')) {
            $productsField = $company->products();
        } else {
            $productsField = $user->company->products();
        }

        $producerProducts = $productsField
            ->select('id', 'name', 'primary_image')
            ->where('status', 'active')
            ->whereIn('visibility', ['public', 'partners_only'])
            ->get();

        return Inertia::render('Matches/Create', [
            'targetCompany' => $company->load($targetType === 'exporter' ? 'exporter' : 'producer'),
            'myProducts' => $producerProducts,
        ]);
    }

    public function store(Request $request, Company $company)
    {
        $request->validate([
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'tentative_date' => 'required|date|after:today',
            'message' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
        ]);

        $user = $request->user();
        $initiatorType = $user->company->type;
        $targetType = $company->type;

        $producerId = null;
        $exporterId = null;

        if ($initiatorType === 'exporter' || ($initiatorType === 'both' && $targetType === 'producer')) {
            $producerId = $company->producer->id;
            $exporterId = $user->company->exporter->id;
        } else {
            $producerId = $user->company->producer->id;
            $exporterId = $company->exporter->id;
        }

        $initiatorId = $initiatorType === 'exporter' ? $exporterId : $producerId;

        // Transactional creation
        DB::transaction(function () use ($request, $producerId, $exporterId, $initiatorType, $initiatorId, $user, $company) {
            $match = BusinessMatch::create([
                'producer_id' => $producerId,
                'exporter_id' => $exporterId,
                'initiator_type' => $initiatorType,
                'initiator_id' => $initiatorId,
                'status' => 'pending',
                'origin' => $request->origin,
                'destination' => $request->destination,
                'tentative_date' => $request->tentative_date,
                'message' => $request->message,
                'products' => $request->products, // casted to json automatically
            ]);

            if ($company->user) {
                $company->user->notify(new \App\Notifications\ConnectionRequestReceivedNotification($match, $user->company->name));
            }
        });

        return redirect()->route('dashboard')->with('success', 'Connection request sent successfully!');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        if (!$user->company) {
             return redirect()->route('dashboard');
        }

        $query = BusinessMatch::query();

        // If Exporter (showing received requests)
        // User requested: "rejected requests remain hidden from the list"
        if ($user->company->type === 'exporter' || ($user->company->type === 'both' && !$request->query('sent'))) {
            if ($request->boolean('archived')) {
                $query->onlyTrashed();
            } else {
                $query->where('status', '!=', 'rejected'); // Hide rejected normally
            }

            $requests = $query->with(['producer.company'])
                ->where('exporter_id', $user->company->exporter->id)
                ->latest()
                ->paginate(10);
                
            return Inertia::render('Matches/Index', [
                'requests' => $requests,
                'type' => 'received',
                'archived' => $request->boolean('archived'),
            ]);
        }
        
        // If Producer (showing sent requests)
        if ($user->company->type === 'producer' || ($user->company->type === 'both' && $request->query('sent'))) {
             if ($request->boolean('archived')) {
                  // For producer, archived means either deleted manually OR rejected (which they might want to see in archive)
                  // But SoftDeletes only handles deleted_at. 
                  // If user wants to see "archived/deleted", we show deleted ones.
                  // Wait, earlier request was: "Al archivarlas [rejected] se ocultaran de la lista" -> implying delete = archive.
                  $query->onlyTrashed();
             }

             $requests = $query->with(['exporter.company'])
                ->where('producer_id', $user->company->producer->id)
                ->latest()
                ->paginate(10);

            return Inertia::render('Matches/Index', [
                'requests' => $requests,
                'type' => 'sent',
                'archived' => $request->boolean('archived'),
            ]);
        }

        return redirect()->route('dashboard');
    }

    public function show(Request $request, BusinessMatch $match)
    {
        $user = $request->user();
        
        // Authorization check (Viewer must be either the exporter or the producer)
        $isExporter = $user->company->exporter && $match->exporter_id === $user->company->exporter->id;
        $isProducer = $user->company->producer && $match->producer_id === $user->company->producer->id;

        if (!$isExporter && !$isProducer) {
            abort(403);
        }

        // Mark as read if not already AND if it is the exporter viewing
        if ($isExporter && !$match->is_read) {
            $match->update(['is_read' => true]);
        }

        $match->load(['producer.company', 'exporter.company']);
        
        // Fetch product details for the products in the request
        $products = \App\Domain\Models\Product::whereIn('id', $match->products ?? [])->get();

        if ($match->status === 'accepted' || in_array($match->status, ['in_progress', 'completed', 'cancelled'])) {
             // Load timeline
             $match->load(['timelines.user', 'timelines.user.company']);

             return Inertia::render('Matches/Workspace', [
                'match' => $match,
                'products' => $products,
            ]);
        }

        // Determine if the current user is the receiver
        $isReceiver = false;
        
        // If initiator is null (old data), fallback to assuming producer initiated 
        // because previously only producers could initiate.
        $actualInitiatorType = $match->initiator_type ?? 'producer';
        $actualInitiatorId = $match->initiator_id ?? $match->producer_id;

        if ($actualInitiatorType === 'producer') {
            // Producer initiated, so Exporter is the receiver
            if ($isExporter && $user->company->exporter->id === $match->exporter_id) {
                $isReceiver = true;
            }
        } else {
            // Exporter initiated, so Producer is the receiver
            if ($isProducer && $user->company->producer->id === $match->producer_id) {
                $isReceiver = true;
            }
        }

        return Inertia::render('Matches/Show', [
            'match' => $match,
            'products' => $products,
            'isReceiver' => $isReceiver,
        ]);
    }

    public function edit(Request $request, BusinessMatch $match)
    {
        $user = $request->user();
        
        // Only producer can edit
        if ($match->producer_id !== $user->company->producer->id) {
            abort(403);
        }

        if ($match->status !== 'pending') {
            return redirect()->route('matches.index')->with('error', 'Cannot edit processed requests.');
        }

        return Inertia::render('Matches/Edit', [
            'match' => $match,
             // pass countries again for the dropdown if needed, or fetch in component
        ]);
    }

    public function update(Request $request, BusinessMatch $match)
    {
         $user = $request->user();
        
        if ($match->producer_id !== $user->company->producer->id) {
            abort(403);
        }

        if ($match->status !== 'pending') {
            abort(403, 'Cannot update processed requests.');
        }

        $request->validate([
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'tentative_date' => 'required|date|after:today',
            'message' => 'nullable|string|max:1000',
        ]);

        $match->update([
            'origin' => $request->origin,
            'destination' => $request->destination,
            'tentative_date' => $request->tentative_date,
            'message' => $request->message,
        ]);

        return redirect()->route('matches.index', ['sent' => true])->with('success', 'Request updated successfully.');
    }

    public function destroy(Request $request, BusinessMatch $match)
    {
        $user = $request->user();
        
        $isExporter = $user->company->exporter && $match->exporter_id === $user->company->exporter->id;
        $isProducer = $user->company->producer && $match->producer_id === $user->company->producer->id;

        $actualInitiatorType = $match->initiator_type ?? 'producer';
        $actualInitiatorId = $match->initiator_id ?? $match->producer_id;

        $isInitiator = false;
        if ($actualInitiatorType === 'producer' && $isProducer && $user->company->producer->id === $actualInitiatorId) {
            $isInitiator = true;
        } elseif ($actualInitiatorType === 'exporter' && $isExporter && $user->company->exporter->id === $actualInitiatorId) {
            $isInitiator = true;
        }

        if (!$isInitiator) {
            abort(403, 'Only the sender can cancel this request.');
        }

        if ($match->status === 'accepted') {
            return back()->with('error', 'Cannot delete accepted requests.');
        }

        $match->delete();

        return redirect()->route('matches.index', ['sent' => true])->with('success', 'Request deleted successfully.');
    }

    /**
     * Reject the specified resource.
     */
    public function reject(Request $request, BusinessMatch $match)
    {
        $user = $request->user();
        
        $isExporter = $user->company->exporter && $match->exporter_id === $user->company->exporter->id;
        $isProducer = $user->company->producer && $match->producer_id === $user->company->producer->id;

        $actualInitiatorType = $match->initiator_type ?? 'producer';
        $actualInitiatorId = $match->initiator_id ?? $match->producer_id;

        $isReceiver = false;
        if ($actualInitiatorType === 'producer' && $isExporter && $user->company->exporter->id === $match->exporter_id) {
            $isReceiver = true;
        } elseif ($actualInitiatorType === 'exporter' && $isProducer && $user->company->producer->id === $match->producer_id) {
            $isReceiver = true;
        }

        if (!$isReceiver) {
             abort(403, 'Only the receiver can reject this request.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $match->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'is_read' => true
        ]);

        $initiatorUser = $actualInitiatorType === 'producer' 
            ? $match->producer->company->user 
            : $match->exporter->company->user;

        if ($initiatorUser) {
            $initiatorUser->notify(new \App\Notifications\ConnectionRequestRejectedNotification($match, $user->company->name));
        }

        return redirect()->back()->with('success', 'Request rejected successfully.');
    }
    public function accept(Request $request, BusinessMatch $match)
    {
        $user = $request->user();
        
        $isExporter = $user->company->exporter && $match->exporter_id === $user->company->exporter->id;
        $isProducer = $user->company->producer && $match->producer_id === $user->company->producer->id;

        $actualInitiatorType = $match->initiator_type ?? 'producer';

        $isReceiver = false;
        if ($actualInitiatorType === 'producer' && $isExporter && $user->company->exporter->id === $match->exporter_id) {
            $isReceiver = true;
        } elseif ($actualInitiatorType === 'exporter' && $isProducer && $user->company->producer->id === $match->producer_id) {
            $isReceiver = true;
        }

        if (!$isReceiver) {
             abort(403, 'Only the receiver can accept this request.');
        }

        $match->update([
            'status' => 'accepted',
            'is_read' => true
        ]);
        
        // Initial timeline entry
        $match->timelines()->create([
             'user_id' => $request->user()->id,
             'previous_status' => 'pending',
             'new_status' => 'accepted',
             'notes' => 'Match accepted by exporter.',
        ]);

        $initiatorUser = $actualInitiatorType === 'producer' 
            ? $match->producer->company->user 
            : $match->exporter->company->user;

        if ($initiatorUser) {
            $initiatorUser->notify(new \App\Notifications\ConnectionRequestAcceptedNotification($match, $user->company->name));
        }

        return redirect()->back()->with('success', 'Request accepted. You are now partners!');
    }

    public function updateStatus(Request $request, BusinessMatch $match)
    {
        $user = $request->user();
        
        // Authorization: must be participant
        $isExporter = $user->company->exporter && $match->exporter_id === $user->company->exporter->id;
        $isProducer = $user->company->producer && $match->producer_id === $user->company->producer->id;
        
        if (!$isExporter && !$isProducer) {
            abort(403);
        }
        
        $validated = $request->validate([
            'status' => 'required|string|in:accepted,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $oldStatus = $match->status;
        $newStatus = $validated['status'];
        
        if ($oldStatus === $newStatus) {
            return back();
        }

        DB::transaction(function() use ($match, $user, $oldStatus, $newStatus, $validated) {
            $match->update(['status' => $newStatus]);
            
            $match->timelines()->create([
                'user_id' => $user->id,
                'previous_status' => $oldStatus,
                'new_status' => $newStatus,
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        $isExporter = $user->company->exporter && $match->exporter_id === $user->company->exporter->id;
        $targetUser = $isExporter
            ? $match->producer->company->user
            : $match->exporter->company->user;

        if ($targetUser) {
            $targetUser->notify(new \App\Notifications\ConnectionRequestStatusChangedNotification($match, $user->company->name, $newStatus));
        }
        
        return back()->with('success', 'Status updated successfully.');
    }
}
