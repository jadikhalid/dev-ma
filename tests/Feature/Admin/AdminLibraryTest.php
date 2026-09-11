<?php

namespace Tests\Feature\Admin;

use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\User;
use App\Support\LibraryBookStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminLibraryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_manage_categories_and_books(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        $this->actingAs($admin)
            ->get(route('admin.library.categories.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('admin.library.categories.store'), [
                'name_fr' => 'Informatique',
                'name_en' => 'IT',
                'sort_order' => 1,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.library.categories.index'));

        $root = LibraryCategory::query()->where('slug', 'informatique')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.library.categories.store'), [
                'name_fr' => 'Développement',
                'name_en' => 'Development',
                'parent_id' => $root->id,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.library.categories.index'));

        $sub = LibraryCategory::query()->where('slug', 'developpement')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.library.categories.store'), [
                'name_fr' => 'Programmation système',
                'name_en' => 'Systems programming',
                'parent_id' => $sub->id,
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.library.categories.index'));

        $leaf = LibraryCategory::query()->where('slug', 'programmation-systeme')->firstOrFail();

        $file = UploadedFile::fake()->create('guide.pdf', 120, 'application/pdf');
        $cover = UploadedFile::fake()->image('cover.jpg', 400, 600);

        $this->actingAs($admin)
            ->post(route('admin.library.books.store'), [
                'library_category_id' => $leaf->id,
                'title' => 'Guide système',
                'author' => 'Auteur',
                'description' => 'Desc',
                'is_published' => 1,
                'file' => $file,
                'cover' => $cover,
            ])
            ->assertRedirect(route('admin.library.books.index'));

        $book = LibraryBook::query()->where('title', 'Guide système')->firstOrFail();
        $this->assertTrue(LibraryBookStorage::exists($book->file_path));
        $this->assertNotNull($book->cover_path);
        $this->assertTrue(\App\Support\LibraryCoverStorage::exists($book->cover_path));
        $this->assertNotNull($book->coverUrl());

        $replacement = UploadedFile::fake()->create('guide-v2.pdf', 180, 'application/pdf');
        $oldPath = $book->file_path;

        $this->actingAs($admin)
            ->put(route('admin.library.books.update', $book), [
                'library_category_id' => $leaf->id,
                'title' => 'Guide système',
                'author' => 'Auteur',
                'description' => 'Desc',
                'is_published' => 1,
                'file' => $replacement,
            ])
            ->assertRedirect(route('admin.library.books.edit', $book));

        $book->refresh();
        $this->assertSame('guide-v2.pdf', $book->original_filename);
        $this->assertTrue(LibraryBookStorage::exists($book->file_path));
        $this->assertNotSame($oldPath, $book->file_path);

        $this->actingAs($admin)
            ->delete(route('admin.library.books.destroy', $book))
            ->assertRedirect(route('admin.library.books.index'));

        $this->assertDatabaseMissing('library_books', ['id' => $book->id]);
    }

    #[Test]
    public function admin_cannot_delete_category_with_children(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        $root = LibraryCategory::query()->create([
            'parent_id' => null,
            'slug' => 'root',
            'name_fr' => 'Root',
            'name_en' => 'Root',
            'depth' => 1,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        LibraryCategory::query()->create([
            'parent_id' => $root->id,
            'slug' => 'child',
            'name_fr' => 'Child',
            'name_en' => 'Child',
            'depth' => 2,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.library.categories.index'))
            ->delete(route('admin.library.categories.destroy', $root))
            ->assertRedirect(route('admin.library.categories.index'))
            ->assertSessionHasErrors('category');

        $this->assertDatabaseHas('library_categories', ['id' => $root->id]);
    }

    #[Test]
    public function non_admin_cannot_access_library_admin(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('admin.library.books.index'))
            ->assertForbidden();
    }
}
