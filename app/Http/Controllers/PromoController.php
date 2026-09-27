<?php

namespace App\Http\Controllers;

use App\Models\LibraryBook;
use App\Services\Library\LibraryCatalogService;
use App\Support\TalentCv\TalentCvMarketingPreview;
use App\Support\TalentCv\TalentCvTemplateCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Public landing pages used to promote new "Mes applications" content
 * (library books, CV templates) on social networks.
 */
class PromoController extends Controller
{
    public const APP_LIBRARY = 'library';

    public const APP_CV_TEMPLATE = 'cv-template';

    public function __construct(private LibraryCatalogService $catalog) {}

    public function library(Request $request, LibraryBook $book): View|RedirectResponse
    {
        abort_unless($book->is_published, 404);

        if ($this->viewerIsTalent($request)) {
            return redirect()->route('promo.library.gate', $book);
        }

        $book->loadMissing('category');

        return $this->render($request, [
            'app' => self::APP_LIBRARY,
            'appLabel' => __('talenma.nav.apps_launcher_library'),
            'title' => $book->title,
            'subtitle' => collect([
                $book->author,
                $book->category ? $this->catalog->breadcrumb($book->category) : null,
            ])->filter()->implode(' · '),
            'description' => (string) $book->description,
            'imageUrl' => $book->coverUrl(),
            'imageFit' => 'cover',
            'highlights' => [
                __('talenma.promo.library.highlight_free'),
                __('talenma.promo.library.highlight_download'),
                __('talenma.promo.library.highlight_catalog'),
            ],
            'shareUrl' => route('promo.library', $book),
            'gateUrl' => route('promo.library.gate', $book),
            'promoRef' => self::APP_LIBRARY.':'.$book->id,
        ]);
    }

    public function cvTemplate(Request $request, string $template): View|RedirectResponse
    {
        if ($this->viewerIsTalent($request)) {
            return redirect()->route('promo.cv-template.gate', $template);
        }

        $label = TalentCvTemplateCatalog::templateLabels()[$template];

        return $this->render($request, [
            'app' => self::APP_CV_TEMPLATE,
            'appLabel' => __('talenma.nav.apps_launcher_cv_builder'),
            'title' => __('talenma.promo.cv_template.title', ['template' => $label]),
            'subtitle' => __('talenma.promo.cv_template.subtitle'),
            'description' => __('talenma.promo.cv_template.description'),
            'imageUrl' => TalentCvMarketingPreview::imagePath($template),
            'imageFit' => 'contain',
            'highlights' => [
                __('talenma.promo.cv_template.highlight_free'),
                __('talenma.promo.cv_template.highlight_pdf'),
                __('talenma.promo.cv_template.highlight_languages'),
            ],
            'shareUrl' => route('promo.cv-template', $template),
            'gateUrl' => route('promo.cv-template.gate', $template),
            'promoRef' => self::APP_CV_TEMPLATE.':'.$template,
        ]);
    }

    public function libraryGate(Request $request, LibraryBook $book): RedirectResponse
    {
        abort_unless($book->is_published, 404);

        if (! $request->user()->canAccessWorkspaceApps()) {
            return $this->talentsOnly($request);
        }

        return redirect()->route('talent.library.index', ['book' => $book->id]);
    }

    public function cvTemplateGate(Request $request, string $template): RedirectResponse
    {
        if (! $request->user()->canAccessWorkspaceApps()) {
            return $this->talentsOnly($request);
        }

        return redirect()->route('talent.cv-builder.index', ['template' => $template]);
    }

    /**
     * Intended URL after sign-up from a promo page (`library:12`, `cv-template:modern`).
     */
    public static function gateUrlForRef(string $ref): ?string
    {
        [$app, $id] = array_pad(explode(':', $ref, 2), 2, '');

        return match (true) {
            $app === self::APP_LIBRARY && ctype_digit($id)
                && LibraryBook::query()->published()->whereKey((int) $id)->exists() => route('promo.library.gate', (int) $id),
            $app === self::APP_CV_TEMPLATE && TalentCvTemplateCatalog::isValidTemplate($id) => route('promo.cv-template.gate', $id),
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $promo
     */
    private function render(Request $request, array $promo): View
    {
        $user = $request->user();

        return view('promo.show', [
            ...$promo,
            'metaDescription' => Str::of(strip_tags($promo['description']))->squish()->limit(160)->toString(),
            'registerUrl' => route('register', ['role' => 'dev', 'from_promo' => $promo['promoRef']]),
            'viewerIsAuthenticated' => $user !== null,
            'viewerIsPendingTalent' => (bool) $user?->isTalent(),
        ]);
    }

    private function viewerIsTalent(Request $request): bool
    {
        return (bool) $request->user()?->canAccessWorkspaceApps();
    }

    private function talentsOnly(Request $request): RedirectResponse
    {
        $user = $request->user();

        return redirect()
            ->route($user->homeRouteName())
            ->with('toast_error', $user->isTalent()
                ? __('talenma.promo.pending_talent')
                : __('talenma.promo.talents_only'));
    }
}
