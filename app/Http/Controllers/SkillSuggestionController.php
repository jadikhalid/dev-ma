<?php

namespace App\Http\Controllers;

use App\Models\ProfessionSuggestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillSuggestionController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:128'],
            'profession' => ['nullable', 'string', 'max:64'],
        ]);

        $term = trim($validated['q'] ?? '');

        if ($term === '') {
            return response()->json(['suggestions' => []]);
        }

        $locale = app()->getLocale();

        $query = ProfessionSuggestion::query()
            ->active()
            ->with(['profession' => fn ($q) => $q->where('is_active', true)])
            ->whereHas('profession', function ($q) use ($validated) {
                $q->where('is_active', true);

                if (! empty($validated['profession'])) {
                    $q->where('slug', $validated['profession']);
                }
            })
            ->forTerm($term, $locale)
            ->limit(8);

        $suggestions = $query->get()->map(fn (ProfessionSuggestion $item) => [
            'id' => $item->id,
            'label' => $item->localizedLabel($locale),
            'profession' => $item->profession?->localizedName($locale),
            'profession_slug' => $item->profession?->slug,
        ]);

        return response()->json(['suggestions' => $suggestions]);
    }
}
