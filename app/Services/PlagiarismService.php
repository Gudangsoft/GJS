<?php

namespace App\Services;

use App\Models\PlagiarismCheck;
use App\Models\Submission;

class PlagiarismService
{
    private const SHINGLE_SIZE = 4;    // 4-word n-grams
    private const MIN_WORDS    = 10;   // skip very short texts

    // Indonesian + English common stop words to reduce noise
    private const STOP_WORDS = [
        'yang','dan','di','ke','dari','untuk','dengan','pada','adalah','ini',
        'itu','atau','juga','dalam','akan','telah','tidak','ada','dapat','oleh',
        'the','a','an','of','in','to','and','for','is','are','was','were',
        'with','as','at','by','this','that','be','have','has','been','it',
        'or','but','not','from','on','they','we','he','she','i','you',
    ];

    public function check(Submission $target): PlagiarismCheck
    {
        $targetText  = $this->extractText($target);
        $targetShingles = $this->shingle($targetText);
        $sourceLength   = count(explode(' ', $targetText));

        // Gather all other submissions (same journal) with text
        $candidates = Submission::where('journal_id', $target->journal_id)
            ->where('id', '!=', $target->id)
            ->whereNotNull('abstract')
            ->select(['id', 'title', 'abstract', 'status'])
            ->get();

        $results = [];
        foreach ($candidates as $candidate) {
            $candText     = $this->extractText($candidate);
            $candShingles = $this->shingle($candText);
            $score        = $this->jaccard($targetShingles, $candShingles);

            if ($score < 0.02) continue; // skip negligible matches

            $results[] = [
                'submission_id' => $candidate->id,
                'title'         => $candidate->title,
                'status'        => $candidate->status,
                'score'         => round($score * 100, 1),
                'matched'       => $this->sampleMatches($targetShingles, $candShingles, 5),
            ];
        }

        // Sort descending by score
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        $overall = count($results) > 0 ? $results[0]['score'] : 0.0;

        $check = PlagiarismCheck::create([
            'submission_id'   => $target->id,
            'checked_by'      => auth()->id(),
            'overall_score'   => $overall,
            'source_length'   => $sourceLength,
            'sources_checked' => $candidates->count(),
            'results'         => $results,
            'checked_at'      => now(),
        ]);

        // Update denormalized field on submission
        $target->update([
            'similarity_score'      => $overall,
            'similarity_checked_at' => now(),
        ]);

        return $check;
    }

    // ─── Internal helpers ─────────────────────────────────────────────────────

    private function extractText(Submission $s): string
    {
        $parts = array_filter([
            $s->title ?? '',
            strip_tags($s->abstract ?? ''),
        ]);
        return implode(' ', $parts);
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        $words = explode(' ', trim($text));
        $words = array_filter($words, fn($w) => strlen($w) > 2 && !in_array($w, self::STOP_WORDS));

        return implode(' ', array_values($words));
    }

    /** Returns a set (assoc array) of n-gram strings */
    private function shingle(string $text): array
    {
        $text  = $this->normalize($text);
        $words = explode(' ', $text);
        $k     = self::SHINGLE_SIZE;
        $n     = count($words);

        if ($n < self::MIN_WORDS) return [];

        $set = [];
        for ($i = 0; $i <= $n - $k; $i++) {
            $gram      = implode(' ', array_slice($words, $i, $k));
            $set[$gram] = true;
        }
        return $set;
    }

    private function jaccard(array $a, array $b): float
    {
        if (empty($a) || empty($b)) return 0.0;

        $intersection = count(array_intersect_key($a, $b));
        $union        = count($a) + count($b) - $intersection;

        return $union === 0 ? 0.0 : $intersection / $union;
    }

    /** Return up to $limit matched n-gram strings as sample */
    private function sampleMatches(array $a, array $b, int $limit): array
    {
        $matched = array_keys(array_intersect_key($a, $b));
        return array_slice($matched, 0, $limit);
    }
}