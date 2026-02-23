<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Domain\Models\BusinessMatch;
use App\Domain\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Fetch messages for a specific match.
     */
    public function index(Request $request, BusinessMatch $match)
    {
        $user = $request->user();

        // Authorization: User must be part of the match
        $isExporter = $user->company->exporter && $match->exporter_id === $user->company->exporter->id;
        $isProducer = $user->company->producer && $match->producer_id === $user->company->producer->id;

        if (!$isExporter && !$isProducer) {
            abort(403);
        }

        return response()->json([
            'messages' => $match->messages()
                ->with('sender')
                ->latest()
                ->get()
        ]);
    }

    /**
     * Store a new message.
     */
    public function store(Request $request, BusinessMatch $match)
    {
        $user = $request->user();

        // Authorization
        $isExporter = $user->company->exporter && $match->exporter_id === $user->company->exporter->id;
        $isProducer = $user->company->producer && $match->producer_id === $user->company->producer->id;

        if (!$isExporter && !$isProducer) {
            abort(403);
        }

        $request->validate([
            'content' => 'required_without:attachment',
            'attachment' => 'nullable|file|max:10240', // 10MB max
            'type' => 'required|in:text,image,file,link',
        ]);

        $message = null;
        DB::transaction(function () use ($request, $match, $user, &$message) {
            $path = null;
            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('messages', 'public');
            }

            $message = $match->messages()->create([
                'sender_id' => $user->id,
                'content' => $request->input('content'),
                'attachment_path' => $path,
                'type' => $request->input('type'),
            ]);
            
            // Touch the match to update 'updated_at' for sorting in lists
            $match->touch();
        });

        // Notify the other party
        $isExporter = $user->company->exporter && $match->exporter_id === $user->company->exporter->id;
        $targetUser = $isExporter
            ? $match->producer->company->user
            : $match->exporter->company->user;

        if ($targetUser && $message) {
            $snippet = $message->content 
                ? \Illuminate\Support\Str::limit($message->content, 50) 
                : ucfirst($message->type) . ' attached';
            $targetUser->notify(new \App\Notifications\NewChatMessageNotification($match, $user->company->name, $snippet));
        }

        // In a real implementation, we would broadcast an event here for realtime updates.
        // For now, we return success and let the frontend poll or reload.

        return back();
    }
}
