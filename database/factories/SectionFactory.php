<?php

namespace Database\Factories;

use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Section> */
class SectionFactory extends Factory
{
    private static array $sections = [
        ['title' => 'Artikel Penelitian',    'abbrev' => 'AP',  'word_count' => 8000],
        ['title' => 'Tinjauan Pustaka',       'abbrev' => 'TP',  'word_count' => 6000],
        ['title' => 'Studi Kasus',            'abbrev' => 'SK',  'word_count' => 5000],
        ['title' => 'Komunikasi Singkat',     'abbrev' => 'KS',  'word_count' => 3000],
        ['title' => 'Editorial',              'abbrev' => 'ED',  'word_count' => 2000],
        ['title' => 'Catatan Lapangan',       'abbrev' => 'CL',  'word_count' => 4000],
        ['title' => 'Resensi Buku',           'abbrev' => 'RB',  'word_count' => 2500],
        ['title' => 'Research Article',       'abbrev' => 'RA',  'word_count' => 8000],
        ['title' => 'Review Article',         'abbrev' => 'REV', 'word_count' => 7000],
        ['title' => 'Short Communication',    'abbrev' => 'SC',  'word_count' => 3000],
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $data = self::$sections[self::$index % count(self::$sections)];
        self::$index++;

        return [
            'title'              => $data['title'],
            'abbrev'             => $data['abbrev'],
            'policy'             => '<p>Naskah yang dikirim ke seksi ini harus sesuai dengan fokus dan ruang lingkup jurnal. Penulis wajib memastikan keaslian karya dan belum diterbitkan sebelumnya.</p>',
            'abstract_word_count'=> true,
            'word_count'         => $data['word_count'],
            'hide_title'         => false,
            'hide_author'        => false,
            'is_inactive'        => false,
            'editor_restricted'  => false,
            'submitter_restricted'=> false,
            'sequence'           => self::$index,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_inactive' => true]);
    }
}
