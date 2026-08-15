<?php

namespace App\Http\Controllers;

use App\Models\Blog;

class BlogController extends Controller
{
    /**
     * Display a listing of published blog posts.
     */
    public function index()
    {
        $blogs = Blog::published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('blog.index', compact('blogs'));
    }

    /**
     * Display the specified blog post.
     *
     * Blog permalinks live at the root ({slug}). If the slug is not a
     * published blog post, fall back to the storefront proxy so legacy
     * category/product URLs at the root keep working.
     */
    public function show($slug)
    {
        $blog = Blog::published()
            ->where('slug', $slug)
            ->first();

        if (! $blog) {
            return app(\Webkul\Shop\Http\Controllers\ProductsCategoriesProxyController::class)->index(request());
        }

        // Get related posts (same author, or recent)
        $relatedPosts = Blog::published()
            ->where('id', '!=', $blog->id)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return view('blog.show', compact('blog', 'relatedPosts'));
    }
}
