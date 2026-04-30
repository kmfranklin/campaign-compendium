<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\SessionLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Stream a private media file to the browser.
     *
     * Files are stored on the 'private' disk and cannot be accessed directly
     * via URL. This controller checks that the authenticated user is a member
     * of the campaign the file belongs to before streaming it.
     */
    public function show(Media $media)
    {
        $mediable = $media->mediable;

        // Authorization: check campaign membership based on the owning model.
        // As more models gain media support, add their cases here.
        if ($mediable instanceof SessionLog) {
            $campaign = $mediable->campaign;

            $isMember = $campaign->members()->where('user_id', auth()->id())->exists()
                     || $campaign->dm_id === auth()->id();

            if (! $isMember) {
                abort(403, 'You do not have access to this file.');
            }
        } else {
            // Fallback: deny access for any mediable type not yet handled
            abort(403);
        }

        // Use response()->file() rather than Storage::response().
        // Storage::response() returns a StreamedResponse, which does NOT support
        // HTTP Range requests — so browsers can't seek in the audio player.
        // response()->file() returns a BinaryFileResponse (Symfony), which
        // automatically handles Range requests and sends Accept-Ranges headers,
        // enabling full seek/scrub support in the browser's native audio element.
        $absolutePath = Storage::disk('private')->path($media->path);

        return response()->file($absolutePath, [
            'Content-Type'        => $media->mime_type,
            'Content-Disposition' => 'inline; filename="' . addslashes($media->filename) . '"',
        ]);
    }
}
