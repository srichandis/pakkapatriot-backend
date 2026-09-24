<?php

namespace App\Services;

use App\Models\Blog;
use Illuminate\Support\Collection;

/**
 * Builds the blog card/detail arrays consumed by the Blade front-end and the
 * JSON API, so both render identical data.
 */
class BlogFeed
{
    /**
     * Newest published posts, formatted for cards.
     *
     * @return array<int, array<string, mixed>>
     */
    public function latest(int $limit = 12): array
    {
        return $this->formatMany(
            Blog::published()->orderByDesc('published_at')->take($limit)->get()
        );
    }

    /**
     * Paginated published posts, optionally filtered by free text and the
     * post's first category (the value shown as its badge).
     *
     * `$category` must be the raw stored value (e.g. "Freedom Fighters", not
     * "FREEDOM FIGHTERS") — see categories().
     */
    public function paginate(int $perPage = 24, ?string $search = null, ?string $category = null)
    {
        $query = Blog::published();

        if ($category) {
            $query->whereJsonContains('meta_data->categories', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('excerpt', 'like', '%'.$search.'%')
                    ->orWhere('author_name', 'like', '%'.$search.'%');
            });
        }

        return $query->orderByDesc('published_at')->paginate($perPage)->withQueryString();
    }

    /**
     * Categories used by published posts, most used first.
     *
     * @return array<int, array{label: string, value: string, count: int}>
     */
    public function categories(): array
    {
        $counts = [];

        foreach (Blog::published()->get(['id', 'meta_data']) as $blog) {
            $category = $this->rawCategory($blog);

            if ($category === null) {
                continue;
            }

            $counts[$category] = ($counts[$category] ?? 0) + 1;
        }

        arsort($counts);

        return collect($counts)
            ->map(fn (int $count, string $value) => [
                'label' => mb_strtoupper($value),
                'value' => $value,
                'count' => $count,
            ])
            ->values()
            ->all();
    }

    /**
     * Total published posts, and the ones with no category of their own.
     */
    public function publishedCount(): int
    {
        return Blog::published()->count();
    }

    /**
     * The raw first category stored on a post, or null when it has none.
     */
    protected function rawCategory(Blog $blog): ?string
    {
        $categories = ($blog->meta_data ?? [])['categories'] ?? [];
        $first = is_array($categories) ? reset($categories) : null;

        return is_string($first) && trim($first) !== '' ? trim($first) : null;
    }

    /**
     * A single published post by slug.
     */
    public function find(string $slug): ?array
    {
        $blog = Blog::published()->where('slug', $slug)->first();

        return $blog ? $this->format($blog) : null;
    }

    /**
     * @param  Collection<int, Blog>  $blogs
     * @return array<int, array<string, mixed>>
     */
    public function formatMany(Collection $blogs): array
    {
        return $blogs->map(fn (Blog $blog) => $this->format($blog))->all();
    }

    /**
     * Format a Blog model into the front-end shape.
     *
     * @return array<string, mixed>
     */
    public function format(Blog $blog): array
    {
        $rawCategory = $this->rawCategory($blog);
        $category = $rawCategory ? mb_strtoupper($rawCategory) : 'STORIES';

        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'excerpt' => strip_tags($blog->excerpt),
            'content' => $blog->content,
            'date' => ($blog->published_at ?? $blog->created_at)?->toDateString(),
            'featured_image' => $this->imageUrl($blog),
            'category' => $category,
            'category_value' => $rawCategory,
            'author_name' => $blog->author_name ?? 'Pakka Patriot',
            'read_time' => $blog->reading_time.' min read',
            'link' => url('/'.$blog->slug),
        ];
    }

    /**
     * Featured image URL for a post.
     */
    protected function imageUrl(Blog $blog): string
    {
        try {
            $media = $blog->getFirstMedia('featured_image');
            if ($media) {
                return $media->getUrl();
            }
        } catch (\Throwable $e) {
            // Fall through to the stored/placeholder URL.
        }

        return $blog->featured_image
            ?: 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=800&auto=format&fit=crop';
    }
}
