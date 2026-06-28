<?php

namespace App\Livewire\JournalManager;

use App\Models\Article;
use App\Models\ArticleGalley;
use App\Models\Journal;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.manager')]
class ArticleGalleys extends Component
{
    public Article $article;

    // Form state
    public bool   $showForm    = false;
    public ?int   $editId      = null;
    public string $label       = '';
    public string $locale      = 'id';
    public string $remoteUrl   = '';
    public string $htmlContent = '';
    public int    $sequence    = 0;
    public bool   $isApproved  = true;

    public function mount(Article $article): void
    {
        $this->article = $article->load(['galleys', 'submission', 'issue']);
        abort_unless($this->activeJournal()?->id === $article->journal_id, 403);
    }

    private function activeJournal(): ?Journal
    {
        $id = session('manager_active_journal');
        return $id ? Journal::find($id) : null;
    }

    public function openCreate(): void
    {
        $this->reset(['editId','label','locale','remoteUrl','htmlContent','sequence','isApproved']);
        $this->locale     = 'id';
        $this->isApproved = true;
        $this->showForm   = true;
    }

    public function openEdit(int $id): void
    {
        $galley = ArticleGalley::findOrFail($id);
        abort_unless($galley->article_id === $this->article->id, 403);

        $this->editId      = $id;
        $this->label       = $galley->label;
        $this->locale      = $galley->locale ?? 'id';
        $this->remoteUrl   = $galley->remote_url ?? '';
        $this->htmlContent = $galley->html_content ?? '';
        $this->sequence    = (int) $galley->sequence;
        $this->isApproved  = $galley->is_approved;
        $this->showForm    = true;
    }

    public function save(): void
    {
        $this->validate([
            'label'     => 'required|string|max:32',
            'locale'    => 'required|string|max:14',
            'remoteUrl' => 'nullable|url|max:512',
            'sequence'  => 'integer|min:0',
        ]);

        $data = [
            'label'        => trim($this->label),
            'locale'       => $this->locale,
            'remote_url'   => $this->remoteUrl ?: null,
            'html_content' => $this->htmlContent ?: null,
            'sequence'     => $this->sequence,
            'is_approved'  => $this->isApproved,
        ];

        if ($this->editId) {
            $galley = ArticleGalley::findOrFail($this->editId);
            abort_unless($galley->article_id === $this->article->id, 403);
            $galley->update($data);
        } else {
            ArticleGalley::create(array_merge($data, ['article_id' => $this->article->id]));
        }

        $this->showForm = false;
        $this->article->refresh()->load('galleys');
        session()->flash('success', 'Galley berhasil disimpan.');
    }

    public function delete(int $id): void
    {
        $galley = ArticleGalley::findOrFail($id);
        abort_unless($galley->article_id === $this->article->id, 403);
        $galley->delete();
        $this->article->refresh()->load('galleys');
        session()->flash('success', 'Galley dihapus.');
    }

    public function render()
    {
        return view('livewire.journal-manager.article-galleys', [
            'journal' => $this->activeJournal(),
            'galleys' => $this->article->galleys,
        ]);
    }
}
