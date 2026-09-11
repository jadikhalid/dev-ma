<?php

namespace Tests\Feature\Talent;

use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\User;
use App\Support\LibraryBookStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TalentLibraryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function approved_talent_can_view_library(): void
    {
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('talent.library.index'))
            ->assertOk()
            ->assertSee(__('talenma.library.page_title'), false);
    }

    #[Test]
    public function talent_sees_published_books_and_can_filter_by_category(): void
    {
        $talent = User::factory()->talent()->create();
        [$leafA, $leafB] = $this->seedTwoLeaves();

        $this->createBook($leafA, 'Book Alpha', true);
        $this->createBook($leafB, 'Book Beta', true);
        $this->createBook($leafA, 'Hidden Draft', false);

        $this->actingAs($talent)
            ->get(route('talent.library.index'))
            ->assertOk()
            ->assertSee(__('talenma.library.catalog_title'), false)
            ->assertSee(__('talenma.library.discover'), false)
            ->assertSee('Book Alpha', false)
            ->assertSee('Book Beta', false)
            ->assertDontSee('Hidden Draft', false);

        $this->actingAs($talent)
            ->get(route('talent.library.index', ['category' => $leafA->id]))
            ->assertOk()
            ->assertSee('Book Alpha', false)
            ->assertDontSee('Book Beta', false);

        $this->actingAs($talent)
            ->get(route('talent.library.index', [
                'category' => $leafA->id,
                'partial' => 1,
            ]), [
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->assertOk()
            ->assertSee(__('talenma.library.catalog_title'), false)
            ->assertSee('Book Alpha', false)
            ->assertDontSee('Book Beta', false)
            ->assertDontSee(__('talenma.library.page_title'), false);
    }

    #[Test]
    public function talent_library_lists_books_by_upload_order_with_ten_per_page(): void
    {
        $talent = User::factory()->talent()->create();
        [$leaf] = $this->seedTwoLeaves();

        for ($i = 1; $i <= 11; $i++) {
            $book = $this->createBook($leaf, 'Book '.str_pad((string) $i, 2, '0', STR_PAD_LEFT), true);
            $book->forceFill([
                'created_at' => now()->subMinutes(12 - $i),
                'updated_at' => now()->subMinutes(12 - $i),
            ])->save();
        }

        $this->actingAs($talent)
            ->get(route('talent.library.index'))
            ->assertOk()
            ->assertSee('Book 11', false)
            ->assertSee('Book 02', false)
            ->assertDontSee('Book 01', false);

        $this->actingAs($talent)
            ->get(route('talent.library.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Book 01', false)
            ->assertDontSee('Book 11', false);
    }

    #[Test]
    public function talent_can_download_published_book_and_increments_counter(): void
    {
        Storage::fake('local');

        $talent = User::factory()->talent()->create();
        [$leaf] = $this->seedTwoLeaves();
        $book = $this->createBook($leaf, 'Downloadable', true);

        $this->actingAs($talent)
            ->get(route('talent.library.download', $book))
            ->assertOk();

        $this->assertSame(1, $book->fresh()->download_count);
    }

    #[Test]
    public function guest_is_redirected_from_library(): void
    {
        $this->get(route('talent.library.index'))
            ->assertRedirect();
    }

    /**
     * @return array{0: LibraryCategory, 1: LibraryCategory}
     */
    private function seedTwoLeaves(): array
    {
        $root = LibraryCategory::query()->create([
            'parent_id' => null,
            'slug' => 'root-a',
            'name_fr' => 'Racine A',
            'name_en' => 'Root A',
            'depth' => 1,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $sub = LibraryCategory::query()->create([
            'parent_id' => $root->id,
            'slug' => 'sub-a',
            'name_fr' => 'Sous A',
            'name_en' => 'Sub A',
            'depth' => 2,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $leafA = LibraryCategory::query()->create([
            'parent_id' => $sub->id,
            'slug' => 'leaf-a',
            'name_fr' => 'Feuille A',
            'name_en' => 'Leaf A',
            'depth' => 3,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $leafB = LibraryCategory::query()->create([
            'parent_id' => $sub->id,
            'slug' => 'leaf-b',
            'name_fr' => 'Feuille B',
            'name_en' => 'Leaf B',
            'depth' => 3,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        return [$leafA, $leafB];
    }

    private function createBook(LibraryCategory $leaf, string $title, bool $published): LibraryBook
    {
        Storage::fake('local');
        $path = 'library-books/'.$title.'.pdf';
        Storage::disk(LibraryBookStorage::DISK)->put($path, '%PDF-1.4 test');

        return LibraryBook::query()->create([
            'library_category_id' => $leaf->id,
            'title' => $title,
            'author' => 'Author',
            'description' => null,
            'file_path' => $path,
            'original_filename' => $title.'.pdf',
            'mime' => 'application/pdf',
            'size_bytes' => 12,
            'download_count' => 0,
            'is_published' => $published,
            'uploaded_by' => null,
        ]);
    }
}
