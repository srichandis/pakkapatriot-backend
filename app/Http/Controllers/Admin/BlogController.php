<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Webkul\Admin\Http\Controllers\Controller;

class BlogController extends Controller
{
    /**
     * Display a listing of blog posts.
     */
    public function index()
    {
        $blogs = Blog::orderBy('created_at', 'desc')->paginate(20);

        return view('admin.blog.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new blog post.
     */
    public function create()
    {
        return view('admin.blog.create');
    }

    /**
     * Store a newly created blog post.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
            'slug' => 'nullable|unique:blogs,slug|max:255',
            'content' => 'required',
            'excerpt' => 'nullable|max:500',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'author_name' => 'nullable|max:255',
        ]);

        Event::dispatch('blog.create.before');

        $data = $request->only([
            'title',
            'slug',
            'excerpt',
            'content',
            'is_published',
            'published_at',
            'author_name',
            'meta_data',
        ]);

        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Set published_at if publishing but no date set
        if ($request->boolean('is_published') && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        // Set admin_id to current user
        $data['admin_id'] = auth()->guard('admin')->id();

        // Set author name from admin if not provided
        if (empty($data['author_name']) && auth()->guard('admin')->user()) {
            $data['author_name'] = auth()->guard('admin')->user()->name;
        }

        $blog = Blog::create($data);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $blog->addMedia($request->file('featured_image'))
                ->toMediaCollection('featured_image');
        }

        Event::dispatch('blog.create.after', $blog);

        session()->flash('success', trans('admin::app.cms.create-success'));

        return redirect()->route('admin.blogs.index');
    }

    /**
     * Show the form for editing the specified blog post.
     */
    public function edit($id)
    {
        $blog = Blog::findOrFail($id);

        return view('admin.blog.edit', compact('blog'));
    }

    /**
     * Update the specified blog post.
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $this->validate($request, [
            'title' => 'required|max:255',
            'slug' => 'nullable|max:255|unique:blogs,slug,'.$id,
            'content' => 'required',
            'excerpt' => 'nullable|max:500',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'author_name' => 'nullable|max:255',
        ]);

        Event::dispatch('blog.update.before', $id);

        $data = $request->only([
            'title',
            'slug',
            'excerpt',
            'content',
            'is_published',
            'published_at',
            'author_name',
            'meta_data',
        ]);

        // Auto-generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Set published_at if publishing but no date set
        if ($request->boolean('is_published') && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        // Unpublish: clear published_at
        if (! $request->boolean('is_published')) {
            $data['published_at'] = null;
        }

        $blog->update($data);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $blog->clearMediaCollection('featured_image');
            $blog->addMedia($request->file('featured_image'))
                ->toMediaCollection('featured_image');
        }

        Event::dispatch('blog.update.after', $blog);

        session()->flash('success', trans('admin::app.cms.update-success'));

        return redirect()->route('admin.blogs.index');
    }

    /**
     * Remove the specified blog post.
     */
    public function destroy($id)
    {
        try {
            Event::dispatch('blog.delete.before', $id);

            $blog = Blog::findOrFail($id);
            $blog->clearMediaCollection('featured_image');
            $blog->delete();

            Event::dispatch('blog.delete.after', $id);

            return response()->json([
                'message' => trans('admin::app.cms.delete-success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('admin::app.cms.no-resource'),
            ], 404);
        }
    }
}
