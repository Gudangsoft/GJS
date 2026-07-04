<?php

namespace App\Http\Controllers;

use App\Models\SubmissionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionFileController extends Controller
{
    public function download(Request $request, SubmissionFile $file): mixed
    {
        $user = $request->user();

        // Author of the submission
        $isOwner = $file->submission->user_id === $user->id;

        // Editor/manager/super_admin
        $isStaff = $user->hasAnyRole(['super_admin', 'admin', 'editor', 'journal_manager']);

        // Reviewer assigned to this submission
        $isReviewer = $user->hasRole('reviewer')
            && $file->submission->reviews()
                ->where('reviewer_id', $user->id)
                ->exists();

        if (! ($isOwner || $isStaff || $isReviewer)) {
            abort(403);
        }

        if (! Storage::disk('local')->exists($file->path)) {
            abort(404);
        }

        return Storage::disk('local')->download(
            $file->path,
            $file->original_file_name,
            ['Content-Type' => $file->mime_type]
        );
    }
}
