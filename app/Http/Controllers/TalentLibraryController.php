<?php

namespace App\Http\Controllers;

use App\Models\LibraryBook;
use App\Services\Library\LibraryBookService;
use App\Services\Library\LibraryCatalogService;
use App\Support\LibraryBookStorage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TalentLibraryController extends Controller
{
    public function __construct(
        private LibraryCatalogService $catalog,
        private LibraryBookService $books,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->canAccessWorkspaceApps(), 403);

        $categoryId = $request->integer('category') ?: null;

        $books = $this->decorateBooks($this->catalog->searchPublished(null, $categoryId));

        if ($request->boolean('partial') || $request->ajax()) {
            return view('talent.library._results', [
                'books' => $books,
            ]);
        }

        $tree = $this->catalog->activeTree();
        $treePayload = $tree->map(fn ($root) => $this->serializeNode($root))->values()->all();

        return view('talent.library.index', [
            'books' => $books,
            'treePayload' => $treePayload,
            'filters' => [
                'category' => $categoryId,
            ],
        ]);
    }

    public function download(Request $request, LibraryBook $book): StreamedResponse
    {
        $user = $request->user();
        abort_unless($user->canAccessWorkspaceApps(), 403);
        abort_unless($book->is_published, 404);
        abort_unless(LibraryBookStorage::exists($book->file_path), 404);

        $this->books->incrementDownload($book);

        $downloadName = pathinfo($book->original_filename, PATHINFO_EXTENSION)
            ? $book->original_filename
            : $book->original_filename.'.pdf';

        return LibraryBookStorage::download($book->file_path, $downloadName);
    }

    /**
     * @param  LengthAwarePaginator<int, LibraryBook>  $books
     * @return LengthAwarePaginator<int, LibraryBook>
     */
    private function decorateBooks(LengthAwarePaginator $books): LengthAwarePaginator
    {
        $books->getCollection()->transform(function (LibraryBook $book) {
            $book->setAttribute('category_path', $book->category
                ? $this->catalog->breadcrumb($book->category)
                : '');

            return $book;
        });

        return $books;
    }

    /**
     * @return array{id:int,name:string,children:list<array>}
     */
    private function serializeNode($node): array
    {
        return [
            'id' => $node->id,
            'name' => $node->localizedName(),
            'children' => $node->children
                ->map(fn ($child) => $this->serializeNode($child))
                ->values()
                ->all(),
        ];
    }
}
