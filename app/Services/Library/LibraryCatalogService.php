<?php

namespace App\Services\Library;

use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LibraryCatalogService
{
    /**
     * @return Collection<int, LibraryCategory>
     */
    public function activeTree(): Collection
    {
        $categories = LibraryCategory::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name_fr')
            ->get();

        return $this->buildTree($categories);
    }

    /**
     * @return list<array{id:int,label:string,depth:int,path:string}>
     */
    public function leafOptionsForAdmin(): array
    {
        $leaves = LibraryCategory::query()
            ->leaves()
            ->with(['parent.parent'])
            ->orderBy('name_fr')
            ->get();

        return $leaves->map(function (LibraryCategory $leaf) {
            return [
                'id' => $leaf->id,
                'label' => $this->breadcrumb($leaf),
                'depth' => $leaf->depth,
                'path' => $this->breadcrumb($leaf),
            ];
        })->all();
    }

    public function breadcrumb(LibraryCategory $category, ?string $locale = null): string
    {
        $parts = [];
        $current = $category->relationLoaded('parent')
            ? $category
            : $category->loadMissing('parent.parent');

        while ($current) {
            array_unshift($parts, $current->localizedName($locale));
            $current = $current->parent;
        }

        return implode(' / ', $parts);
    }

    /**
     * @return LengthAwarePaginator<int, LibraryBook>
     */
    public function searchPublished(?string $query, ?int $categoryId, int $perPage = 10): LengthAwarePaginator
    {
        $builder = LibraryBook::query()
            ->published()
            ->with(['category.parent.parent'])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if (filled($query)) {
            $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], trim($query)).'%';
            $builder->where(function (Builder $q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('author', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        if ($categoryId) {
            $ids = $this->descendantCategoryIdsIncludingSelf($categoryId);
            $builder->whereIn('library_category_id', $ids);
        }

        return $builder->paginate($perPage)->withQueryString();
    }

    /**
     * @return list<int>
     */
    public function descendantCategoryIdsIncludingSelf(int $categoryId): array
    {
        $all = LibraryCategory::query()
            ->active()
            ->get(['id', 'parent_id']);

        $childrenMap = [];
        foreach ($all as $row) {
            $childrenMap[$row->parent_id ?? 0][] = (int) $row->id;
        }

        $ids = [];
        $stack = [$categoryId];

        while ($stack !== []) {
            $current = array_pop($stack);
            if (in_array($current, $ids, true)) {
                continue;
            }
            $ids[] = $current;
            foreach ($childrenMap[$current] ?? [] as $childId) {
                $stack[] = $childId;
            }
        }

        return $ids;
    }

    /**
     * @param  Collection<int, LibraryCategory>  $flat
     * @return Collection<int, LibraryCategory>
     */
    private function buildTree(Collection $flat): Collection
    {
        $byParent = $flat->groupBy(fn (LibraryCategory $c) => $c->parent_id ?? 0);

        $attach = function (LibraryCategory $node) use (&$attach, $byParent): LibraryCategory {
            $children = ($byParent->get($node->id) ?? collect())->values();
            $children->each(fn (LibraryCategory $child) => $attach($child));
            $node->setRelation('children', $children);

            return $node;
        };

        return ($byParent->get(0) ?? collect())
            ->values()
            ->each(fn (LibraryCategory $root) => $attach($root));
    }
}
