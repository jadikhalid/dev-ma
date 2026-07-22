<?php

namespace App\Http\Controllers;

use App\Services\CompanyCatalogSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyCatalogSearchController extends Controller
{
    public function __construct(private CompanyCatalogSearchService $companySearch) {}

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! $user->isTalent() || ! $user->isApproved()) {
            return response()->json([
                'message' => __('talenma.home.company_search_forbidden'),
                'count' => 0,
                'results' => [],
            ], 403);
        }

        $validated = $request->validate([
            'sector' => ['required', 'string', 'max:64'],
            'keyword' => ['required', 'string', 'max:500'],
            'country' => ['nullable', 'string', 'max:2', Rule::in(\App\Models\CompanyProfile::COUNTRY_CODES)],
        ]);

        $keywords = array_values(array_filter(array_map(
            'trim',
            explode(',', $validated['keyword']),
        )));

        $count = count($keywords);

        if ($count === 0) {
            return response()->json([
                'message' => __('talenma.home.search_validation_incomplete'),
                'count' => 0,
                'results' => [],
            ], 422);
        }

        if ($count > 3) {
            return response()->json([
                'message' => __('talenma.home.search_validation_keywords_max'),
                'count' => 0,
                'results' => [],
            ], 422);
        }

        $preview = $this->companySearch->preview(
            $validated['sector'],
            $keywords,
            $validated['country'] ?? null,
            8,
        );

        return response()->json($preview);
    }
}
