<?php

namespace App\Observers;

use App\Mail\SubmissionReceived;
use App\Models\Submission;
use Illuminate\Support\Facades\Mail;

class SubmissionObserver
{
    public function created(Submission $submission): void
    {
        $submission->loadMissing(['submitter', 'journal']);

        if ($submission->submitted_at && $submission->submitter?->email) {
            Mail::to($submission->submitter->email)
                ->queue(new SubmissionReceived($submission));
        }
    }
}
