<?php

namespace App\Http\Controllers;

use App\Services\AtsCompatibilityScorer;
use App\Services\AtsCvOptimizer;
use App\Services\AtsCvTextExtractor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use RuntimeException;

class TalentAtsScoreController extends Controller
{
    public function __construct(
        private AtsCvTextExtractor $extractor,
        private AtsCompatibilityScorer $scorer,
        private AtsCvOptimizer $optimizer,
    ) {}

    public function show(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->canAccessWorkspaceApps(), 403);

        $payload = $request->session()->get('ats_score_last');

        return view('talent.ats-score.index', [
            'result' => is_array($payload['result'] ?? null) ? $payload['result'] : null,
            'filename' => is_string($payload['filename'] ?? null) ? $payload['filename'] : null,
            'optimizedText' => is_string($payload['optimized_text'] ?? null) ? $payload['optimized_text'] : null,
            'optimizedResult' => is_array($payload['optimized_result'] ?? null) ? $payload['optimized_result'] : null,
            'remainingActions' => is_array($payload['remaining_actions'] ?? null) ? $payload['remaining_actions'] : [],
            'hasSourceText' => is_string($payload['source_text'] ?? null) && $payload['source_text'] !== '',
        ]);
    }

    public function analyze(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->canAccessWorkspaceApps(), 403);

        $request->validate([
            'cv' => ['required', 'file', 'max:5120', 'mimes:pdf,docx,txt'],
        ], [
            'cv.required' => __('talenma.ats_score.validation.cv_required'),
            'cv.mimes' => __('talenma.ats_score.validation.cv_mimes'),
            'cv.max' => __('talenma.ats_score.validation.cv_max'),
        ]);

        $file = $request->file('cv');

        try {
            $extracted = $this->extractor->extract($file);
        } catch (RuntimeException $e) {
            return back()->withErrors([
                'cv' => __('talenma.ats_score.errors.'.$e->getMessage()),
            ]);
        }

        $result = $this->scorer->scoreText($extracted['text']);

        $request->session()->put('ats_score_last', [
            'filename' => $file->getClientOriginalName(),
            'source_text' => $extracted['text'],
            'result' => $result,
            'optimized_text' => null,
            'optimized_result' => null,
            'remaining_actions' => [],
            'analyzed_at' => now()->toIso8601String(),
        ]);

        return redirect()
            ->route('talent.ats-score.index')
            ->with('toast_success', __('talenma.ats_score.analyzed_toast'));
    }

    public function optimize(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->canAccessWorkspaceApps(), 403);

        $payload = $request->session()->get('ats_score_last');
        $source = is_string($payload['source_text'] ?? null) ? $payload['source_text'] : '';
        $result = is_array($payload['result'] ?? null) ? $payload['result'] : null;

        if ($source === '' || $result === null) {
            return redirect()
                ->route('talent.ats-score.index')
                ->withErrors(['cv' => __('talenma.ats_score.errors.optimize_needs_upload')]);
        }

        $locale = app()->getLocale() === 'en' ? 'en' : 'fr';
        $optimized = $this->optimizer->optimize($source, $result, $locale);

        $payload['optimized_text'] = $optimized['text'];
        $payload['optimized_result'] = $optimized['result'];
        $payload['remaining_actions'] = $optimized['remaining_actions'];
        $payload['optimized_at'] = now()->toIso8601String();
        $request->session()->put('ats_score_last', $payload);

        return redirect()
            ->route('talent.ats-score.index')
            ->with('toast_success', __('talenma.ats_score.optimized_toast', [
                'score' => $optimized['result']['score'] ?? 0,
            ]));
    }

    public function downloadOptimized(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->canAccessWorkspaceApps(), 403);

        $payload = $request->session()->get('ats_score_last');
        $text = is_string($payload['optimized_text'] ?? null) ? $payload['optimized_text'] : '';

        if ($text === '') {
            return redirect()
                ->route('talent.ats-score.index')
                ->withErrors(['cv' => __('talenma.ats_score.errors.optimize_needs_upload')]);
        }

        $base = pathinfo((string) ($payload['filename'] ?? 'cv'), PATHINFO_FILENAME);
        $base = preg_replace('/[^A-Za-z0-9_\-]+/', '-', $base) ?: 'cv';
        $downloadName = $base.'-ats-friendly.txt';

        return response($text, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$downloadName.'"',
        ]);
    }
}
