<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Domain\Models\Post;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

use App\Domain\Models\Event;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with(['company', 'comments.company', 'likes.company'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(10);
            
        $userCompanyId = $request->user()->company->id;
        
        $posts->getCollection()->transform(function ($post) use ($userCompanyId) {
            $post->is_liked_by_me = $post->likes->contains('company_id', $userCompanyId);
            return $post;
        });

        $events = Event::where('status', 'active')
            ->where(function ($query) {
                $query->whereDate('end_date', '>=', now())
                      ->orWhere(function ($q) {
                          $q->whereNull('end_date')
                            ->whereDate('start_date', '>=', now());
                      });
            })
            ->orderBy('start_date')
            ->get();

        return Inertia::render('News/Index', [
            'posts' => $posts,
            'events' => $events
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:10240',
        ]);

        if (!$request->input('content') && !$request->hasFile('image')) {
            return back()->withErrors(['content' => 'Please provide content or an image.']);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $company = $request->user()->company;
        $post = $company->posts()->create([
            'content' => $request->input('content'),
            'image_path' => $imagePath,
        ]);

        // Notify partners
        $partners = collect();
        if ($company->type === 'exporter' || $company->type === 'both') {
            $matches = \App\Domain\Models\BusinessMatch::where('exporter_id', $company->exporter->id)
                ->whereIn('status', ['accepted', 'completed', 'in_progress'])
                ->with('producer.company.user')
                ->get();
            foreach ($matches as $match) {
                if ($match->producer && $match->producer->company->user) {
                    $partners->push($match->producer->company->user);
                }
            }
        }
        if ($company->type === 'producer' || $company->type === 'both') {
            $matches = \App\Domain\Models\BusinessMatch::where('producer_id', $company->producer->id)
                ->whereIn('status', ['accepted', 'completed', 'in_progress'])
                ->with('exporter.company.user')
                ->get();
            foreach ($matches as $match) {
                if ($match->exporter && $match->exporter->company->user) {
                    $partners->push($match->exporter->company->user);
                }
            }
        }

        $partners = $partners->unique('id');
        $snippet = $post->content ? \Illuminate\Support\Str::limit($post->content, 50) : 'An image';

        foreach ($partners as $partnerUser) {
            $partnerUser->notify(new \App\Notifications\NewPartnerPostNotification($post, $company->name, $snippet));
        }

        return back();
    }

    public function like(Request $request, Post $post)
    {
        $companyId = $request->user()->company->id;
        $like = $post->likes()->where('company_id', $companyId)->first();

        if ($like) {
            $like->delete();
        } else {
            $post->likes()->create(['company_id' => $companyId]);
            
            if ($post->company_id !== $companyId && $post->company->user) {
                $likerName = $request->user()->company->name;
                $post->company->user->notify(new \App\Notifications\PostLikedNotification($post, $likerName));
            }
        }

        return back();
    }

    public function comment(Request $request, Post $post)
    {
        $request->validate(['content' => 'required|string']);

        $comment = $post->comments()->create([
            'company_id' => $request->user()->company->id,
            'content' => $request->input('content')
        ]);

        if ($post->company_id !== $request->user()->company->id && $post->company->user) {
            $commenterName = $request->user()->company->name;
            $post->company->user->notify(new \App\Notifications\NewCommentNotification($comment, $commenterName, $post));
        }

        return back();
    }

    public function update(Request $request, Post $post)
    {
        if ($request->user()->company->id !== $post->company_id) {
            abort(403);
        }

        $request->validate(['content' => 'required|string']);

        $post->update(['content' => $request->input('content')]);

        return back();
    }

    public function destroy(Request $request, Post $post)
    {
        if ($request->user()->company->id !== $post->company_id) {
            abort(403);
        }

        $post->delete();

        return back();
    }
}
