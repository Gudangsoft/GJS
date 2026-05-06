<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Review> */
class ReviewFactory extends Factory
{
    private static array $authorComments = [
        'Penelitian ini memiliki topik yang menarik dan relevan. Namun, beberapa bagian metodologi perlu diperjelas. Saya menyarankan penulis untuk memberikan justifikasi yang lebih kuat atas pemilihan metode yang digunakan.',
        'Tinjauan pustaka sudah cukup komprehensif, tetapi perlu diperbarui dengan literatur terbaru (2022–2025). Analisis data sudah baik, namun interpretasi hasil perlu diperdalam terutama pada diskusi implikasi praktis.',
        'Naskah ini sudah ditulis dengan baik dan terstruktur. Beberapa saran minor: (1) perjelas definisi operasional variabel utama, (2) tambahkan limitasi penelitian, (3) simpulan perlu lebih spesifik mengacu pada tujuan penelitian.',
        'The manuscript presents interesting findings but requires significant revision. The theoretical framework needs strengthening, and the statistical analysis has some inconsistencies that must be addressed before publication.',
        'This is a well-written paper with clear methodology. Minor revisions are needed: please clarify the sampling procedure and add more discussion on the limitations and future research directions.',
    ];

    private static array $editorComments = [
        'Secara umum naskah layak untuk dipertimbangkan setelah revisi. Penulis diminta memperhatikan semua komentar reviewer dan melakukan revisi yang substantif.',
        'Reviewer memberikan masukan yang konstruktif. Saya setuju bahwa revisi mayor diperlukan terutama pada bagian metodologi dan analisis.',
        'The paper has potential but needs significant improvement in the theoretical grounding and discussion sections.',
    ];

    public function definition(): array
    {
        return [
            'recommendation'       => fake()->randomElement(['accept', 'pending_revisions', 'resubmit_here', 'resubmit_elsewhere', 'decline', 'see_comments']),
            'comments_for_author'  => fake()->randomElement(self::$authorComments),
            'comments_for_editors' => fake()->optional(0.6)->randomElement(self::$editorComments),
            'form_responses'       => null,
            'reviewed_file_id'     => null,
        ];
    }

    public function accept(): static
    {
        return $this->state(fn () => ['recommendation' => 'accept']);
    }

    public function minorRevisions(): static
    {
        return $this->state(fn () => ['recommendation' => 'pending_revisions']);
    }

    public function majorRevisions(): static
    {
        return $this->state(fn () => ['recommendation' => 'resubmit_here']);
    }

    public function decline(): static
    {
        return $this->state(fn () => ['recommendation' => 'decline']);
    }
}
