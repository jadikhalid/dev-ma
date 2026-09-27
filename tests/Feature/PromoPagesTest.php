<?php

namespace Tests\Feature;

use App\Models\LibraryBook;
use App\Models\LibraryCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_library_promo_with_access_ctas_and_open_graph(): void
    {
        $book = $this->publishedBook();

        $this->get(route('promo.library', $book))
            ->assertOk()
            ->assertSee('property="og:title" content="'.$book->title.'"', false)
            ->assertSee(route('promo.library.gate', $book), false)
            ->assertSee(e(route('register', ['role' => 'dev', 'from_promo' => 'library:'.$book->id])), false);
    }

    public function test_unpublished_book_promo_is_not_found(): void
    {
        $book = $this->publishedBook(['is_published' => false]);

        $this->get(route('promo.library', $book))->assertNotFound();
    }

    public function test_talent_is_sent_to_library_with_book_opened(): void
    {
        $book = $this->publishedBook();
        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('promo.library', $book))
            ->assertRedirect(route('promo.library.gate', $book));

        $this->actingAs($talent)
            ->get(route('promo.library.gate', $book))
            ->assertRedirect(route('talent.library.index', ['book' => $book->id]));

        $this->actingAs($talent)
            ->get(route('talent.library.index', ['book' => $book->id]))
            ->assertOk()
            ->assertSee('\u0022id\u0022:'.$book->id.',\u0022title\u0022', false)
            ->assertDontSee('featuredBook: null', false);

        $this->actingAs($talent)
            ->get(route('talent.library.index'))
            ->assertSee('featuredBook: null', false);
    }

    public function test_company_sees_talents_only_notice(): void
    {
        $book = $this->publishedBook();
        $company = User::factory()->companyOwner()->create();

        $this->actingAs($company)
            ->get(route('promo.library', $book))
            ->assertOk()
            ->assertSee('data-promo-talents-only', false);

        $this->actingAs($company)
            ->get(route('promo.library.gate', $book))
            ->assertRedirect(route($company->homeRouteName()))
            ->assertSessionHas('toast_error', __('talenma.promo.talents_only'));
    }

    public function test_cv_template_promo_preselects_template_for_talent(): void
    {
        $this->get(route('promo.cv-template', 'modern'))
            ->assertOk()
            ->assertSee('property="og:image"', false)
            ->assertSee(route('promo.cv-template.gate', 'modern'), false);

        $talent = User::factory()->talent()->create();

        $this->actingAs($talent)
            ->get(route('promo.cv-template.gate', 'modern'))
            ->assertRedirect(route('talent.cv-builder.index', ['template' => 'modern']));

        $this->actingAs($talent)
            ->get(route('talent.cv-builder.index', ['template' => 'modern']))
            ->assertOk()
            ->assertSee('\u0022template\u0022:\u0022modern\u0022', false);
    }

    public function test_register_from_promo_sets_intended_gate(): void
    {
        $book = $this->publishedBook();

        $this->get(route('register', ['role' => 'dev', 'from_promo' => 'library:'.$book->id]))
            ->assertOk()
            ->assertSessionHas('url.intended', route('promo.library.gate', $book));
    }

    public function test_admin_sees_share_links_for_books_and_cv_templates(): void
    {
        $book = $this->publishedBook();
        $admin = User::factory()->create(['role' => 'admin', 'approval_status' => null]);

        $this->actingAs($admin)
            ->get(route('admin.library.books.index'))
            ->assertOk()
            ->assertSee(route('promo.library', $book), false)
            ->assertSee('linkedin.com/sharing/share-offsite', false)
            ->assertSee(route('admin.cv-templates.index'), false);

        $this->actingAs($admin)
            ->get(route('admin.cv-templates.index'))
            ->assertOk()
            ->assertSee(route('promo.cv-template', 'modern'), false);
    }

    private function publishedBook(array $overrides = []): LibraryBook
    {
        $category = LibraryCategory::query()->create([
            'name_fr' => 'Informatique',
            'name_en' => 'Computer science',
            'slug' => 'informatique-'.uniqid(),
            'depth' => 0,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        return LibraryBook::query()->create(array_merge([
            'library_category_id' => $category->id,
            'title' => 'Clean Code en pratique',
            'author' => 'Jane Doe',
            'description' => 'Un guide pour écrire du code lisible.',
            'file_path' => 'library/books/clean-code.pdf',
            'original_filename' => 'clean-code.pdf',
            'mime' => 'application/pdf',
            'size_bytes' => 2048,
            'is_published' => true,
        ], $overrides));
    }
}
