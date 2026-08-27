<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileDocument;
use App\Services\ProfileDocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfileDocumentController extends Controller
{
    public function __construct(private ProfileDocumentService $documents) {}

    public function show(ProfileDocument $profileDocument): StreamedResponse
    {
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
        if (! in_array($profileDocument->document_type, [
            ProfileDocument::TYPE_CV,
            ProfileDocument::TYPE_REGISTRATION,
        ], true)) {
            abort(403);
        }

        $owner = $profileDocument->profile?->user;
        $documentType = $profileDocument->document_type;

        $this->documents->delete($profileDocument);

        $message = $documentType === ProfileDocument::TYPE_REGISTRATION
            ? __('talenma.talent.section_updated.certifications')
            : __('talenma.talent.section_updated.documents');

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        if ($owner) {
            return redirect()
                ->route('admin.users.profile.edit', $owner)
                ->with('toast_success', $message);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('toast_success', $message);
    }
}
