<?php

namespace App\Filament\Pages;

use App\Models\Journal;
use App\Models\JournalSidebarBlock;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class PluginManager extends Page
{
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationLabel                = 'Plugin Manager';
    protected static string|\UnitEnum|null $navigationGroup  = 'Pengaturan';
    protected static ?int    $navigationSort                  = 5;
    protected static ?string $title                          = 'Plugin Manager — Sidebar Jurnal';
    protected string         $view                           = 'filament.pages.plugin-manager';

    public ?int    $selectedJournalId = null;
    public ?string $confirmInstallType = null;

    public static function availablePlugins(): array
    {
        return [
            [
                'type'        => 'journal_info',
                'name'        => 'Informasi Jurnal',
                'description' => 'Nama, ISSN cetak/online, penerbit, mode peer review.',
                'icon'        => 'clipboard-document-list',
                'color'       => '#1d4ed8',
                'bg'          => '#eff6ff',
                'unique'      => true,
                'default_settings' => [
                    'show_issn_print'   => true,
                    'show_issn_online'  => true,
                    'show_publisher'    => true,
                    'show_review_mode'  => true,
                    'show_frequency'    => false,
                ],
            ],
            [
                'type'        => 'accreditation',
                'name'        => 'Akreditasi & Indeksasi',
                'description' => 'Badge SINTA, nomor SK, periode akreditasi, dan indeks (Garuda, DOAJ, dll.).',
                'icon'        => 'academic-cap',
                'color'       => '#15803d',
                'bg'          => '#f0fdf4',
                'unique'      => true,
                'default_settings' => [
                    'show_sinta'            => true,
                    'show_accreditation_no' => true,
                    'show_garuda'           => true,
                    'show_google_scholar'   => true,
                    'show_sinta_score'      => true,
                    'show_doaj'             => false,
                ],
            ],
            [
                'type'        => 'submission',
                'name'        => 'Kirim Naskah',
                'description' => 'Tombol call-for-papers dengan teks yang dapat dikustomisasi.',
                'icon'        => 'pencil-square',
                'color'       => '#7c3aed',
                'bg'          => '#faf5ff',
                'unique'      => true,
                'default_settings' => [
                    'call_text'    => 'Jurnal kami membuka penerimaan naskah ilmiah secara berkala.',
                    'button_label' => 'Kirim Naskah',
                ],
            ],
            [
                'type'        => 'focus_scope',
                'name'        => 'Fokus & Ruang Lingkup',
                'description' => 'Teks Fokus & Ruang Lingkup dari profil jurnal, tampil otomatis.',
                'icon'        => 'magnifying-glass-circle',
                'color'       => '#0f766e',
                'bg'          => '#f0fdfa',
                'unique'      => true,
                'default_settings' => [],
            ],
            [
                'type'        => 'statistics',
                'name'        => 'Statistik Jurnal',
                'description' => 'Total artikel, terbitan, tayangan, unduhan, dan sitasi jurnal.',
                'icon'        => 'chart-bar',
                'color'       => '#0891b2',
                'bg'          => '#ecfeff',
                'unique'      => true,
                'default_settings' => [
                    'show_articles'   => true,
                    'show_issues'     => true,
                    'show_views'      => false,
                    'show_downloads'  => false,
                    'show_citations'  => false,
                ],
            ],
            [
                'type'        => 'article_template',
                'name'        => 'Template Artikel',
                'description' => 'Tombol unduh template DOCX dan panduan penulisan PDF.',
                'icon'        => 'document-arrow-down',
                'color'       => '#b45309',
                'bg'          => '#fffbeb',
                'unique'      => false,
                'default_settings' => [
                    'label_docx' => 'Unduh Template (DOCX)',
                    'label_pdf'  => 'Panduan Penulisan (PDF)',
                ],
            ],
            [
                'type'        => 'custom_html',
                'name'        => 'Konten Bebas (HTML)',
                'description' => 'Blok konten bebas — untuk pengumuman, banner, atau info khusus.',
                'icon'        => 'code-bracket',
                'color'       => '#475569',
                'bg'          => '#f8fafc',
                'unique'      => false,
                'default_settings' => ['html' => ''],
            ],
        ];
    }

    public function getJournals(): Collection
    {
        return Journal::orderBy('name')->get(['id', 'name', 'name_abbrev', 'sinta_level', 'logo']);
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

    public function getInstalledCounts(): array
    {
        if (! $this->selectedJournalId) return [];
        return JournalSidebarBlock::where('journal_id', $this->selectedJournalId)
            ->selectRaw('type, COUNT(*) as cnt')
            ->groupBy('type')
            ->pluck('cnt', 'type')
            ->toArray();
    }

    public function installPlugin(string $type): void
    {
        if (! $this->selectedJournalId) {
            Notification::make()->title('Pilih jurnal terlebih dahulu')->warning()->send();
            return;
        }

        $plugin = collect(self::availablePlugins())->firstWhere('type', $type);
        if (! $plugin) return;

        $maxOrder = JournalSidebarBlock::where('journal_id', $this->selectedJournalId)
            ->max('sort_order') ?? 0;

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
            ->body('Klik ikon ⚙ untuk mengkonfigurasi plugin.')
            ->success()
            ->send();
    }

    public function installAll(): void
    {
        if (! $this->selectedJournalId) {
            Notification::make()->title('Pilih jurnal terlebih dahulu')->warning()->send();
            return;
        }

        $installed = 0;
        $order = JournalSidebarBlock::where('journal_id', $this->selectedJournalId)
            ->max('sort_order') ?? 0;

        $existingTypes = JournalSidebarBlock::where('journal_id', $this->selectedJournalId)
            ->pluck('type')->toArray();

        foreach (self::availablePlugins() as $plugin) {
            if ($plugin['type'] === 'custom_html') continue;
            if (in_array($plugin['type'], $existingTypes)) continue;

            JournalSidebarBlock::create([
                'journal_id' => $this->selectedJournalId,
                'type'       => $plugin['type'],
                'title'      => null,
                'settings'   => $plugin['default_settings'],
                'enabled'    => true,
                'sort_order' => ++$order,
            ]);
            $installed++;
        }

        if ($installed === 0) {
            Notification::make()->title('Semua plugin sudah terpasang')->info()->send();
        } else {
            Notification::make()
                ->title("{$installed} plugin berhasil dipasang sekaligus")
                ->success()->send();
        }
    }

    public function toggleBlock(int $blockId): void
    {
        $block = JournalSidebarBlock::find($blockId);
        if ($block && $block->journal_id === $this->selectedJournalId) {
            $block->update(['enabled' => ! $block->enabled]);
            Notification::make()
                ->title($block->enabled ? 'Plugin diaktifkan' : 'Plugin dinonaktifkan')
                ->success()->send();
        }
    }

    public function deleteBlock(int $blockId): void
    {
        $block = JournalSidebarBlock::find($blockId);
        if ($block && $block->journal_id === $this->selectedJournalId) {
            $name = $block->getDisplayTitle();
            $block->delete();
            Notification::make()->title("Plugin \"{$name}\" dihapus")->success()->send();
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

    protected function getViewData(): array
    {
        return [
            'journals'        => $this->getJournals(),
            'activeBlocks'    => $this->getActiveBlocks(),
            'installedCounts' => $this->getInstalledCounts(),
            'allPlugins'      => static::availablePlugins(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('install_all')
                ->label('Pasang Semua Plugin')
                ->icon('heroicon-o-squares-plus')
                ->color('gray')
                ->visible(fn () => (bool) $this->selectedJournalId)
                ->requiresConfirmation()
                ->modalHeading('Pasang semua plugin?')
                ->modalDescription('Semua plugin yang belum terpasang (kecuali Konten Bebas) akan dipasang sekaligus ke jurnal yang dipilih.')
                ->action('installAll'),
        ];
    }
}
