<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryCategory;
use App\Services\Library\LibraryCatalogService;
use App\Services\Library\LibraryCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LibraryCategoryController extends Controller
{
    public function __construct(
        private LibraryCategoryService $categories,
        private LibraryCatalogService $catalog,
    ) {}

    public function index(): View
    {
        $flat = LibraryCategory::query()
            ->withCount(['children', 'books'])
            ->with('parent')
            ->orderBy('depth')
            ->orderBy('sort_order')
            ->orderBy('name_fr')
            ->get();

        return view('admin.library.categories.index', [
            'categories' => $flat,
            'parentOptions' => LibraryCategory::query()
                ->where('depth', '<', LibraryCategory::MAX_DEPTH)
                ->orderBy('depth')
                ->orderBy('name_fr')
                ->get()
                ->map(fn (LibraryCategory $c) => [
                    'id' => $c->id,
                    'label' => $this->catalog->breadcrumb($c),
                    'depth' => $c->depth,
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_fr' => ['required', 'string', 'max:120'],
            'name_en' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140'],
            'parent_id' => ['nullable', 'integer', 'exists:library_categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $this->categories->create($validated);

        return redirect()
            ->route('admin.library.categories.index')
            ->with('toast_success', __('talenma.admin.library.category_saved'));
    }

    public function update(Request $request, LibraryCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name_fr' => ['required', 'string', 'max:120'],
            'name_en' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', $category->is_active);

        $this->categories->update($category, $validated);

        return redirect()
            ->route('admin.library.categories.index')
            ->with('toast_success', __('talenma.admin.library.category_saved'));
    }

    public function destroy(LibraryCategory $category): RedirectResponse
    {
        try {
            $this->categories->delete($category);
        } catch (ValidationException $e) {
            return redirect()
                ->route('admin.library.categories.index')
                ->withErrors($e->errors());
        }

        return redirect()
            ->route('admin.library.categories.index')
            ->with('toast_success', __('talenma.admin.library.category_deleted'));
    }
}
