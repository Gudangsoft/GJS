<?php

namespace App\Console\Commands;

use App\Services\DiajengService;
use Illuminate\Console\Command;

class DiajengSync extends Command
{
    protected $signature = 'diajeng:sync
                            {resource=journals : Resource yang di-sync: journals|articles|authors|categories}
                            {--search= : Kata kunci pencarian}
                            {--id= : ID spesifik}
                            {--page=1 : Nomor halaman}
                            {--per-page=20 : Jumlah data per halaman}
                            {--ping : Hanya test koneksi}
                            {--clear-cache : Hapus cache DIAJENG}';

    protected $description = 'Sinkronisasi / ambil data dari DIAJENG LLDIKTI6 API';

    public function __construct(private readonly DiajengService $diajeng)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('clear-cache')) {
            $this->diajeng->clearCache();
            $this->info('Cache DIAJENG berhasil dihapus.');
            return self::SUCCESS;
        }

        if ($this->option('ping')) {
            return $this->runPing();
        }

        if (! $this->diajeng->isConfigured()) {
            $this->error('DIAJENG_API_KEY belum dikonfigurasi di .env');
            return self::FAILURE;
        }

        $resource = $this->argument('resource');

        return match ($resource) {
            'journals'   => $this->fetchJournals(),
            'articles'   => $this->fetchArticles(),
            'authors'    => $this->fetchAuthors(),
            'categories' => $this->fetchCategories(),
            default      => $this->invalidResource($resource),
        };
    }

    private function runPing(): int
    {
        $this->info('Menghubungi DIAJENG API...');
        $result = $this->diajeng->ping();

        if ($result['ok']) {
            $this->info('✓ ' . $result['message']);
            return self::SUCCESS;
        }

        $this->error('✗ ' . $result['message']);
        return self::FAILURE;
    }

    private function fetchJournals(): int
    {
        $params = array_filter([
            'search'   => $this->option('search'),
            'page'     => $this->option('page'),
            'per_page' => $this->option('per-page'),
        ]);

        if ($id = $this->option('id')) {
            $this->info("Mengambil jurnal ID: {$id}");
            $data = $this->diajeng->journal($id);
            $this->printJson($data);
            return self::SUCCESS;
        }

        $this->info('Mengambil daftar jurnal dari DIAJENG...');
        $result = $this->diajeng->journals($params);

        $rows = collect(data_get($result, 'data', []))->map(fn ($j) => [
            data_get($j, 'id'),
            data_get($j, 'name') ?? data_get($j, 'title'),
            data_get($j, 'issn_print') ?? '-',
            data_get($j, 'issn_online') ?? '-',
            data_get($j, 'accreditation') ?? '-',
        ]);

        $this->table(['ID', 'Nama Jurnal', 'ISSN Cetak', 'E-ISSN', 'Akreditasi'], $rows);

        $total = data_get($result, 'meta.total') ?? data_get($result, 'total') ?? count($rows);
        $this->line("Total: {$total} jurnal");

        return self::SUCCESS;
    }

    private function fetchArticles(): int
    {
        $params = array_filter([
            'search'   => $this->option('search'),
            'page'     => $this->option('page'),
            'per_page' => $this->option('per-page'),
        ]);

        if ($id = $this->option('id')) {
            $this->info("Mengambil artikel ID: {$id}");
            $data = $this->diajeng->article($id);
            $this->printJson($data);
            return self::SUCCESS;
        }

        $this->info('Mengambil daftar artikel dari DIAJENG...');
        $result = $this->diajeng->articles($params);

        $rows = collect(data_get($result, 'data', []))->map(fn ($a) => [
            data_get($a, 'id'),
            mb_strimwidth(data_get($a, 'title') ?? '-', 0, 50, '…'),
            data_get($a, 'year') ?? '-',
            data_get($a, 'doi') ?? '-',
        ]);

        $this->table(['ID', 'Judul', 'Tahun', 'DOI'], $rows);

        $total = data_get($result, 'meta.total') ?? count($rows);
        $this->line("Total: {$total} artikel");

        return self::SUCCESS;
    }

    private function fetchAuthors(): int
    {
        $params = array_filter([
            'search'   => $this->option('search'),
            'page'     => $this->option('page'),
            'per_page' => $this->option('per-page'),
        ]);

        if ($id = $this->option('id')) {
            $this->info("Mengambil penulis ID: {$id}");
            $data = $this->diajeng->author($id);
            $this->printJson($data);
            return self::SUCCESS;
        }

        $this->info('Mengambil daftar penulis dari DIAJENG...');
        $result = $this->diajeng->authors($params);

        $rows = collect(data_get($result, 'data', []))->map(fn ($a) => [
            data_get($a, 'id'),
            data_get($a, 'name') ?? (data_get($a, 'first_name') . ' ' . data_get($a, 'last_name')),
            data_get($a, 'institution') ?? data_get($a, 'affiliation') ?? '-',
            data_get($a, 'email') ?? '-',
        ]);

        $this->table(['ID', 'Nama', 'Institusi', 'Email'], $rows);

        return self::SUCCESS;
    }

    private function fetchCategories(): int
    {
        $this->info('Mengambil kategori dari DIAJENG...');
        $result = $this->diajeng->categories();

        $rows = collect(data_get($result, 'data', $result))->map(fn ($c) => [
            data_get($c, 'id'),
            data_get($c, 'name') ?? data_get($c, 'title') ?? '-',
            data_get($c, 'journals_count') ?? '-',
        ]);

        $this->table(['ID', 'Kategori', 'Jumlah Jurnal'], $rows);

        return self::SUCCESS;
    }

    private function invalidResource(string $resource): int
    {
        $this->error("Resource tidak dikenal: {$resource}. Pilihan: journals, articles, authors, categories");
        return self::FAILURE;
    }

    private function printJson(array $data): void
    {
        $this->line(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
