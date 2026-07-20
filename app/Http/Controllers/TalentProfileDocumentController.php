<?php

namespace App\Http\Controllers;

use App\Models\ProfileDocument;
use App\Services\ProfileDocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TalentProfileDocumentController extends Controller
{
    public function __construct(private ProfileDocumentService $documents) {}

    public function show(ProfileDocument $profileDocument): StreamedResponse
    {
        $this->authorizeDocument($profileDocument);

        $disk = Storage::disk('public');

        abort_unless($disk->exists($profileDocument->path), 404);

        return $disk->response(
            $profileDocument->path,
            $profileDocument->original_name,
            [
                'Content-Type' => $profileDocument->mime_type ?? 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="'.$profileDocument->original_name.'"',
            ],
        );
    }

    public function destroy(Request $request, ProfileDocument $profileDocument): RedirectResponse|JsonResponse
    {
        $this->authorizeDocument($profileDocument);

        if (! in_array($profileDocument->document_type, [
            ProfileDocument::TYPE_CV,
            ProfileDocument::TYPE_REGISTRATION,
        ], true)) {
            abort(403);
        }

        $documentType = $profileDocument->document_type;

        $this->documents->delete($profileDocument);

        $message = $documentType === ProfileDocument::TYPE_REGISTRATION
            ? __('talenma.talent.section_updated.certifications')
            : __('talenma.talent.section_updated.documents');

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return redirect()
            ->route('profile.details.edit')
            ->with('toast_success', $message);
    }

    private function authorizeDocument(ProfileDocument $profileDocument): void
    {
        $user = Auth::user();

        abort_unless(
            $user
            && $user->isTalent()
            && (int) $profileDocument->profile?->user_id === (int) $user->id,
            403,
        );
    }
}
