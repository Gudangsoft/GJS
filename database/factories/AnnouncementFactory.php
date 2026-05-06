<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Announcement> */
class AnnouncementFactory extends Factory
{
    private static array $announcements = [
        [
            'title' => 'Panggilan Naskah Vol. Berikutnya',
            'short' => 'Redaksi membuka penerimaan naskah untuk volume berikutnya. Batas akhir pengiriman naskah adalah 3 bulan dari sekarang.',
            'desc'  => '<p>Redaksi jurnal dengan hormat mengundang para peneliti, akademisi, dan praktisi untuk mengirimkan naskah ilmiah terbaiknya untuk diterbitkan pada volume berikutnya.</p><p>Topik yang relevan meliputi semua bidang yang sesuai dengan fokus dan ruang lingkup jurnal ini.</p><p>Naskah harus ditulis mengikuti panduan penulis yang tersedia di laman jurnal.</p>',
        ],
        [
            'title' => 'Edisi Terbaru Telah Diterbitkan',
            'short' => 'Edisi terbaru jurnal telah resmi diterbitkan. Pembaca dapat mengakses seluruh artikel secara online.',
            'desc'  => '<p>Dengan bangga kami mengumumkan bahwa edisi terbaru jurnal ini telah resmi diterbitkan dan dapat diakses secara daring. Edisi ini memuat sejumlah artikel berkualitas tinggi dari berbagai institusi terkemuka.</p><p>Kami mengucapkan terima kasih kepada para penulis, reviewer, dan editor yang telah berkontribusi.</p>',
        ],
        [
            'title' => 'Pemeliharaan Sistem Jurnal',
            'short' => 'Sistem jurnal akan mengalami pemeliharaan terjadwal. Layanan pengiriman naskah mungkin terganggu selama periode tersebut.',
            'desc'  => '<p>Kami menginformasikan bahwa sistem jurnal akan menjalani pemeliharaan terjadwal. Selama periode ini, beberapa layanan mungkin tidak tersedia atau berjalan lambat.</p><p>Kami mohon maaf atas ketidaknyamanan ini dan berterima kasih atas pengertian Anda.</p>',
        ],
        [
            'title' => 'Rekrutmen Reviewer Baru',
            'short' => 'Redaksi membuka pendaftaran reviewer baru untuk memperkuat tim mitra bebestari jurnal ini.',
            'desc'  => '<p>Jurnal ini membuka kesempatan bagi para peneliti dan akademisi yang kompeten untuk bergabung sebagai reviewer (mitra bebestari). Reviewer baru diharapkan memiliki keahlian di bidang yang relevan dengan ruang lingkup jurnal.</p><p>Untuk mendaftar, silakan hubungi redaksi melalui email yang tertera di halaman kontak.</p>',
        ],
        [
            'title' => 'Perubahan Format Submission',
            'short' => 'Terhitung mulai edisi berikutnya, format pengiriman naskah menggunakan template baru yang telah diperbarui.',
            'desc'  => '<p>Redaksi menginformasikan bahwa mulai edisi berikutnya, seluruh naskah harus menggunakan template baru yang telah diperbarui. Template dapat diunduh dari halaman panduan penulis.</p><p>Naskah yang tidak menggunakan template terbaru akan dikembalikan untuk penyesuaian format sebelum proses review dimulai.</p>',
        ],
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $data      = self::$announcements[self::$index % count(self::$announcements)];
        self::$index++;

        $datePosted = fake()->dateTimeBetween('-12 months', 'now');

        return [
            'title'            => $data['title'],
            'description_short'=> $data['short'],
            'description'      => $data['desc'],
            'date_expire'      => fake()->optional(0.4)->dateTimeBetween('now', '+6 months'),
            'date_posted'      => $datePosted,
            'send_email'       => fake()->boolean(30),
        ];
    }
}
