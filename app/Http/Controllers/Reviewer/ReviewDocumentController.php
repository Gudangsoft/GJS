<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\ReviewAssignment;
use Illuminate\Http\Request;

class ReviewDocumentController extends Controller
{
    public function suratTugas(ReviewAssignment $assignment)
    {
        abort_if($assignment->reviewer_id !== auth()->id(), 403);
        abort_if(!in_array($assignment->status, ['accepted', 'completed']), 403);

        $assignment->load([
            'submission.journal',
            'submission.section',
            'submission.contributors',
            'reviewer',
            'editor',
        ]);

        return view('reviewer.surat-tugas', compact('assignment'));
    }

    public function sertifikat(ReviewAssignment $assignment)
    {
        abort_if($assignment->reviewer_id !== auth()->id(), 403);
        abort_if($assignment->status !== 'completed', 403);

        $assignment->load([
            'submission.journal',
            'submission.section',
            'reviewer',
            'review',
        ]);

        return view('reviewer.sertifikat', compact('assignment'));
    }
}
