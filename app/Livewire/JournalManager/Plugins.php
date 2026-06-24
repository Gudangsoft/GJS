<?php

namespace App\Livewire\JournalManager;

use App\Models\Journal;
use App\Models\JournalSidebarBlock;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.manager')]
class Plugins extends Component
{
    public string $flash = '';

    public static function availablePlugins(): array
    {
        return [
            [
                'type'        => 'journal_info',
                'name'        => 'Informasi Jurnal',
                'description' => 'Menampilkan nama, ISSN cetak, e-ISSN, penerbit, dan mode peer review jurnal.',
                'color'       => '#1d4ed8',
                'bg'          => '#eff6ff',
                'default_settings' => [
                    'show_issn_print' => true, 'show_issn_online' => true,
                    'show_publisher' => true, 'show_review_mode' => true,
                ],
            ],
            [
                'type'        => 'accreditation',
                'name'        => 'Akreditasi & Indeksasi',
                'description' => 'Badge SINTA, nomor SK akreditasi, dan daftar indeksasi.',
                'color'       => '#15803d',
                'bg'          => '#f0fdf4',
                'default_settings' => [
                    'show_sinta' => true, 'show_accreditation_no' => true,
                    'show_garuda' => true, 'show_google_scholar' => true,
                ],
            ],
            [
                'type'        => 'submission',
                'name'        => 'Kirim Naskah',
                'description' => 'Tombol ajakan submit dengan teks call-for-papers yang dapat dikustomisasi.',
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
                'description' => 'Tombol unduh template Word (.docx) dan panduan penulisan PDF.',
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
                'description' => 'Total artikel, terbitan, tayangan, unduhan, dan sitasi jurnal.',
                'color'       => '#0891b2',
                'bg'          => '#ecfeff',
                'default_settings' => [
                    'show_articles' => true, 'show_issues' => true,
                    'show_views' => true, 'show_downloads' => true,
                ],
            ],
            [
                'type'        => 'focus_scope',
                'name'        => 'Fokus & Ruang Lingkup',
                'description' => 'Menampilkan teks Fokus & Ruang Lingkup dari profil jurnal secara otomatis.',
                'color'       => '#0f766e',
                'bg'          => '#f0fdfa',
                'default_settings' => [],
            ],
            [
                'type'        => 'custom_html',
                'name'        => 'Konten Bebas (HTML)',
                'description' => 'Blok konten fleksibel — untuk pengumuman, banner, atau info khusus.',
                'color'       => '#475569',
                'bg'          => '#f8fafc',
                'default_settings' => ['html' => ''],
            ],
        ];
    }

    protected function getJournal(): ?Journal
    {
        return Journal::whereHas('managers', fn($q) => $q->where('users.id', auth()->id()))
            ->orWhereHas('editors', fn($q) => $q->where('users.id', auth()->id()))
            ->first();
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

        session()->flash('success', 'Plugin "' . $plugin['name'] . '" berhasil dipasang.');
    }

    public function toggleBlock(int $blockId): void
    {
        $journal = $this->getJournal();
        $block = JournalSidebarBlock::find($blockId);
        if ($block && $journal && $block->journal_id === $journal->id) {
            $block->update(['enabled' => !$block->enabled]);
        }
    }

    public function deleteBlock(int $blockId): void
    {
        $journal = $this->getJournal();
        $block = JournalSidebarBlock::find($blockId);
        if ($block && $journal && $block->journal_id === $journal->id) {
            $block->delete();
            session()->flash('success', 'Blok dihapus.');
        }
    }

    public function moveUp(int $blockId): void
    {
        $journal = $this->getJournal();
        if (!$journal) return;
        $block = JournalSidebarBlock::find($blockId);
        if (!$block) return;
        $prev = JournalSidebarBlock::where('journal_id', $journal->id)
            ->where('sort_order', '<', $block->sort_order)
            ->orderByDesc('sort_order')->first();
        if ($prev) {
            [$block->sort_order, $prev->sort_order] = [$prev->sort_order, $block->sort_order];
            $block->save();
            $prev->save();
        }
    }

    public function moveDown(int $blockId): void
    {
        $journal = $this->getJournal();
        if (!$journal) return;
        $block = JournalSidebarBlock::find($blockId);
        if (!$block) return;
        $next = JournalSidebarBlock::where('journal_id', $journal->id)
            ->where('sort_order', '>', $block->sort_order)
            ->orderBy('sort_order')->first();
        if ($next) {
            [$block->sort_order, $next->sort_order] = [$next->sort_order, $block->sort_order];
            $block->save();
            $next->save();
        }
    }

    public function render()
    {
        $journal = $this->getJournal();
        $blocks  = $journal
            ? JournalSidebarBlock::where('journal_id', $journal->id)->orderBy('sort_order')->get()
            : collect();

        $installedTypes = $blocks->pluck('type')->toArray();
        $available = collect(self::availablePlugins());

        return view('livewire.journal-manager.plugins', compact('journal', 'blocks', 'available', 'installedTypes'))
            ->title('Plugin Sidebar — Panel Pengelola');
    }
}
