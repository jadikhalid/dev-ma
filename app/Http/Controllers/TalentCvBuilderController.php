<?php

namespace App\Http\Controllers;

use App\Services\TalentCvBuilderService;
use App\Support\TalentCv\TalentCvTemplateCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TalentCvBuilderController extends Controller
{
    public function __construct(private TalentCvBuilderService $builder) {}

    public function show(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->isTalent(), 403);

        $draft = $this->builder->draftForUser($user);

        return view('talent.cv-builder.index', [
            'draft' => $draft,
            'templates' => TalentCvTemplateCatalog::templateLabels(),
            'profileAvatarUrl' => $user->avatarUrl(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->isTalent(), 403);

        $draft = $this->builder->draftForUser($user);
        $draft = $this->builder->updateDraft($draft, $this->validatedBuilderPayload($request));

        return response()->json([
            'ok' => true,
            'saved_at' => $draft->updated_at?->toIso8601String(),
        ]);
    }

    public function preview(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->isTalent(), 403);

        $draft = $this->builder->draftForUser($user);

        if ($request->isMethod('post')) {
            $payload = $this->validatedBuilderPayload($request);

            if (isset($payload['template'])) {
                $draft->template = $payload['template'];
            }
            if (isset($payload['locale'])) {
                $draft->locale = $payload['locale'];
            }
            if (isset($payload['data']) && is_array($payload['data'])) {
                $draft->data = \App\Support\TalentCv\TalentCvDraftDefaults::merge($payload['data']);
            }
        }

        return $this->builder->previewView($draft, $user);
    }

    public function export(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user->isTalent(), 403);

        $draft = $this->builder->draftForUser($user);

        if ($request->isMethod('post')) {
            $draft = $this->builder->updateDraft($draft, $this->validatedBuilderPayload($request));
        }

        return $this->builder->exportPdf($draft, $user);
    }

    /** @return array<string, mixed> */
    private function validatedBuilderPayload(Request $request): array
    {
        $validated = $request->validate([
            'template' => ['sometimes', 'string', 'in:'.implode(',', TalentCvTemplateCatalog::templateKeys())],
            'locale' => ['sometimes', 'string', 'in:fr,en'],
            'data' => ['sometimes', 'array'],
        ]);

        if ($request->has('data') && is_array($request->input('data'))) {
            validator(
                [
                    'photo_base64' => $request->input('data.photo_base64'),
                    'photo_source' => $request->input('data.photo_source'),
                ],
                [
                    'photo_base64' => ['nullable', 'string', 'max:1400000'],
                    'photo_source' => ['nullable', 'string', 'in:custom,profile,sample'],
                ],
            )->validate();

            $validated['data'] = $request->input('data');
        }

        return $validated;
    }
}
