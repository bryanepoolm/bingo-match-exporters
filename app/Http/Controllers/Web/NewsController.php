<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Domain\Models\Post;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

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

        return Inertia::render('News/Index', [
            'posts' => $posts
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

        $request->user()->company->posts()->create([
            'content' => $request->input('content'),
            'image_path' => $imagePath,
        ]);

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
        }

        return back();
    }

    public function comment(Request $request, Post $post)
    {
        $request->validate(['content' => 'required|string']);

        $post->comments()->create([
            'company_id' => $request->user()->company->id,
            'content' => $request->input('content')
        ]);

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
