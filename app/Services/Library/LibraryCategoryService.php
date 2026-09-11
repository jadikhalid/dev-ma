<?php

namespace App\Services\Library;

use App\Models\LibraryCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class LibraryCategoryService
{
    /**
     * @param  array{name_fr:string,name_en:string,slug?:string|null,parent_id?:int|null,sort_order?:int,is_active?:bool}  $data
     */
    public function create(array $data): LibraryCategory
    {
        $parentId = $data['parent_id'] ?? null;
        $parent = null;
        $depth = 1;

        if ($parentId) {
            $parent = LibraryCategory::query()->findOrFail($parentId);
            if ($parent->depth >= LibraryCategory::MAX_DEPTH) {
                throw ValidationException::withMessages([
                    'parent_id' => [__('talenma.admin.library.category_max_depth')],
                ]);
            }
            $depth = $parent->depth + 1;
        }

        $slugSource = filled($data['slug'] ?? null) ? (string) $data['slug'] : $data['name_fr'];

        return LibraryCategory::query()->create([
            'parent_id' => $parent?->id,
            'slug' => LibraryCategory::uniqueSlugForParent($slugSource, $parent?->id),
            'name_fr' => $data['name_fr'],
            'name_en' => $data['name_en'],
            'depth' => $depth,
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);
    }

    /**
     * @param  array{name_fr:string,name_en:string,slug?:string|null,sort_order?:int,is_active?:bool}  $data
     */
    public function update(LibraryCategory $category, array $data): LibraryCategory
    {
        $slugSource = filled($data['slug'] ?? null) ? (string) $data['slug'] : $data['name_fr'];

        $category->fill([
            'slug' => LibraryCategory::uniqueSlugForParent($slugSource, $category->parent_id, $category->id),
            'name_fr' => $data['name_fr'],
            'name_en' => $data['name_en'],
            'sort_order' => (int) ($data['sort_order'] ?? $category->sort_order),
            'is_active' => array_key_exists('is_active', $data)
                ? (bool) $data['is_active']
                : $category->is_active,
        ])->save();

        return $category->refresh();
    }

    public function delete(LibraryCategory $category): void
    {
        if ($category->children()->exists()) {
            throw ValidationException::withMessages([
                'category' => [__('talenma.admin.library.category_has_children')],
            ]);
        }

        if ($category->books()->exists()) {
            throw ValidationException::withMessages([
                'category' => [__('talenma.admin.library.category_has_books')],
            ]);
        }

        $category->delete();
    }

    /**
     * Idempotent seed from nested array structure.
     *
     * @param  list<array{slug:string,name_fr:string,name_en:string,children?:list<array>}>  $tree
     */
    public function syncTree(array $tree): void
    {
        DB::transaction(function () use ($tree) {
            $this->syncLevel($tree, null, 1);
        });
    }

    /**
     * @param  list<array{slug:string,name_fr:string,name_en:string,children?:list<array>}>  $nodes
     */
    private function syncLevel(array $nodes, ?int $parentId, int $depth): void
    {
        if ($depth > LibraryCategory::MAX_DEPTH) {
            throw new RuntimeException('Library category depth exceeded.');
        }

        foreach ($nodes as $index => $node) {
            $category = LibraryCategory::query()->updateOrCreate(
                [
                    'parent_id' => $parentId,
                    'slug' => $node['slug'],
                ],
                [
                    'name_fr' => $node['name_fr'],
                    'name_en' => $node['name_en'],
                    'depth' => $depth,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );

            $children = $node['children'] ?? [];
            if ($children !== []) {
                $this->syncLevel($children, $category->id, $depth + 1);
            }
        }
    }
}
