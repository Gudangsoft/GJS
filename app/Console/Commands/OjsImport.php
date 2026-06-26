<?php

namespace App\Console\Commands;

use App\Models\Journal;
use App\Services\OjsImportService;
use Illuminate\Console\Command;

class OjsImport extends Command
{
    protected $signature = 'ojs:import
        {--url=      : URL OAI-PMH atau REST API OJS}
        {--journal=  : ID atau slug jurnal di GJS}
        {--method=   : oai (default) | rest | crossref}
        {--api-key=  : API key OJS REST (opsional)}
        {--issn=     : ISSN jurnal (untuk metode crossref)}
        {--only=     : Pilih: sections,issues,articles (hanya untuk metode rest)}
        {--test      : Hanya tes koneksi}';

    protected $description = 'Import data jurnal dari OJS via OAI-PMH, CrossRef, atau REST API';

    public function handle(): int
    {
        $method = $this->option('method') ?: 'oai';
        $url    = $this->option('url');
        $jParam = $this->option('journal');
        $apiKey = $this->option('api-key');
        $issn   = $this->option('issn');
        $test   = (bool)$this->option('test');
        $only   = $this->option('only')
            ? array_map('trim', explode(',', $this->option('only')))
            : ['sections', 'issues', 'articles'];

        // Resolve journal
        if (!$jParam) {
            $journals = Journal::orderBy('name')->get(['id', 'name', 'slug']);
            $choices  = $journals->map(fn($j) => "[{$j->id}] {$j->name} ({$j->slug})")->toArray();
            $choice   = $this->choice('Pilih jurnal tujuan di GJS', $choices);
            preg_match('/^\[(\d+)\]/', $choice, $m);
            $jParam   = $m[1] ?? null;
        }

        $gjsJournal = is_numeric($jParam)
            ? Journal::find((int)$jParam)
            : Journal::where('slug', $jParam)->first();

        if (!$gjsJournal) {
            $this->error("Jurnal '{$jParam}' tidak ditemukan di GJS.");
            return self::FAILURE;
        }

        $service = new OjsImportService($gjsJournal->id);

        $this->line("<fg=cyan>Metode   :</> {$method}");
        $this->line("<fg=cyan>Jurnal GJS:</> [{$gjsJournal->id}] {$gjsJournal->name}");

        // ── OAI-PMH
        if ($method === 'oai') {
            if (!$url) {
                $url = $this->ask('URL OAI-PMH (contoh: https://jurnal.example.com/index.php/jiki/oai)');
            }
            if (!$url) { $this->error('URL OAI-PMH diperlukan.'); return self::FAILURE; }

            $this->line("<fg=cyan>URL OAI  :</> {$url}");
            $this->newLine();

            $conn = $service->testOaiConnection($url);
            if ($conn['ok']) {
                $this->info('✓ ' . $conn['message']);
            } else {
                $this->error('✗ ' . $conn['message']);
                return self::FAILURE;
            }
            if ($test) return self::SUCCESS;
            if (!$this->confirm("Lanjutkan import ke '{$gjsJournal->name}'?", true)) return self::SUCCESS;

            $this->newLine();
            $this->line('<fg=cyan>▸ Mengimpor via OAI-PMH...</>');
            $service->importFromOai($url, null, function ($type, $data) {
                $this->getOutput()->write('.');
            });
        }

        // ── CrossRef
        elseif ($method === 'crossref') {
            if (!$issn) {
                $issn = $this->ask('ISSN jurnal (cetak atau online)');
            }
            if (!$issn) { $this->error('ISSN diperlukan.'); return self::FAILURE; }

            $this->line("<fg=cyan>ISSN     :</> {$issn}");
            $this->newLine();

            $conn = $service->testCrossrefByIssn($issn);
            if ($conn['ok']) {
                $this->info('✓ ' . $conn['message']);
            } else {
                $this->error('✗ ' . $conn['message']);
                return self::FAILURE;
            }
            if ($test) return self::SUCCESS;
            if (!$this->confirm("Lanjutkan import ke '{$gjsJournal->name}'?", true)) return self::SUCCESS;

            $this->newLine();
            $this->line('<fg=cyan>▸ Mengimpor via CrossRef...</>');
            $service->importFromCrossref($issn, function ($type, $data) {
                $this->getOutput()->write('.');
            });
        }

        // ── OJS REST API
        else {
            if (!$url) {
                $url = $this->ask('URL dasar OJS (contoh: https://jurnal.example.com/index.php/jiki)');
            }
            if (!$url) { $this->error('URL OJS diperlukan.'); return self::FAILURE; }
            if ($apiKey === null) {
                $apiKey = $this->ask('API key OJS (Enter untuk skip)') ?: null;
            }

            $this->line("<fg=cyan>URL OJS  :</> {$url}");
            $this->newLine();

            $service->setRestConfig($url, $apiKey);
            $conn = $service->testRestConnection();
            if ($conn['ok']) {
                $this->info('✓ ' . $conn['message']);
            } else {
                $this->error('✗ ' . $conn['message']);
                return self::FAILURE;
            }
            if ($test) return self::SUCCESS;
            if (!$this->confirm("Lanjutkan import ke '{$gjsJournal->name}'?", true)) return self::SUCCESS;

            $this->newLine();
            $service->importFromRest($only);
            $this->line("  <fg=green>✓ Seksi: {$service->sectionsCreated} | Edisi: {$service->issuesCreated} | Artikel: {$service->articlesCreated} / diperbarui: {$service->articlesUpdated}</>");
        }

        $this->newLine(2);
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Edisi baru',     $service->issuesCreated],
                ['Artikel baru',   $service->articlesCreated],
                ['Diperbarui',     $service->articlesUpdated],
                ['Error',          $service->errors],
            ]
        );

        foreach ($service->getLogs() as $entry) {
            match ($entry['level']) {
                'warn'  => $this->warn("  [{$entry['time']}] {$entry['msg']}"),
                'error' => $this->error("  [{$entry['time']}] {$entry['msg']}"),
                default => null,
            };
        }

        $this->info('Import selesai.');
        return self::SUCCESS;
    }
}
