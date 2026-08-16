<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\JsonResponse;

class BlogApiController extends Controller
{
    /**
     * Return paginated published blog posts as JSON.
     */
    public function index(): JsonResponse
    {
        $perPage = (int) request()->query('per_page', 12);
        $search = request()->query('search');

        $query = Blog::published();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                  ->orWhere('excerpt', 'like', '%'.$search.'%')
                  ->orWhere('author_name', 'like', '%'.$search.'%');
            });
        }

        $blogs = $query
            ->orderBy('published_at', 'desc')
            ->paginate(min($perPage, 50));

        $data = $blogs->map(function (Blog $blog) {
            return $this->formatBlog($blog);
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $blogs->currentPage(),
                'last_page' => $blogs->lastPage(),
                'per_page' => $blogs->perPage(),
                'total' => $blogs->total(),
            ],
        ]);
    }

    /**
     * Return a single blog post by slug.
     */
    public function show(string $slug): JsonResponse
    {
        $blog = Blog::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Blog::published()
            ->where('id', '!=', $blog->id)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get()
            ->map(fn (Blog $b) => $this->formatBlog($b));

        return response()->json([
            'data' => $this->formatBlog($blog),
            'related' => $related,
        ]);
    }

    /**
     * Format a Blog model into the shape the frontend expects.
     */
    protected function formatBlog(Blog $blog): array
    {
        $meta = $blog->meta_data ?? [];
        $categories = $meta['categories'] ?? [];
        $category = ! empty($categories) ? strtoupper($categories[0]) : 'STORIES';

        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'excerpt' => strip_tags($blog->excerpt),
            'content' => $blog->content,
            'date' => $blog->published_at?->toDateString() ?? $blog->created_at->toDateString(),
            'featured_image' => $this->getBlogImageUrl($blog),
            'category' => $category,
            'author_name' => $blog->author_name ?? 'Pakka Patriot',
            'read_time' => $blog->reading_time . ' min read',
            'link' => url('/' . $blog->slug),
        ];
    }

    /**
     * Get the absolute image URL for a blog post so the cross-origin
     * React frontend can display it (relative paths only resolve on
     * the API origin, not on the deployed frontend host).
     */
    protected function getBlogImageUrl(Blog $blog): string
    {
        try {
            $media = $blog->getFirstMedia('featured_image');
            if ($media) {
                return $media->getUrl();
            }
        } catch (\Throwable $e) {
            // Fall through
        }

        // Use the original WordPress URL if available, or a placeholder
        return $blog->featured_image
            ?: 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=800&auto=format&fit=crop';
    }
}
