<?php

namespace App\Filament\Resources\Journals\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class JournalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->size(44)
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=J&background=3b82f6&color=fff&size=44')
                    ->extraImgAttributes(['class' => 'object-contain']),

                TextColumn::make('name')
                    ->label('Nama Jurnal')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->name_abbrev),

                // Kolom pengelola (journal_manager)
                TextColumn::make('managers_display')
                    ->label('Journal Manager')
                    ->state(function ($record) {
                        if ($record->managers->isEmpty()) return null;
                        return $record->managers
                            ->map(fn ($u) => $u->first_name . ' ' . $u->last_name)
                            ->join(', ');
                    })
                    ->description(function ($record) {
                        if ($record->managers->isEmpty()) return null;
                        return $record->managers->map(fn ($u) => $u->email)->join(', ');
                    })
                    ->placeholder('—')
                    ->searchable(false),

                // Kolom editor per jurnal
                TextColumn::make('editors_display')
                    ->label('Editor')
                    ->state(function ($record) {
                        if ($record->editors->isEmpty()) return null;
                        return $record->editors
                            ->map(fn ($u) => $u->first_name . ' ' . $u->last_name)
                            ->join(', ');
                    })
                    ->description(function ($record) {
                        if ($record->editors->isEmpty()) return null;
                        return $record->editors->map(fn ($u) => $u->email)->join(', ');
                    })
                    ->placeholder('—')
                    ->searchable(false),

                TextColumn::make('issn_print')
                    ->label('ISSN Cetak')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('issn_online')
                    ->label('e-ISSN')
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('publisher')
                    ->label('Penerbit')
                    ->searchable()
                    ->toggleable()
                    ->limit(30),

                TextColumn::make('review_mode')
                    ->label('Mode Review')
                    ->badge()
                    ->colors([
                        'gray'    => 'single_blind',
                        'blue'    => 'double_blind',
                        'purple'  => 'triple_blind',
                        'success' => 'open',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'single_blind' => 'Single Blind',
                        'double_blind' => 'Double Blind',
                        'triple_blind' => 'Triple Blind',
                        'open'         => 'Open',
                        default        => $state,
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger'  => 'archived',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'active'   => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'archived' => 'Diarsipkan',
                        default    => $state,
                    }),

                IconColumn::make('enabled')
                    ->label('Publik')
                    ->boolean(),

                TextColumn::make('sinta_level')
                    ->label('SINTA')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'S1' => 'success',
                        'S2' => 'info',
                        'S3', 'S4' => 'warning',
                        default => 'gray',
                    })
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('loa_signer_name')
                    ->label('Penandatangan LOA')
                    ->placeholder('⚠ Belum diisi')
                    ->description(fn ($record) => $record->loa_signer_title)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active'   => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'archived' => 'Diarsipkan',
                    ]),
                SelectFilter::make('review_mode')
                    ->label('Mode Review')
                    ->options([
                        'single_blind' => 'Single Blind',
                        'double_blind' => 'Double Blind',
                        'triple_blind' => 'Triple Blind',
                        'open'         => 'Open',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                // Login As Pengelola
                Action::make('loginAsManager')
                    ->label('Login As')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->modalHeading(fn ($record) => 'Login As — ' . $record->name)
                    ->modalDescription('Pilih pengelola atau editor jurnal ini.')
                    ->form(fn ($record) => [
                        \Filament\Forms\Components\Select::make('user_id')
                            ->label('Pilih User')
                            ->options(function () use ($record) {
                                $options = [];
                                foreach ($record->managers as $u) {
                                    $options[$u->id] = '[Manager] ' . $u->first_name . ' ' . $u->last_name . ' — ' . $u->email;
                                }
                                foreach ($record->editors as $u) {
                                    $options[$u->id] = '[Editor] ' . $u->first_name . ' ' . $u->last_name . ' — ' . $u->email;
                                }
                                return $options;
                            })
                            ->required()
                            ->placeholder('— pilih user —'),
                    ])
                    ->modalSubmitActionLabel('Login As')
                    ->visible(fn ($record) => $record->managers->isNotEmpty() || $record->editors->isNotEmpty())
                    ->action(function ($record, array $data) {
                        session([
                            'impersonator_id'  => Auth::id(),
                            'impersonating_as' => $data['user_id'],
                        ]);
                        Auth::loginUsingId($data['user_id']);
                        return redirect('/dashboard');
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
