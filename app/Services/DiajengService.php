<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiajengService
{
    private string $baseUrl;
    private string $apiKey;
    private int    $cacheTtl;
    private int    $timeout;

    public function __construct()
    {
        $this->baseUrl  = rtrim(config('services.diajeng.base_url', 'https://diajeng.lldikti6.id/api/v1'), '/');
        $this->apiKey   = config('services.diajeng.api_key', '');
        $this->cacheTtl = (int) config('services.diajeng.cache_ttl', 3600);
        $this->timeout  = (int) config('services.diajeng.timeout', 15);
    }

    // ── Konfigurasi ───────────────────────────────────────────────────────────

    public function isConfigured(): bool
    {
        return filled($this->apiKey);
    }

    // ── Journals ──────────────────────────────────────────────────────────────

    /**
     * Daftar semua jurnal terdaftar di DIAJENG.
     *
     * @param  array  $params  Query params: page, per_page, search, category_id, city
     */
    public function journals(array $params = []): array
    {
        return $this->cachedGet('journals', $params);
    }

    /**
     * Detail satu jurnal berdasarkan ID DIAJENG.
     */
    public function journal(int|string $id): array
    {
        return $this->cachedGet("journals/{$id}");
    }

    /**
     * Cari jurnal berdasarkan ISSN (print atau online).
     */
    public function findJournalByIssn(string $issn): ?array
    {
        $result = $this->cachedGet('journals', ['search' => $issn]);
        $items  = data_get($result, 'data', []);

        foreach ($items as $item) {
            if (($item['issn_print'] ?? '') === $issn || ($item['issn_online'] ?? '') === $issn) {
                return $item;
            }
        }
        return null;
    }

    // ── Articles ──────────────────────────────────────────────────────────────

    /**
     * Daftar artikel.
     *
     * @param  array  $params  page, per_page, search, journal_id, author_id, year
     */
    public function articles(array $params = []): array
    {
        return $this->cachedGet('articles', $params);
    }

    /**
     * Detail satu artikel.
     */
    public function article(int|string $id): array
    {
        return $this->cachedGet("articles/{$id}");
    }

    /**
     * Artikel berdasarkan DOI.
     */
    public function findArticleByDoi(string $doi): ?array
    {
        $result = $this->cachedGet('articles', ['search' => $doi]);
        $items  = data_get($result, 'data', []);

        foreach ($items as $item) {
            if (($item['doi'] ?? '') === $doi) {
                return $item;
            }
        }
        return null;
    }

    // ── Authors ───────────────────────────────────────────────────────────────

    /**
     * Daftar penulis.
     *
     * @param  array  $params  page, per_page, search
     */
    public function authors(array $params = []): array
    {
        return $this->cachedGet('authors', $params);
    }

    /**
     * Detail satu penulis.
     */
    public function author(int|string $id): array
    {
        return $this->cachedGet("authors/{$id}");
    }

    /**
     * Cari penulis berdasarkan nama atau email (tanpa cache — untuk lookup real-time).
     */
    public function searchAuthors(string $query): array
    {
        return $this->get('authors', ['search' => $query]);
    }

    // ── Categories ────────────────────────────────────────────────────────────

    /**
     * Daftar kategori/bidang jurnal.
     */
    public function categories(): array
    {
        return $this->cachedGet('categories');
    }

    // ── Connectivity test ─────────────────────────────────────────────────────

    /**
     * Cek koneksi dan validitas API key. Mengembalikan array status.
     */
    public function ping(): array
    {
        if (! $this->isConfigured()) {
            return ['ok' => false, 'message' => 'API key belum dikonfigurasi.'];
        }

        try {
            $response = $this->request('journals', ['per_page' => 1]);

            if ($response->successful()) {
                $total = data_get($response->json(), 'meta.total') ?? data_get($response->json(), 'total') ?? '?';
                return [
                    'ok'      => true,
                    'message' => "Terhubung ke DIAJENG. Total jurnal terdaftar: {$total}.",
                    'data'    => $response->json(),
                ];
            }

            return [
                'ok'      => false,
                'message' => "HTTP {$response->status()}: " . ($response->json('message') ?? $response->body()),
            ];
        } catch (ConnectionException $e) {
            return ['ok' => false, 'message' => 'Koneksi gagal: ' . $e->getMessage()];
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function cachedGet(string $endpoint, array $params = []): array
    {
        $key = 'diajeng:' . md5($endpoint . serialize($params));

        return Cache::remember($key, $this->cacheTtl, fn () => $this->get($endpoint, $params));
    }

    private function get(string $endpoint, array $params = []): array
    {
        try {
            return $this->request($endpoint, $params)->json() ?? [];
        } catch (ConnectionException $e) {
            Log::warning("DiajengService: gagal GET /{$endpoint}", ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage(), 'data' => []];
        }
    }

    private function request(string $endpoint, array $params = []): Response
    {
        return Http::timeout($this->timeout)
            ->withHeaders([
                'X-API-Key' => $this->apiKey,
                'Accept'    => 'application/json',
            ])
            ->get("{$this->baseUrl}/{$endpoint}", $params ?: null);
    }

    /** Hapus seluruh cache DIAJENG. */
    public function clearCache(): void
    {
        // Laravel file cache tidak mendukung pattern delete; flush tag jika pakai Redis
        // Untuk now: simpan daftar key di cache itu sendiri (alternatif: pakai tagged cache)
        Cache::flush(); // atau bisa di-scope ke prefix jika pakai Redis tags
    }
}
