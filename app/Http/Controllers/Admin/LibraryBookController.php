<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Services\Library\LibraryBookService;
use App\Services\Library\LibraryCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LibraryBookController extends Controller
{
    public function __construct(
        private LibraryBookService $books,
        private LibraryCatalogService $catalog,
    ) {}

    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();
        $categoryId = $request->integer('category') ?: null;

        $query = LibraryBook::query()
            ->with(['category.parent.parent', 'uploader'])
            ->orderByDesc('updated_at');

        if ($q !== '') {
            $term = '%'.$q.'%';
            $query->where(function ($builder) use ($term) {
                $builder->where('title', 'like', $term)
                    ->orWhere('author', 'like', $term);
            });
        }

        if ($categoryId) {
            $ids = $this->catalog->descendantCategoryIdsIncludingSelf($categoryId);
            $query->whereIn('library_category_id', $ids);
        }

        $books = $query->paginate(20)->withQueryString();

        $books->getCollection()->transform(function (LibraryBook $book) {
            $book->setAttribute('category_path', $book->category
                ? $this->catalog->breadcrumb($book->category)
                : '');

            return $book;
        });

        return view('admin.library.books.index', [
            'books' => $books,
            'leafOptions' => $this->catalog->leafOptionsForAdmin(),
            'filters' => [
                'q' => $q,
                'category' => $categoryId,
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.library.books.form', [
            'book' => new LibraryBook(['is_published' => true]),
            'leafOptions' => $this->catalog->leafOptionsForAdmin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request, requireFile: true, requireCover: true);
        $this->books->store(
            $validated,
            $request->file('file'),
            $request->file('cover'),
            $request->user(),
        );

        return redirect()
            ->route('admin.library.books.index')
            ->with('toast_success', __('talenma.admin.library.book_saved'));
    }

    public function edit(LibraryBook $book): View
    {
        return view('admin.library.books.form', [
            'book' => $book,
            'leafOptions' => $this->catalog->leafOptionsForAdmin(),
        ]);
    }

    public function update(Request $request, LibraryBook $book): RedirectResponse
    {
        $validated = $this->validated($request, requireFile: false, requireCover: false);
        $this->books->update(
            $book,
            $validated,
            $request->file('file'),
            $request->file('cover'),
        );

        return redirect()
            ->route('admin.library.books.edit', $book)
            ->with('toast_success', __('talenma.admin.library.book_saved'));
    }

    public function destroy(LibraryBook $book): RedirectResponse
    {
        $this->books->delete($book);

        return redirect()
            ->route('admin.library.books.index')
            ->with('toast_success', __('talenma.admin.library.book_deleted'));
    }

    /**
     * @return array{library_category_id:int,title:string,author:?string,description:?string,is_published:bool}
     */
    private function validated(Request $request, bool $requireFile, bool $requireCover): array
    {
        $rules = [
            'library_category_id' => ['required', 'integer', 'exists:library_categories,id'],
            'title' => ['required', 'string', 'max:200'],
            'author' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_published' => ['sometimes', 'boolean'],
            'file' => [
                Rule::requiredIf($requireFile),
                'nullable',
                'file',
                'mimes:pdf',
                'max:51200',
            ],
            'cover' => [
                Rule::requiredIf($requireCover),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];

        $validated = $request->validate($rules, [
            'file.required' => __('talenma.admin.library.file_required'),
            'file.mimes' => __('talenma.admin.library.file_mimes'),
            'file.max' => __('talenma.admin.library.file_max'),
            'cover.required' => __('talenma.admin.library.cover_required'),
            'cover.mimes' => __('talenma.admin.library.cover_mimes'),
            'cover.max' => __('talenma.admin.library.cover_max'),
        ]);

        $validated['is_published'] = $request->boolean('is_published', true);

        unset($validated['file'], $validated['cover']);

        return $validated;
    }
}
