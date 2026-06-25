<?php

namespace App\Livewire\Reader;

use App\Models\Journal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class JournalPage extends Component
{
    public Journal $journal;
    public string  $page = 'about';
    public ?array  $customPageData = null;

    protected static array $presetPages = [
        'about', 'editorial-team', 'guidelines', 'reviewer-guidelines',
        'ethics', 'privacy', 'contact', 'submissions',
    ];

    public function mount(Journal $journal, string $page = 'about'): void
    {
        $this->journal = $journal;
        $this->page    = $page;

        if (! in_array($page, self::$presetPages)) {
            $customPages = $journal->settings['custom_pages'] ?? [];
            $found = collect($customPages)->firstWhere('slug', $page);
            abort_if(! $found || ! ($found['enabled'] ?? true), 404);
            $this->customPageData = $found;
        }
    }

    public function render()
    {
        $presetConfig = [
            'about'               => ['title' => 'Tentang Jurnal',           'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            'editorial-team'      => ['title' => 'Tim Editorial',             'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            'guidelines'          => ['title' => 'Panduan Penulis',           'icon' => 'M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25'],
            'reviewer-guidelines' => ['title' => 'Panduan Reviewer',          'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
            'ethics'              => ['title' => 'Etika Publikasi',           'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            'privacy'             => ['title' => 'Kebijakan Privasi',         'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
            'contact'             => ['title' => 'Kontak',                    'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
            'submissions'         => ['title' => 'Panduan Pengiriman Naskah', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12'],
        ];

        $customPages = collect($this->journal->settings['custom_pages'] ?? [])
            ->filter(fn($p) => $p['enabled'] ?? true)
            ->values()
            ->all();

        if ($this->customPageData) {
            $pageTitle = $this->customPageData['title'];
        } else {
            $pageTitle = $presetConfig[$this->page]['title'] ?? 'Halaman Jurnal';
        }

        return view('livewire.reader.journal-page', [
            'presetConfig' => $presetConfig,
            'customPages'  => $customPages,
        ])->title($pageTitle . ' — ' . $this->journal->name);
    }
}
