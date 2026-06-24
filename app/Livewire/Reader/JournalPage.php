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

    public function mount(Journal $journal, string $page = 'about'): void
    {
        $validPages = ['about','editorial-team','guidelines','reviewer-guidelines','ethics','privacy','contact','submissions'];
        abort_unless(in_array($page, $validPages), 404);
        $this->journal = $journal;
        $this->page    = $page;
    }

    public function render()
    {
        $pageConfig = [
            'about'               => ['title' => 'Tentang Jurnal',            'icon' => 'info'],
            'editorial-team'      => ['title' => 'Tim Editorial',              'icon' => 'users'],
            'guidelines'          => ['title' => 'Panduan Penulis',            'icon' => 'book-open'],
            'reviewer-guidelines' => ['title' => 'Panduan Reviewer',           'icon' => 'clipboard'],
            'ethics'              => ['title' => 'Etika Publikasi',            'icon' => 'shield'],
            'privacy'             => ['title' => 'Kebijakan Privasi',          'icon' => 'lock'],
            'contact'             => ['title' => 'Kontak',                     'icon' => 'mail'],
            'submissions'         => ['title' => 'Panduan Pengiriman Naskah',  'icon' => 'upload'],
        ];

        $current = $pageConfig[$this->page] ?? $pageConfig['about'];

        return view('livewire.reader.journal-page', [
            'pageConfig'  => $current,
            'allPages'    => $pageConfig,
        ])->title($current['title'] . ' — ' . $this->journal->name);
    }
}
