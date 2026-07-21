<?php

namespace App\Http\Controllers;

use App\Services\TalentPresentationVideoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class TalentPresentationVideoController extends Controller
{
    public function __construct(
        private TalentPresentationVideoService $videos,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user?->isTalent(), 403);

        $profile = $user->profile ?: $user->profile()->create();
        $maxKb = $this->videos->maxKilobytes();

        $request->validate([
            'presentation_video' => [
                'required',
                'file',
                'max:'.$maxKb,
                'mimetypes:'.implode(',', TalentPresentationVideoService::ALLOWED_MIMES),
            ],
        ], [
            'presentation_video.required' => __('talenma.talent.presentation_video_required'),
            'presentation_video.max' => __('talenma.talent.presentation_video_size', ['max' => (int) ceil($maxKb / 1024)]),
            'presentation_video.mimetypes' => __('talenma.talent.presentation_video_type'),
        ]);

        try {
            $profile = $this->videos->store($profile, $request->file('presentation_video'));
        } catch (RuntimeException $exception) {
            Log::warning('Presentation video store failed', [
                'profile_id' => $profile->id,
                'message' => $exception->getMessage(),
                'cause' => $exception->getPrevious()?->getMessage(),
            ]);

            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => [
                    'presentation_video' => [$exception->getMessage()],
                ],
            ], 422);
        } catch (Throwable $exception) {
            Log::error('Presentation video store unexpected error', [
                'profile_id' => $profile->id,
                'message' => $exception->getMessage(),
            ]);

            $message = __('talenma.talent.presentation_video_upload_failed');

            return response()->json([
                'message' => $message,
                'errors' => [
                    'presentation_video' => [$message],
                ],
            ], 422);
        }

        return response()->json([
            'message' => __('talenma.talent.presentation_video_updated'),
            'presentation_video_url' => $profile->presentation_video_url,
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = Auth::user();
        abort_unless($user?->isTalent(), 403);

        $profile = $user->profile;
        abort_unless($profile, 404);

        $this->videos->destroy($profile);

        return response()->json([
            'message' => __('talenma.talent.presentation_video_removed'),
            'presentation_video_url' => null,
        ]);
    }
}
