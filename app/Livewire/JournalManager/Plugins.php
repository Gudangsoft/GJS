<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use App\Models\JournalSidebarBlock;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class Plugins extends Component
{
    // Editing state
    public ?int    $editingBlockId = null;
    public string  $editTitle      = '';
    public array   $editSettings   = [];
    public bool    $showEditForm   = false;

    public static function availablePlugins(): array
    {
        return [
            [
                'type'        => 'journal_info',
                'name'        => 'Informasi Jurnal',
                'description' => 'Menampilkan nama, ISSN cetak, e-ISSN, penerbit, dan mode peer review jurnal.',
                'color'       => '#1d4ed8', 'bg' => '#eff6ff',
                'default_settings' => [
                    'show_issn_print' => true, 'show_issn_online' => true,
                    'show_publisher' => true, 'show_review_mode' => true,
                ],
                'settings_schema' => [
                    ['key' => 'show_issn_print',  'label' => 'Tampilkan ISSN Cetak',    'type' => 'toggle'],
                    ['key' => 'show_issn_online',  'label' => 'Tampilkan e-ISSN',        'type' => 'toggle'],
                    ['key' => 'show_publisher',    'label' => 'Tampilkan Penerbit',       'type' => 'toggle'],
                    ['key' => 'show_review_mode',  'label' => 'Tampilkan Mode Review',   'type' => 'toggle'],
                ],
            ],
            [
                'type'        => 'accreditation',
                'name'        => 'Akreditasi & Indeksasi',
                'description' => 'Badge SINTA, nomor SK akreditasi, dan daftar indeksasi.',
                'color'       => '#15803d', 'bg' => '#f0fdf4',
                'default_settings' => [
                    'show_sinta' => true, 'show_accreditation_no' => true,
                    'show_garuda' => true, 'show_google_scholar' => true,
                ],
                'settings_schema' => [
                    ['key' => 'show_sinta',            'label' => 'Tampilkan Badge SINTA',           'type' => 'toggle'],
                    ['key' => 'url_sinta',             'label' => '↳ Link SINTA (URL profil)',       'type' => 'text'],
                    ['key' => 'show_accreditation_no', 'label' => 'Tampilkan No. SK Akreditasi',     'type' => 'toggle'],
                    ['key' => 'url_sk',                'label' => '↳ Link Dokumen SK (PDF/URL)',     'type' => 'text'],
                    ['key' => 'show_garuda',           'label' => 'Tampilkan Indeks Garuda',         'type' => 'toggle'],
                    ['key' => 'url_garuda',            'label' => '↳ Link Garuda (URL profil)',      'type' => 'text'],
                    ['key' => 'show_google_scholar',   'label' => 'Tampilkan Google Scholar',        'type' => 'toggle'],
                    ['key' => 'url_google_scholar',    'label' => '↳ Link Google Scholar (URL)',     'type' => 'text'],
                    ['key' => 'show_doaj',             'label' => 'Tampilkan Indeks DOAJ',           'type' => 'toggle'],
                    ['key' => 'url_doaj',              'label' => '↳ Link DOAJ (URL profil)',        'type' => 'text'],
                    ['key' => 'show_scopus',           'label' => 'Tampilkan Scopus',                'type' => 'toggle'],
                    ['key' => 'url_scopus',            'label' => '↳ Link Scopus (URL profil)',      'type' => 'text'],
                    ['key' => 'show_wos',              'label' => 'Tampilkan Web of Science',        'type' => 'toggle'],
                    ['key' => 'url_wos',               'label' => '↳ Link Web of Science (URL)',     'type' => 'text'],
                    ['key' => 'extra_indexes',         'label' => 'Indeksasi Tambahan Lainnya',      'type' => 'index_list'],
                ],
            ],
            [
                'type'        => 'submission',
                'name'        => 'Kirim Naskah',
                'description' => 'Tombol ajakan submit dengan teks call-for-papers yang dapat dikustomisasi.',
                'color'       => '#7c3aed', 'bg' => '#faf5ff',
                'default_settings' => [
                    'call_text'    => 'Jurnal kami membuka penerimaan naskah ilmiah secara berkala.',
                    'button_label' => 'Kirim Naskah',
                ],
                'settings_schema' => [
                    ['key' => 'call_text',    'label' => 'Teks Ajakan',      'type' => 'textarea'],
                    ['key' => 'button_label', 'label' => 'Label Tombol',     'type' => 'text'],
                    ['key' => 'button_url',   'label' => 'URL Kustom (opsional)', 'type' => 'text'],
                ],
            ],
            [
                'type'        => 'article_template',
                'name'        => 'Template Artikel',
                'description' => 'Tombol unduh template Word (.docx) dan panduan penulisan PDF.',
                'color'       => '#b45309', 'bg' => '#fffbeb',
                'default_settings' => [
                    'label_docx' => 'Unduh Template (DOCX)',
                    'label_pdf'  => 'Panduan Penulisan (PDF)',
                ],
                'settings_schema' => [
                    ['key' => 'url_docx',   'label' => 'URL File DOCX', 'type' => 'text'],
                    ['key' => 'label_docx', 'label' => 'Label DOCX',    'type' => 'text'],
                    ['key' => 'url_pdf',    'label' => 'URL File PDF',  'type' => 'text'],
                    ['key' => 'label_pdf',  'label' => 'Label PDF',     'type' => 'text'],
                ],
            ],
            [
                'type'        => 'statistics',
                'name'        => 'Statistik Jurnal',
                'description' => 'Total artikel, terbitan, tayangan, unduhan, dan sitasi jurnal.',
                'color'       => '#0891b2', 'bg' => '#ecfeff',
                'default_settings' => [
                    'show_articles' => true, 'show_issues' => true,
                    'show_views' => true, 'show_downloads' => true,
                ],
                'settings_schema' => [
                    ['key' => 'show_articles',  'label' => 'Tampilkan Total Artikel',  'type' => 'toggle'],
                    ['key' => 'show_issues',    'label' => 'Tampilkan Total Terbitan', 'type' => 'toggle'],
                    ['key' => 'show_views',     'label' => 'Tampilkan Tayangan',       'type' => 'toggle'],
                    ['key' => 'show_downloads', 'label' => 'Tampilkan Unduhan',        'type' => 'toggle'],
                ],
            ],
            [
                'type'        => 'focus_scope',
                'name'        => 'Fokus & Ruang Lingkup',
                'description' => 'Menampilkan teks Fokus & Ruang Lingkup dari profil jurnal secara otomatis.',
                'color'       => '#0f766e', 'bg' => '#f0fdfa',
                'default_settings' => [],
                'settings_schema' => [],
            ],
            [
                'type'        => 'current_issue',
                'name'        => 'Terbitan Saat Ini',
                'description' => 'Cover dan info terbitan aktif (volume, nomor, tahun) + tombol lihat terbitan.',
                'color'       => '#0369a1', 'bg' => '#f0f9ff',
                'default_settings' => ['show_cover' => true, 'show_toc_preview' => true, 'max_articles' => 5],
                'settings_schema' => [
                    ['key' => 'show_cover',        'label' => 'Tampilkan Cover Terbitan', 'type' => 'toggle'],
                    ['key' => 'show_toc_preview',  'label' => 'Tampilkan Pratinjau TOC',  'type' => 'toggle'],
                    ['key' => 'max_articles',       'label' => 'Jumlah Artikel Ditampilkan', 'type' => 'text'],
                ],
            ],
            [
                'type'        => 'most_read',
                'name'        => 'Artikel Terpopuler',
                'description' => 'Daftar artikel dengan tayangan atau unduhan terbanyak.',
                'color'       => '#b45309', 'bg' => '#fffbeb',
                'default_settings' => ['count' => 5, 'metric' => 'views'],
                'settings_schema' => [
                    ['key' => 'count',  'label' => 'Jumlah Artikel', 'type' => 'text'],
                    ['key' => 'metric', 'label' => 'Urutkan Berdasarkan (views / downloads)', 'type' => 'text'],
                ],
            ],
            [
                'type'        => 'recent_articles',
                'name'        => 'Artikel Terbaru',
                'description' => 'Artikel yang baru-baru ini diterbitkan.',
                'color'       => '#0891b2', 'bg' => '#ecfeff',
                'default_settings' => ['count' => 5],
                'settings_schema' => [
                    ['key' => 'count', 'label' => 'Jumlah Artikel', 'type' => 'text'],
                ],
            ],
            [
                'type'        => 'keyword_cloud',
                'name'        => 'Kata Kunci Populer',
                'description' => 'Awan kata kunci dari seluruh artikel yang diterbitkan.',
                'color'       => '#6d28d9', 'bg' => '#f5f3ff',
                'default_settings' => ['max_keywords' => 30],
                'settings_schema' => [
                    ['key' => 'max_keywords', 'label' => 'Maksimal Kata Kunci', 'type' => 'text'],
                ],
            ],
            [
                'type'        => 'announcements_list',
                'name'        => 'Pengumuman Terbaru',
                'description' => 'Pengumuman jurnal terbaru dengan link ke halaman penuh.',
                'color'       => '#0f766e', 'bg' => '#f0fdfa',
                'default_settings' => ['count' => 3, 'show_date' => true],
                'settings_schema' => [
                    ['key' => 'count',     'label' => 'Jumlah Pengumuman', 'type' => 'text'],
                    ['key' => 'show_date', 'label' => 'Tampilkan Tanggal', 'type' => 'toggle'],
                ],
            ],
            [
                'type'        => 'open_access',
                'name'        => 'Akses Terbuka & Lisensi',
                'description' => 'Pernyataan Open Access dan badge lisensi Creative Commons.',
                'color'       => '#15803d', 'bg' => '#f0fdf4',
                'default_settings' => ['license' => 'cc_by', 'show_statement' => true],
                'settings_schema' => [
                    ['key' => 'license',         'label' => 'Tipe Lisensi CC (cc_by / cc_by_nc / cc_by_nc_nd)', 'type' => 'text'],
                    ['key' => 'show_statement',   'label' => 'Tampilkan Pernyataan OA', 'type' => 'toggle'],
                    ['key' => 'custom_statement', 'label' => 'Teks Pernyataan Kustom (opsional)', 'type' => 'textarea'],
                ],
            ],
            [
                'type'        => 'peer_review',
                'name'        => 'Proses Peer Review',
                'description' => 'Penjelasan singkat proses penelaahan sejawat jurnal.',
                'color'       => '#1d4ed8', 'bg' => '#eff6ff',
                'default_settings' => ['show_mode' => true, 'show_duration' => true, 'custom_text' => ''],
                'settings_schema' => [
                    ['key' => 'show_mode',     'label' => 'Tampilkan Mode Review',     'type' => 'toggle'],
                    ['key' => 'show_duration', 'label' => 'Tampilkan Durasi Review',   'type' => 'toggle'],
                    ['key' => 'custom_text',   'label' => 'Teks Tambahan (opsional)',  'type' => 'textarea'],
                ],
            ],
            [
                'type'        => 'social_links',
                'name'        => 'Media Sosial',
                'description' => 'Ikon dan tautan ke akun media sosial jurnal.',
                'color'       => '#db2777', 'bg' => '#fdf2f8',
                'default_settings' => [],
                'settings_schema' => [
                    ['key' => 'url_twitter',    'label' => 'Twitter / X (URL)',    'type' => 'text'],
                    ['key' => 'url_facebook',   'label' => 'Facebook (URL)',        'type' => 'text'],
                    ['key' => 'url_instagram',  'label' => 'Instagram (URL)',       'type' => 'text'],
                    ['key' => 'url_youtube',    'label' => 'YouTube (URL)',         'type' => 'text'],
                    ['key' => 'url_linkedin',   'label' => 'LinkedIn (URL)',        'type' => 'text'],
                    ['key' => 'url_telegram',   'label' => 'Telegram (URL/channel)','type' => 'text'],
                ],
            ],
            [
                'type'        => 'preservation',
                'name'        => 'Pengarsipan & Indeksasi Digital',
                'description' => 'Badge LOCKSS, PKP PN, Portico, CLOCKSS dan layanan pengarsipan lainnya.',
                'color'       => '#374151', 'bg' => '#f9fafb',
                'default_settings' => ['show_lockss' => false, 'show_pkp_pn' => false, 'show_portico' => false, 'show_clockss' => false],
                'settings_schema' => [
                    ['key' => 'show_lockss',   'label' => 'LOCKSS',   'type' => 'toggle'],
                    ['key' => 'url_lockss',    'label' => '↳ Link LOCKSS',  'type' => 'text'],
                    ['key' => 'show_pkp_pn',   'label' => 'PKP PN',   'type' => 'toggle'],
                    ['key' => 'show_portico',  'label' => 'Portico',  'type' => 'toggle'],
                    ['key' => 'url_portico',   'label' => '↳ Link Portico', 'type' => 'text'],
                    ['key' => 'show_clockss',  'label' => 'CLOCKSS',  'type' => 'toggle'],
                    ['key' => 'show_cope',     'label' => 'COPE',     'type' => 'toggle'],
                    ['key' => 'url_cope',      'label' => '↳ Link COPE',    'type' => 'text'],
                    ['key' => 'show_crossref', 'label' => 'CrossRef', 'type' => 'toggle'],
                    ['key' => 'show_mendeley', 'label' => 'Mendeley', 'type' => 'toggle'],
                ],
            ],
            [
                'type'        => 'custom_html',
                'name'        => 'Konten Bebas (HTML)',
                'description' => 'Blok konten fleksibel — untuk pengumuman, banner, atau info khusus.',
                'color'       => '#475569', 'bg' => '#f8fafc',
                'default_settings' => ['title' => '', 'html' => ''],
                'settings_schema' => [
                    ['key' => 'html', 'label' => 'Konten HTML', 'type' => 'html'],
                ],
            ],
        ];
    }

    protected function getJournal(): ?Journal
    {
        $journals = Journal::whereHas('managers', fn($q) => $q->where('users.id', auth()->id()))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', auth()->id()))
            ->get();
        $activeId = session('manager_active_journal');
        return $journals->firstWhere('id', $activeId) ?? $journals->first();
    }

    public function installPlugin(string $type): void
    {
        $journal = $this->getJournal();
        if (!$journal) return;

        $plugin = collect(self::availablePlugins())->firstWhere('type', $type);
        if (!$plugin) return;

        $maxOrder = JournalSidebarBlock::where('journal_id', $journal->id)->max('sort_order') ?? 0;

        JournalSidebarBlock::create([
            'journal_id' => $journal->id,
            'type'       => $type,
            'title'      => null,
            'settings'   => $plugin['default_settings'],
            'enabled'    => true,
            'sort_order' => $maxOrder + 1,
        ]);

        $this->dispatch('toast', message: 'Plugin "' . $plugin['name'] . '" berhasil dipasang.', type: 'success');
    }

    public function toggleBlock(int $blockId): void
    {
        $journal = $this->getJournal();
        $block = JournalSidebarBlock::find($blockId);
        if ($block && $journal && $block->journal_id === $journal->id) {
            $block->update(['enabled' => !$block->enabled]);
        }
    }

    public function openEdit(int $blockId): void
    {
        $journal = $this->getJournal();
        $block = JournalSidebarBlock::find($blockId);
        if (!$block || !$journal || $block->journal_id !== $journal->id) return;

        $this->editingBlockId = $blockId;
        $this->editTitle      = $block->title ?? '';
        $this->editSettings   = $block->settings ?? [];
        $this->showEditForm   = true;
    }

    public function saveEdit(): void
    {
        $journal = $this->getJournal();
        $block = JournalSidebarBlock::find($this->editingBlockId);
        if (!$block || !$journal || $block->journal_id !== $journal->id) return;

        $block->update([
            'title'    => $this->editTitle ?: null,
            'settings' => $this->editSettings,
        ]);

        $this->showEditForm   = false;
        $this->editingBlockId = null;
        $this->dispatch('toast', message: 'Blok berhasil diperbarui.', type: 'success');
    }

    public function addExtraIndex(): void
    {
        $this->editSettings['extra_indexes'][] = ['label' => '', 'url' => ''];
    }

    public function removeExtraIndex(int $i): void
    {
        $list = $this->editSettings['extra_indexes'] ?? [];
        array_splice($list, $i, 1);
        $this->editSettings['extra_indexes'] = array_values($list);
    }

    public function updateOrder(array $order): void
    {
        $journal = $this->getJournal();
        if (!$journal) return;

        foreach ($order as $i => $blockId) {
            JournalSidebarBlock::where('id', $blockId)
                ->where('journal_id', $journal->id)
                ->update(['sort_order' => $i + 1]);
        }
    }

    public function deleteBlock(int $blockId): void
    {
        $journal = $this->getJournal();
        $block = JournalSidebarBlock::find($blockId);
        if ($block && $journal && $block->journal_id === $journal->id) {
            $block->delete();
            $this->dispatch('toast', message: 'Blok dihapus.', type: 'success');
        }
    }

    protected static array $defaultTypes = [
        'journal_info', 'accreditation', 'submission', 'statistics',
    ];

    protected function seedDefaultBlocks(Journal $journal): void
    {
        $available = collect(self::availablePlugins())->keyBy('type');
        foreach (self::$defaultTypes as $i => $type) {
            $plugin = $available[$type] ?? null;
            if (!$plugin) continue;
            JournalSidebarBlock::create([
                'journal_id' => $journal->id,
                'type'       => $type,
                'title'      => null,
                'settings'   => $plugin['default_settings'],
                'enabled'    => true,
                'sort_order' => $i + 1,
            ]);
        }
    }

    public function render()
    {
        $journal = $this->getJournal();

        if ($journal) {
            $count = JournalSidebarBlock::where('journal_id', $journal->id)->count();
            if ($count === 0) {
                $this->seedDefaultBlocks($journal);
            }
        }

        $blocks = $journal
            ? JournalSidebarBlock::where('journal_id', $journal->id)->orderBy('sort_order')->get()
            : collect();

        $installedTypes = $blocks->pluck('type')->toArray();
        $available      = collect(self::availablePlugins());
        $pluginMap      = $available->keyBy('type');

        return view('livewire.journal-manager.plugins', compact('journal', 'blocks', 'available', 'installedTypes', 'pluginMap'))
            ->title('Plugin Sidebar — Panel Pengelola');
    }
}