<?php

namespace App\Filament\Resources\LetterOfAcceptances;

use App\Filament\Resources\LetterOfAcceptances\Pages;
use App\Models\Journal;
use App\Models\LetterOfAcceptance;
use App\Models\Submission;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class LetterOfAcceptanceResource extends Resource
{
    protected static ?string $model = LetterOfAcceptance::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';
    protected static ?string $navigationLabel  = 'Letter of Acceptance';
    protected static string|\UnitEnum|null $navigationGroup = 'Naskah';
    protected static ?string $pluralLabel      = 'Letter of Acceptance';
    protected static ?string $modelLabel       = 'LOA';
    protected static ?int    $navigationSort   = 3;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'draft')->count();
        return $count > 0 ? (string)$count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    // ─── Form ─────────────────────────────────────────────────────────────────

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Submission')
                ->description('Pilih jurnal dan naskah yang diterima')
                ->icon('heroicon-o-inbox-arrow-down')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('journal_id')
                            ->label('Jurnal')
                            ->options(Journal::orderBy('name')->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (callable $set) => $set('submission_id', null)),

                        Select::make('submission_id')
                            ->label('Naskah (yang telah diterima)')
                            ->required()
                            ->searchable()
                            ->options(function (callable $get) {
                                $jid = $get('journal_id');
                                if (!$jid) return [];
                                return Submission::where('journal_id', $jid)
                                    ->whereIn('status', ['accepted', 'copyediting', 'production', 'scheduled', 'published'])
                                    ->orderByDesc('submitted_at')
                                    ->get()
                                    ->mapWithKeys(fn($s) => [
                                        $s->id => '#' . $s->id . ' — ' . Str::limit($s->title, 70),
                                    ]);
                            })
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (!$state) return;
                                $sub = Submission::with('contributors')->find($state);
                                if (!$sub) return;

                                $set('article_title', $sub->title);

                                $authors = $sub->contributors
                                    ->sortBy('sequence')
                                    ->map(fn($c) => [
                                        'name'        => trim($c->first_name . ' ' . $c->last_name),
                                        'affiliation' => $c->affiliation ?? '',
                                    ])
                                    ->values()
                                    ->toArray();

                                if (count($authors) > 0) {
                                    $set('authors', $authors);
                                }
                            }),
                    ]),
                ]),

            Section::make('Detail LOA')
                ->description('Nomor, status, dan judul artikel')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('loa_number')
                            ->label('Nomor LOA')
                            ->placeholder('Otomatis: LOA/JIKI/2026/001')
                            ->unique(ignoreRecord: true)
                            ->helperText('Kosongkan untuk generate otomatis saat simpan'),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft'   => 'Draft',
                                'issued'  => 'Diterbitkan',
                                'revoked' => 'Dicabut',
                            ])
                            ->default('draft')
                            ->required()
                            ->native(false),
                    ]),

                    TextInput::make('article_title')
                        ->label('Judul Artikel')
                        ->required()
                        ->maxLength(500),
                ]),

            Section::make('Penulis (Authors)')
                ->description('Data penulis yang akan tercantum di dokumen LOA')
                ->icon('heroicon-o-users')
                ->schema([
                    Repeater::make('authors')
                        ->label('')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->placeholder('Dr. John Doe, M.Sc'),
                                TextInput::make('affiliation')
                                    ->label('Institusi / Afiliasi')
                                    ->placeholder('Universitas Indonesia, Jakarta'),
                            ]),
                        ])
                        ->addActionLabel('+ Tambah Penulis')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                        ->minItems(1),
                ]),

            Section::make('Jadwal Penerbitan')
                ->description('Tanggal penerimaan dan estimasi terbit')
                ->icon('heroicon-o-calendar-days')
                ->columns(3)
                ->schema([
                    DatePicker::make('acceptance_date')
                        ->label('Tanggal Diterima')
                        ->default(now())
                        ->required()
                        ->displayFormat('d M Y'),

                    DatePicker::make('expected_publication_date')
                        ->label('Estimasi Terbit')
                        ->displayFormat('d M Y'),

                    TextInput::make('year')
                        ->label('Tahun Penerbitan')
                        ->default(now()->year)
                        ->numeric(),

                    TextInput::make('volume')
                        ->label('Volume')
                        ->numeric(),

                    TextInput::make('number')
                        ->label('Nomor / Issue')
                        ->numeric(),
                ]),

            Section::make('Catatan')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->collapsed()
                ->schema([
                    Textarea::make('notes')
                        ->label('Catatan Tambahan (internal)')
                        ->rows(3)
                        ->placeholder('Tidak tampil di dokumen LOA'),
                ]),
        ]);
    }

    // ─── Table ────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loa_number')
                    ->label('Nomor LOA')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->fontFamily('mono')
                    ->copyable()
                    ->copyMessage('Nomor LOA disalin'),

                TextColumn::make('journal.name_abbrev')
                    ->label('Jurnal')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('article_title')
                    ->label('Judul Artikel')
                    ->limit(55)
                    ->searchable()
                    ->tooltip(fn ($record) => $record->article_title),

                TextColumn::make('authors')
                    ->label('Penulis')
                    ->formatStateUsing(function ($state) {
                        if (!is_array($state)) return $state ?? '—';
                        $names = array_map(fn($a) => is_array($a) ? ($a['name'] ?? '') : $a, $state);
                        return implode(', ', array_filter($names));
                    })
                    ->limit(35)
                    ->tooltip(function ($record) {
                        if (!is_array($record->authors)) return null;
                        return implode(' · ', array_map(
                            fn($a) => is_array($a)
                                ? ($a['name'] ?? '') . (($a['affiliation'] ?? '') ? ' (' . $a['affiliation'] . ')' : '')
                                : (string)$a,
                            $record->authors
                        ));
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'issued'  => 'success',
                        'draft'   => 'warning',
                        'revoked' => 'danger',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'issued'  => 'Diterbitkan',
                        'draft'   => 'Draft',
                        'revoked' => 'Dicabut',
                        default   => $state,
                    }),

                TextColumn::make('acceptance_date')
                    ->label('Tgl Diterima')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('expected_publication_date')
                    ->label('Est. Terbit')
                    ->date('M Y')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('volume')
                    ->label('Vol/No')
                    ->formatStateUsing(fn ($state, $record) => $state
                        ? 'Vol.' . $state . ' No.' . ($record->number ?? '?')
                        : '—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('issuedBy.name')
                    ->label('Diterbitkan Oleh')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('journal_id')
                    ->label('Jurnal')
                    ->options(Journal::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft'   => 'Draft',
                        'issued'  => 'Diterbitkan',
                        'revoked' => 'Dicabut',
                    ]),
            ])
            ->actions([
                Action::make('issue')
                    ->label('Terbitkan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (LetterOfAcceptance $r) => $r->status === 'draft')
                    ->requiresConfirmation()
                    ->modalHeading('Terbitkan LOA?')
                    ->modalDescription(fn (LetterOfAcceptance $r) => 'LOA ' . $r->loa_number . ' akan diubah ke status Diterbitkan.')
                    ->action(function (LetterOfAcceptance $record) {
                        $record->update(['status' => 'issued']);
                        Notification::make()->title('LOA berhasil diterbitkan.')->success()->send();
                    }),

                Action::make('revoke')
                    ->label('Cabut')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (LetterOfAcceptance $r) => $r->status === 'issued')
                    ->requiresConfirmation()
                    ->modalHeading('Cabut LOA?')
                    ->modalDescription('LOA yang dicabut tidak dapat diverifikasi oleh penulis.')
                    ->action(function (LetterOfAcceptance $record) {
                        $record->update(['status' => 'revoked']);
                        Notification::make()->title('LOA dicabut.')->warning()->send();
                    }),

                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (LetterOfAcceptance $r) => route('loa.preview', $r))
                    ->openUrlInNewTab(),

                EditAction::make()->label('Edit'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_issue')
                        ->label('Terbitkan yang Dipilih')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $count = $records->where('status', 'draft')->count();
                            $records->where('status', 'draft')
                                ->each(fn ($r) => $r->update(['status' => 'issued']));
                            Notification::make()
                                ->title("{$count} LOA berhasil diterbitkan.")
                                ->success()->send();
                        }),

                    BulkAction::make('bulk_revoke')
                        ->label('Cabut yang Dipilih')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalDescription('Tindakan ini akan mencabut semua LOA yang dipilih dan tidak dapat diverifikasi.')
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records) {
                            $count = $records->where('status', 'issued')->count();
                            $records->where('status', 'issued')
                                ->each(fn ($r) => $r->update(['status' => 'revoked']));
                            Notification::make()
                                ->title("{$count} LOA dicabut.")
                                ->warning()->send();
                        }),

                    DeleteBulkAction::make()->label('Hapus yang Dipilih'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->persistFiltersInSession()
            ->persistSearchInSession();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['journal', 'issuedBy', 'submission']);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLetterOfAcceptances::route('/'),
            'create' => Pages\CreateLetterOfAcceptance::route('/create'),
            'edit'   => Pages\EditLetterOfAcceptance::route('/{record}/edit'),
        ];
    }
}
