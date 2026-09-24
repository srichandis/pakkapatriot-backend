<?php

namespace App\Http\Controllers;

use App\Models\EBook;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EBooksController extends Controller
{
    /**
     * The four eBook categories are a fixed editorial taxonomy.
     *
     * @var array<int, string>
     */
    public const CATEGORIES = ['Freedom Fighters', 'Poets', 'Scientists', 'Saints'];

    /**
     * Free eBook library with search and category filtering.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));

        $query = EBook::query();

        if (in_array($category, self::CATEGORIES, true)) {
            $query->where('category', $category);
        } else {
            $category = '';
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('subtitle', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhere('era', 'like', '%'.$search.'%');
            });
        }

        $books = $query->orderBy('title')->get();

        return view('ebooks.index', [
            'books' => $books,
            'categories' => collect(self::CATEGORIES)->map(fn (string $name) => [
                'label' => $name,
                'count' => EBook::where('category', $name)->count(),
            ])->all(),
            'total' => EBook::count(),
            'search' => $search,
            'category' => $category,
        ]);
    }
}
