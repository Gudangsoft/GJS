<?php

namespace App\Filament\Pages;

use App\Models\Journal;
use App\Models\JournalSidebarBlock;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

class PluginManager extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationLabel  = 'Plugin Manager';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?int    $navigationSort   = 5;
    protected static ?string $title            = 'Plugin Manager';
    protected string         $view             = 'filament.pages.plugin-manager';

    public ?int    $selectedJournalId = null;
    public ?string $installType       = null;

    /** Available plugin definitions */
    public static function availablePlugins(): array
    {
        return [
            [
                'type'        => 'journal_info',
                'name'        => 'Informasi Jurnal',
                'description' => 'Menampilkan nama, ISSN cetak, e-ISSN, penerbit, dan mode peer review jurnal.',
                'icon'        => 'clipboard-document-list',
                'color'       => '#1d4ed8',
                'bg'          => '#eff6ff',
                'default_settings' => [
                    'show_issn_print' => true, 'show_issn_online' => true,
                    'show_publisher' => true,  'show_review_mode' => true,
                    'show_frequency' => false,
                ],
            ],
            [
                'type'        => 'accreditation',
                'name'        => 'Akreditasi & Indeksasi',
                'description' => 'Badge SINTA, nomor SK akreditasi, periode, dan daftar indeksasi (Garuda, DOAJ, dll.).',
                'icon'        => 'academic-cap',
                'color'       => '#15803d',
                'bg'          => '#f0fdf4',
                'default_settings' => [
                    'show_sinta' => true, 'show_accreditation_no' => true,
                    'show_garuda' => true, 'show_google_scholar' => true,
                    'show_sinta_score' => true, 'show_doaj' => false,
                ],
            ],
            [
                'type'        => 'submission',
                'name'        => 'Kirim Naskah',
                'description' => 'Tombol ajakan submit dengan teks call-for-papers yang dapat dikustomisasi.',
                'icon'        => 'pencil-square',
                'color'       => '#7c3aed',
                'bg'          => '#faf5ff',
                'default_settings' => [
                    'call_text'    => 'Jurnal kami membuka penerimaan naskah ilmiah secara berkala.',
                    'button_label' => 'Kirim Naskah',
                ],
            ],
            [
                'type'        => 'article_template',
                'name'        => 'Template Artikel',
                'description' => 'Tombol unduh template Word (.docx) dan panduan penulisan PDF bagi penulis.',
                'icon'        => 'document-arrow-down',
                'color'       => '#b45309',
                'bg'          => '#fffbeb',
                'default_settings' => [
                    'label_docx' => 'Unduh Template (DOCX)',
                    'label_pdf'  => 'Panduan Penulisan (PDF)',
                ],
            ],
            [
                'type'        => 'statistics',
                'name'        => 'Statistik Jurnal',
                'description' => 'Menampilkan total artikel, terbitan, tayangan, unduhan, dan sitasi jurnal.',
                'icon'        => 'chart-bar',
                'color'       => '#0891b2',
                'bg'          => '#ecfeff',
                'default_settings' => [
                    'show_articles' => true, 'show_issues' => true,
                    'show_views' => true,    'show_downloads' => true,
                    'show_citations' => true,
                ],
            ],
            [
                'type'        => 'focus_scope',
                'name'        => 'Fokus & Ruang Lingkup',
                'description' => 'Menampilkan teks Fokus & Ruang Lingkup dari profil jurnal secara otomatis.',
                'icon'        => 'magnifying-glass-circle',
                'color'       => '#0f766e',
                'bg'          => '#f0fdfa',
                'default_settings' => [],
            ],
            [
                'type'        => 'custom_html',
                'name'        => 'Konten Bebas (HTML)',
                'description' => 'Blok konten fleksibel dengan rich text editor — untuk pengumuman, banner, atau info khusus.',
                'icon'        => 'code-bracket',
                'color'       => '#475569',
                'bg'          => '#f8fafc',
                'default_settings' => ['html' => ''],
            ],
        ];
    }

    public function getJournals(): Collection
    {
        return Journal::orderBy('name')->get(['id', 'name', 'name_abbrev', 'sinta_level']);
    }

    public function getActiveBlocks(): Collection
    {
        if (! $this->selectedJournalId) {
            return collect();
        }
        return JournalSidebarBlock::where('journal_id', $this->selectedJournalId)
            ->orderBy('sort_order')
            ->get();
    }

    public function installPlugin(string $type): void
    {
        if (! $this->selectedJournalId) {
            Notification::make()->title('Pilih jurnal terlebih dahulu')->warning()->send();
            return;
        }

        $plugin = collect(self::availablePlugins())->firstWhere('type', $type);
        if (! $plugin) return;

        $maxOrder = JournalSidebarBlock::where('journal_id', $this->selectedJournalId)->max('sort_order') ?? 0;

        JournalSidebarBlock::create([
            'journal_id' => $this->selectedJournalId,
            'type'       => $type,
            'title'      => null,
            'settings'   => $plugin['default_settings'],
            'enabled'    => true,
            'sort_order' => $maxOrder + 1,
        ]);

        Notification::make()
            ->title('Plugin "' . $plugin['name'] . '" berhasil dipasang')
            ->success()
            ->send();
    }

    public function toggleBlock(int $blockId): void
    {
        $block = JournalSidebarBlock::find($blockId);
        if ($block && $block->journal_id === $this->selectedJournalId) {
            $block->update(['enabled' => ! $block->enabled]);
            Notification::make()
                ->title($block->enabled ? 'Blok diaktifkan' : 'Blok dinonaktifkan')
                ->success()->send();
        }
    }

    public function deleteBlock(int $blockId): void
    {
        $block = JournalSidebarBlock::find($blockId);
        if ($block && $block->journal_id === $this->selectedJournalId) {
            $block->delete();
            Notification::make()->title('Blok dihapus')->success()->send();
        }
    }

    public function moveUp(int $blockId): void
    {
        $block = JournalSidebarBlock::find($blockId);
        if (!$block) return;
        $prev = JournalSidebarBlock::where('journal_id', $this->selectedJournalId)
            ->where('sort_order', '<', $block->sort_order)
            ->orderByDesc('sort_order')->first();
        if ($prev) {
            [$block->sort_order, $prev->sort_order] = [$prev->sort_order, $block->sort_order];
            $block->save(); $prev->save();
        }
    }

    public function moveDown(int $blockId): void
    {
        $block = JournalSidebarBlock::find($blockId);
        if (!$block) return;
        $next = JournalSidebarBlock::where('journal_id', $this->selectedJournalId)
            ->where('sort_order', '>', $block->sort_order)
            ->orderBy('sort_order')->first();
        if ($next) {
            [$block->sort_order, $next->sort_order] = [$next->sort_order, $block->sort_order];
            $block->save(); $next->save();
        }
    }
}
