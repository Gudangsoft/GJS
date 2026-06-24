<?php

namespace Database\Factories;

use App\Models\Journal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Journal> */
class JournalFactory extends Factory
{
    private static array $journals = [
        [
            'name'        => 'Jurnal Ilmu Komputer dan Informatika',
            'abbrev'      => 'JIKI',
            'issn_print'  => '2301-1234',
            'issn_online' => '2302-5678',
            'publisher'   => 'Universitas Indonesia',
            'focus'       => 'Ilmu komputer, kecerdasan buatan, jaringan komputer, rekayasa perangkat lunak, dan sistem informasi.',
        ],
        [
            'name'        => 'Jurnal Kesehatan Masyarakat Indonesia',
            'abbrev'      => 'JKMI',
            'issn_print'  => '2303-2211',
            'issn_online' => '2304-3322',
            'publisher'   => 'Universitas Gadjah Mada',
            'focus'       => 'Kesehatan masyarakat, epidemiologi, gizi, promosi kesehatan, dan kebijakan kesehatan.',
        ],
        [
            'name'        => 'Jurnal Teknik Sipil dan Lingkungan',
            'abbrev'      => 'JTSL',
            'issn_print'  => '2305-4455',
            'issn_online' => '2306-5566',
            'publisher'   => 'Institut Teknologi Bandung',
            'focus'       => 'Teknik sipil, rekayasa lingkungan, manajemen konstruksi, dan infrastruktur.',
        ],
        [
            'name'        => 'Jurnal Pendidikan dan Kebudayaan',
            'abbrev'      => 'JPK',
            'issn_print'  => '2307-6677',
            'issn_online' => '2308-7788',
            'publisher'   => 'Universitas Pendidikan Indonesia',
            'focus'       => 'Pendidikan, kurikulum, pedagogi, kebudayaan, dan kebijakan pendidikan nasional.',
        ],
        [
            'name'        => 'Indonesian Journal of Agricultural Science',
            'abbrev'      => 'IJAS',
            'issn_print'  => '2309-8899',
            'issn_online' => '2310-9900',
            'publisher'   => 'Institut Pertanian Bogor',
            'focus'       => 'Agricultural science, food science, plant biotechnology, and sustainable farming.',
        ],
    ];

    private static int $journalIndex = 0;

    public function definition(): array
    {
        $data  = self::$journals[self::$journalIndex % count(self::$journals)];
        self::$journalIndex++;

        $name  = $data['name'];
        $slug  = Str::slug($data['abbrev'] ?? $name);

        return [
            'slug'            => $slug,
            'name'            => $name,
            'name_abbrev'     => $data['abbrev'],
            'description'     => fake()->paragraph(3),
            'issn_print'      => $data['issn_print'],
            'issn_online'     => $data['issn_online'],
            'publisher'       => $data['publisher'],
            'email'           => 'editor@'.Str::slug($data['abbrev'] ?? $name).'.id',
            'url'             => 'https://journal.'.Str::slug($data['abbrev'] ?? $name).'.id',
            'primary_locale'  => str_contains($name, 'Indonesian') ? 'en' : 'id',
            'supported_locales' => ['id', 'en'],
            'country'         => 'ID',
            'timezone'        => 'Asia/Jakarta',
            'status'          => 'active',
            'enabled'         => true,
            'focus_scope'     => '<p>'.$data['focus'].'</p>',
            'ethics_statement'=> '<p>Jurnal ini berkomitmen pada standar etika publikasi ilmiah yang tinggi. Penulis, reviewer, dan editor diharapkan mematuhi pedoman COPE (Committee on Publication Ethics).</p>',
            'author_guidelines' => '<p>Naskah harus ditulis dalam Bahasa Indonesia atau Inggris dengan format APA 7th edition. Panjang naskah antara 4.000–8.000 kata, termasuk referensi.</p><ul><li>Abstrak maksimal 250 kata</li><li>Kata kunci 3–6 kata</li><li>Referensi minimal 20 sumber primer</li></ul>',
            'reviewer_guidelines' => '<p>Reviewer dimohon menyelesaikan review dalam 4 minggu. Penilaian meliputi orisinalitas, metodologi, relevansi, dan kualitas penulisan.</p>',
            'privacy_statement' => '<p>Data pribadi yang diberikan kepada jurnal ini hanya digunakan untuk tujuan editorial dan tidak akan dibagikan kepada pihak ketiga.</p>',
            'about_journal'   => '<p>'.ucfirst($data['publisher']).' menerbitkan '.$name.' sebagai wadah diseminasi hasil penelitian berkualitas tinggi.</p>',
            'review_mode'     => fake()->randomElement(['double_blind', 'double_blind', 'double_blind', 'single_blind', 'open']),
            'num_weeks_per_review'   => fake()->randomElement([3, 4, 4, 6]),
            'num_weeks_per_response' => fake()->randomElement([2, 3, 3]),
            'requires_author_competinginterests'   => true,
            'requires_reviewer_competinginterests' => true,
        ];
    }
}
