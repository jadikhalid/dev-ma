<?php

namespace App\Http\Controllers;

use App\Services\TalentSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TalentSearchController extends Controller
{
    public function __construct(private TalentSearchService $talentSearch) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sector' => ['required', 'string', 'max:64'],
            'profession' => ['required', 'string', 'max:64'],
            'keyword' => ['required', 'string', 'max:500'],
        ]);

        $keywords = array_values(array_filter(array_map(
            'trim',
            explode(',', $validated['keyword']),
        )));

        if (count($keywords) < 3) {
            return response()->json([
                'message' => __('talenma.home.search_validation_incomplete'),
                'count' => 0,
                'results' => [],
            ], 422);
        }

        $preview = $this->talentSearch->preview(
            $validated['sector'],
            $validated['profession'],
            $keywords,
            2,
        );

        return response()->json($preview);
    }
}
