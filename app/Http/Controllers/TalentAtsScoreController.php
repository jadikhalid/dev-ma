<?php

namespace App\Http\Controllers;

use App\Services\AtsCompatibilityScorer;
use App\Services\AtsCvOptimizer;
use App\Services\AtsCvTextExtractor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use RuntimeException;

class TalentAtsScoreController extends Controller
{
    private const SOURCE_CACHE_TTL_SECONDS = 7200;

    public function __construct(
        private AtsCvTextExtractor $extractor,
        private AtsCompatibilityScorer $scorer,
        private AtsCvOptimizer $optimizer,
    ) {}

    public function show(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->canAccessWorkspaceApps(), 403);

        $payload = $this->payloadFor($request);

        return view('talent.ats-score.index', [
            'result' => is_array($payload['result'] ?? null) ? $payload['result'] : null,
            'filename' => is_string($payload['filename'] ?? null) ? $payload['filename'] : null,
            'optimizedText' => is_string($payload['optimized_text'] ?? null) ? $payload['optimized_text'] : null,
            'optimizedResult' => is_array($payload['optimized_result'] ?? null) ? $payload['optimized_result'] : null,
            'suggestions' => is_array($payload['suggestions'] ?? null) ? $payload['suggestions'] : [],
            'remainingActions' => is_array($payload['remaining_actions'] ?? null) ? $payload['remaining_actions'] : [],
            'hasSourceText' => is_string($payload['source_text'] ?? null) && $payload['source_text'] !== '',
        ]);
    }

    public function analyze(Request $request): RedirectResponse|JsonResponse
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
            $message = __('talenma.ats_score.errors.'.$e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'errors' => ['cv' => [$message]],
                ], 422);
            }

            return back()->withErrors(['cv' => $message]);
        }

        $result = $this->scorer->scoreText(
            $extracted['text'],
            (bool) ($extracted['has_embedded_image'] ?? false),
        );

        $this->storePayload($request, [
            'filename' => $file->getClientOriginalName(),
            'source_text' => $extracted['text'],
            'result' => $result,
            'optimized_text' => null,
            'optimized_result' => null,
            'suggestions' => [],
            'remaining_actions' => [],
            'analyzed_at' => now()->toIso8601String(),
        ]);

        $message = __('talenma.ats_score.analyzed_toast');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'reload' => true,
            ]);
        }

        return redirect()
            ->route('talent.ats-score.index')
            ->with('toast_success', $message);
    }

    public function optimize(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        abort_unless($user->canAccessWorkspaceApps(), 403);

        $payload = $this->payloadFor($request);
        $source = is_string($payload['source_text'] ?? null) ? $payload['source_text'] : '';
        $result = is_array($payload['result'] ?? null) ? $payload['result'] : null;

        if ($source === '' || $result === null) {
            $message = __('talenma.ats_score.errors.optimize_needs_upload');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'errors' => ['cv' => [$message]],
                ], 422);
            }

            return redirect()
                ->route('talent.ats-score.index')
                ->withErrors(['cv' => $message]);
        }

        $locale = app()->getLocale() === 'en' ? 'en' : 'fr';
        $optimized = $this->optimizer->optimize($source, $result, $locale);

        $payload['optimized_text'] = $optimized['text'];
        $payload['optimized_result'] = $optimized['result'];
        $payload['suggestions'] = $optimized['suggestions'];
        $payload['remaining_actions'] = $optimized['remaining_actions'];
        $payload['optimized_at'] = now()->toIso8601String();
        $this->storePayload($request, $payload);

        $message = __('talenma.ats_score.optimized_toast', [
            'score' => $optimized['result']['score'] ?? 0,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'reload' => true,
            ]);
        }

        return redirect()
            ->route('talent.ats-score.index')
            ->with('toast_success', $message);
    }

    public function downloadOptimized(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->canAccessWorkspaceApps(), 403);

        $payload = $this->payloadFor($request);
        $text = is_string($payload['optimized_text'] ?? null) ? $payload['optimized_text'] : '';

        if ($text === '') {
            return redirect()
                ->route('talent.ats-score.index')
                ->withErrors(['cv' => __('talenma.ats_score.errors.optimize_needs_upload')]);
        }

        $base = pathinfo((string) ($payload['filename'] ?? 'cv'), PATHINFO_FILENAME);
        $base = preg_replace('/[^A-Za-z0-9_\-]+/', '-', $base) ?: 'cv';
        $downloadName = $base.'-ats-suggestions.txt';

        return response($text, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$downloadName.'"',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadFor(Request $request): array
    {
        $payload = $request->session()->get('ats_score_last');
        if (! is_array($payload)) {
            return [];
        }

        $source = is_string($payload['source_text'] ?? null) ? $payload['source_text'] : '';
        if ($source !== '') {
            return $payload;
        }

        $cached = Cache::get($this->sourceCacheKey((int) $request->user()->id));
        if (is_string($cached) && $cached !== '') {
            $payload['source_text'] = $cached;
        }

        return $payload;
    }

    /**
     * Keep heavy CV text out of the session row (avoids rare session write failures
     * that look like a sudden logout after upload).
     *
     * @param  array<string, mixed>  $payload
     */
    private function storePayload(Request $request, array $payload): void
    {
        $source = is_string($payload['source_text'] ?? null) ? $payload['source_text'] : '';
        $userId = (int) $request->user()->id;

        if ($source !== '') {
            Cache::put($this->sourceCacheKey($userId), $source, self::SOURCE_CACHE_TTL_SECONDS);
        } else {
            Cache::forget($this->sourceCacheKey($userId));
        }

        $sessionPayload = $payload;
        // Session keeps a short marker only; full text lives in cache.
        $sessionPayload['source_text'] = $source !== '' ? '' : null;
        $sessionPayload['has_source_text'] = $source !== '';

        $request->session()->put('ats_score_last', $sessionPayload);
    }

    private function sourceCacheKey(int $userId): string
    {
        return 'ats_score_source:'.$userId;
    }
}
