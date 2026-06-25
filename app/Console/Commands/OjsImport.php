<?php

namespace App\Console\Commands;

use App\Models\Journal;
use App\Services\OjsImportService;
use Illuminate\Console\Command;

class OjsImport extends Command
{
    protected $signature = 'ojs:import
        {--url=      : URL dasar OJS (contoh: https://jurnal.example.com/index.php/jiki)}
        {--journal=  : ID atau slug jurnal di GJS}
        {--api-key=  : API key OJS (opsional)}
        {--only=     : Pilih: sections,issues,articles (default: semua)}
        {--test      : Hanya tes koneksi ke OJS API}';

    protected $description = 'Import data jurnal dari OJS 3.x via REST API';

    public function handle(): int
    {
        $url    = $this->option('url');
        $jParam = $this->option('journal');
        $apiKey = $this->option('api-key');
        $test   = (bool) $this->option('test');
        $only   = $this->option('only')
            ? array_map('trim', explode(',', $this->option('only')))
            : ['sections', 'issues', 'articles'];

        if (!$url) {
            $url = $this->ask('URL dasar OJS (contoh: https://jurnal.example.com/index.php/jiki)');
        }
        if (!$url) {
            $this->error('URL OJS diperlukan.');
            return self::FAILURE;
        }

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

        if ($apiKey === null) {
            $apiKey = $this->ask('API key OJS (Enter untuk skip)') ?: null;
        }

        $this->info("Jurnal GJS : [{$gjsJournal->id}] {$gjsJournal->name}");
        $this->info("URL OJS    : {$url}");
        $this->info("API Key    : " . ($apiKey ? str_repeat('*', max(0, strlen($apiKey) - 4)) . substr($apiKey, -4) : '(tidak ada)'));
        $this->newLine();

        $service = new OjsImportService($url, $gjsJournal->id, $apiKey);

        $conn = $service->testConnection();
        if ($conn['ok']) {
            $this->info('✓ ' . $conn['message']);
        } else {
            $this->error('✗ ' . $conn['message']);
            return self::FAILURE;
        }

        if ($test) return self::SUCCESS;

        if (!$this->confirm("Lanjutkan import ke jurnal '{$gjsJournal->name}'?", true)) {
            return self::SUCCESS;
        }

        $this->newLine();

        if (in_array('sections', $only)) {
            $this->line('<fg=cyan>▸ Mengimpor seksi...</>');
            $service->importSections();
            $this->line("  <fg=green>✓ Seksi baru: {$service->sectionsCreated}</>");
        }

        if (in_array('issues', $only)) {
            $this->line('<fg=cyan>▸ Mengimpor edisi...</>');
            $service->importIssues();
            $this->line("  <fg=green>✓ Edisi baru: {$service->issuesCreated} | Diperbarui: {$service->issuesSkipped}</>");
        }

        if (in_array('articles', $only)) {
            $this->line('<fg=cyan>▸ Mengimpor artikel...</>');
            $service->importArticles();
            $this->line("  <fg=green>✓ Artikel baru: {$service->articlesCreated} | Diperbarui: {$service->articlesUpdated} | Error: {$service->errors}</>");
        }

        $this->newLine();
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
