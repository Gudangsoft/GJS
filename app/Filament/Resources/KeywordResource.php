<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KeywordResource\Pages;
use App\Models\Keyword;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class KeywordResource extends Resource
{
    protected static ?string               $model           = Keyword::class;
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-tag';
    protected static ?string               $navigationLabel = 'Kata Kunci';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?int                  $navigationSort  = 9;
    protected static ?string               $modelLabel      = 'Kata Kunci';
    protected static ?string               $pluralModelLabel= 'Kata Kunci';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                TextInput::make('keyword')
                    ->label('Kata Kunci')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                \Filament\Forms\Components\Select::make('locale')
                    ->label('Bahasa')
                    ->options(['id' => 'Indonesia', 'en' => 'English'])
                    ->default('id')
                    ->required(),

                TextInput::make('discipline')
                    ->label('Disiplin Ilmu')
                    ->placeholder('mis. Teknologi Informasi, Hukum, Kesehatan...')
                    ->maxLength(255),

                TextInput::make('usage_count')
                    ->label('Jumlah Penggunaan')
                    ->numeric()
                    ->default(0)
                    ->disabled(),

                Toggle::make('is_approved')
                    ->label('Disetujui')
                    ->default(true)
                    ->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('keyword')
                    ->label('Kata Kunci')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('discipline')
                    ->label('Disiplin')
                    ->badge()
                    ->color('info')
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('locale')
                    ->label('Bahasa')
                    ->badge()
                    ->formatStateUsing(fn($s) => $s === 'id' ? 'Indonesia' : 'English')
                    ->color(fn($s) => $s === 'id' ? 'warning' : 'primary'),

                TextColumn::make('usage_count')
                    ->label('Penggunaan')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                IconColumn::make('is_approved')
                    ->label('Disetujui')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('usage_count', 'desc')
            ->filters([
                SelectFilter::make('locale')
                    ->label('Bahasa')
                    ->options(['id' => 'Indonesia', 'en' => 'English']),

                TernaryFilter::make('is_approved')
                    ->label('Status Persetujuan'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('approve')
                        ->label('Setujui Semua')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn($records) => $records->each->update(['is_approved' => true])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKeywords::route('/'),
            'edit'  => Pages\EditKeyword::route('/{record}/edit'),
        ];
    }
}
