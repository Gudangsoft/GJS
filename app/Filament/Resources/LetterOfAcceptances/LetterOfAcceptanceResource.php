<?php

namespace App\Filament\Resources\LetterOfAcceptances;

use App\Models\LetterOfAcceptance;
use App\Models\Journal;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Str;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class LetterOfAcceptanceResource extends Resource
{
    protected static ?string $model = LetterOfAcceptance::class;
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-document-check';
    protected static ?string $navigationLabel = 'Letter of Acceptance';
    protected static string|\UnitEnum|null $navigationGroup = 'Editorial';
    protected static ?string $pluralLabel     = 'Letter of Acceptance';
    protected static ?string $modelLabel      = 'LOA';
    protected static ?int    $navigationSort  = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Informasi Submission')->schema([
                Grid::make(2)->schema([
                    Select::make('journal_id')
                        ->label('Jurnal')
                        ->options(Journal::orderBy('name')->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(fn ($set) => $set('submission_id', null)),

                    Select::make('submission_id')
                        ->label('Submission (Naskah Diterima)')
                        ->required()
                        ->searchable()
                        ->options(function ($get) {
                            $jid = $get('journal_id');
                            return Submission::where('journal_id', $jid)
                                ->whereIn('status', ['accepted','copyediting','production','scheduled','published'])
                                ->get()
                                ->mapWithKeys(fn ($s) => [$s->id => '#'.$s->id.' — '.Str::limit($s->title, 60)]);
                        })
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set) {
                            if (!$state) return;
                            $sub = Submission::with('submitter')->find($state);
                            if ($sub) {
                                $set('article_title', $sub->title);
                                $set('authors', collect([$sub->submitter])->filter()->map(fn($u) => $u->first_name.' '.$u->last_name)->implode(', '));
                            }
                        }),
                ]),
            ]),

            Section::make('Detail LOA')->schema([
                Grid::make(2)->schema([
                    TextInput::make('loa_number')
                        ->label('Nomor LOA')
                        ->placeholder('LOA/JIKI/2026/001')
                        ->required(),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'draft'   => 'Draft',
                            'issued'  => 'Diterbitkan',
                            'revoked' => 'Dicabut',
                        ])
                        ->default('draft')
                        ->required(),

                    TextInput::make('article_title')
                        ->label('Judul Artikel')
                        ->required()
                        ->columnSpan(2),

                    TextInput::make('authors')
                        ->label('Nama Penulis')
                        ->helperText('Pisahkan dengan koma jika lebih dari satu')
                        ->columnSpan(2),

                    DatePicker::make('acceptance_date')
                        ->label('Tanggal Penerimaan')
                        ->default(now())
                        ->required(),

                    DatePicker::make('expected_publication_date')
                        ->label('Estimasi Terbit'),

                    TextInput::make('volume')->label('Volume'),
                    TextInput::make('number')->label('Nomor'),
                    TextInput::make('year')->label('Tahun')->default(now()->year),
                ]),

                Textarea::make('notes')
                    ->label('Catatan Tambahan')
                    ->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loa_number')
                    ->label('Nomor LOA')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('journal.name_abbrev')
                    ->label('Jurnal')
                    ->badge()
                    ->color('info'),

                TextColumn::make('article_title')
                    ->label('Judul Artikel')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('authors')
                    ->label('Penulis')
                    ->limit(30)
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state),

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
                    ->label('Tgl Terima')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('issuedBy.name')
                    ->label('Diterbitkan Oleh')
                    ->default('—'),
            ])
            ->filters([
                SelectFilter::make('journal_id')
                    ->label('Jurnal')
                    ->options(Journal::orderBy('name')->pluck('name_abbrev', 'id')),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft'   => 'Draft',
                        'issued'  => 'Diterbitkan',
                        'revoked' => 'Dicabut',
                    ]),
            ])
            ->actions([
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (LetterOfAcceptance $record) => route('loa.preview', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
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
