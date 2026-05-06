<?php

namespace Database\Factories;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Submission> */
class SubmissionFactory extends Factory
{
    private static array $titles = [
        ['title' => 'Pengaruh Media Sosial terhadap Perilaku Konsumen di Era Digital', 'locale' => 'id', 'keywords' => ['media sosial', 'perilaku konsumen', 'digital marketing', 'e-commerce']],
        ['title' => 'Implementasi Machine Learning untuk Deteksi Penyakit Tanaman Berbasis Citra', 'locale' => 'id', 'keywords' => ['machine learning', 'deteksi penyakit', 'pengolahan citra', 'pertanian cerdas']],
        ['title' => 'Analisis Faktor-Faktor yang Mempengaruhi Kualitas Air Sungai di Daerah Perkotaan', 'locale' => 'id', 'keywords' => ['kualitas air', 'sungai', 'pencemaran', 'perkotaan', 'lingkungan']],
        ['title' => 'Efektivitas Model Pembelajaran Berbasis Proyek terhadap Kemampuan Berpikir Kritis Siswa', 'locale' => 'id', 'keywords' => ['pembelajaran berbasis proyek', 'berpikir kritis', 'pendidikan', 'inovasi']],
        ['title' => 'Pengembangan Sistem Informasi Manajemen Rumah Sakit Berbasis Web', 'locale' => 'id', 'keywords' => ['sistem informasi', 'rumah sakit', 'manajemen', 'web', 'kesehatan']],
        ['title' => 'Perbandingan Algoritma Deep Learning untuk Klasifikasi Sentimen pada Ulasan Produk', 'locale' => 'id', 'keywords' => ['deep learning', 'klasifikasi sentimen', 'NLP', 'ulasan produk']],
        ['title' => 'Studi Keragaman Hayati Terumbu Karang di Perairan Kepulauan Seribu', 'locale' => 'id', 'keywords' => ['terumbu karang', 'keragaman hayati', 'ekologi laut', 'konservasi']],
        ['title' => 'Penerapan Prinsip Green Building dalam Desain Gedung Pemerintah', 'locale' => 'id', 'keywords' => ['green building', 'bangunan hijau', 'efisiensi energi', 'arsitektur berkelanjutan']],
        ['title' => 'Analisis Kebijakan Perlindungan Data Pribadi di Indonesia Pasca UU PDP', 'locale' => 'id', 'keywords' => ['perlindungan data', 'kebijakan', 'hukum digital', 'privasi']],
        ['title' => 'Dampak Pandemi COVID-19 terhadap Ketahanan Pangan Rumah Tangga di Pedesaan', 'locale' => 'id', 'keywords' => ['COVID-19', 'ketahanan pangan', 'pedesaan', 'dampak pandemi']],
        ['title' => 'Optimization of Convolutional Neural Networks for Real-Time Object Detection', 'locale' => 'en', 'keywords' => ['CNN', 'object detection', 'deep learning', 'optimization', 'real-time']],
        ['title' => 'Sustainable Agricultural Practices and Soil Carbon Sequestration in Tropical Regions', 'locale' => 'en', 'keywords' => ['sustainable agriculture', 'carbon sequestration', 'tropical', 'soil health']],
        ['title' => 'The Impact of Digital Transformation on Organizational Performance in Public Sector', 'locale' => 'en', 'keywords' => ['digital transformation', 'organizational performance', 'public sector', 'e-government']],
        ['title' => 'Phytoremediation Potential of Native Plants for Heavy Metal Contaminated Soils', 'locale' => 'en', 'keywords' => ['phytoremediation', 'heavy metals', 'contamination', 'native plants', 'bioremediation']],
        ['title' => 'Blockchain Technology for Transparent Supply Chain Management in Agri-Food Sectors', 'locale' => 'en', 'keywords' => ['blockchain', 'supply chain', 'agri-food', 'transparency', 'traceability']],
        ['title' => 'Peran Kecerdasan Buatan dalam Transformasi Layanan Kesehatan Primer', 'locale' => 'id', 'keywords' => ['kecerdasan buatan', 'layanan kesehatan', 'transformasi digital', 'AI kesehatan']],
        ['title' => 'Evaluasi Program Beasiswa terhadap Angka Putus Sekolah di Daerah Terpencil', 'locale' => 'id', 'keywords' => ['beasiswa', 'putus sekolah', 'daerah terpencil', 'pendidikan inklusif']],
        ['title' => 'Karakterisasi Senyawa Bioaktif Ekstrak Daun Sirsak sebagai Antioksidan', 'locale' => 'id', 'keywords' => ['sirsak', 'bioaktif', 'antioksidan', 'fitokimia', 'ekstraksi']],
        ['title' => 'Pengembangan Aplikasi Mobile untuk Pemantauan Kesehatan Ibu Hamil', 'locale' => 'id', 'keywords' => ['aplikasi mobile', 'kesehatan ibu', 'kehamilan', 'monitoring', 'maternal']],
        ['title' => 'Analisis Deformasi Jembatan Gantung Menggunakan Metode Elemen Hingga', 'locale' => 'id', 'keywords' => ['jembatan gantung', 'deformasi', 'metode elemen hingga', 'struktur']],
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $data     = self::$titles[self::$index % count(self::$titles)];
        self::$index++;

        $submittedAt = fake()->dateTimeBetween('-18 months', '-1 month');

        return [
            'status'         => 'submitted',
            'title'          => $data['title'],
            'subtitle'       => fake()->optional(0.3)->sentence(6),
            'abstract'       => fake()->paragraphs(3, true),
            'keywords'       => $data['keywords'],
            'disciplines'    => fake()->optional(0.5)->randomElements(['Ilmu Komputer', 'Teknik Informatika', 'Sistem Informasi', 'Matematika', 'Statistika', 'Biologi', 'Kimia', 'Fisika'], 2),
            'subjects'       => null,
            'languages'      => [$data['locale']],
            'locale'         => $data['locale'],
            'doi'            => null,
            'submission_type'=> 'article',
            'hide_author'    => false,
            'competing_interests' => '<p>Para penulis menyatakan tidak ada konflik kepentingan dalam penelitian ini.</p>',
            'submitted_at'   => $submittedAt,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft', 'submitted_at' => null]);
    }

    public function inReview(): static
    {
        return $this->state(fn () => ['status' => 'review']);
    }

    public function accepted(): static
    {
        return $this->state(fn () => ['status' => 'accepted']);
    }

    public function copyediting(): static
    {
        return $this->state(fn () => ['status' => 'copyediting']);
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => 'published',
            'doi'    => '10.12345/gjs.' . fake()->numerify('####') . '.' . fake()->numerify('####'),
        ]);
    }

    public function declined(): static
    {
        return $this->state(fn () => ['status' => 'declined']);
    }
}
