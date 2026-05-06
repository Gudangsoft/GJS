<?php

namespace App\Filament\Resources\Users\Tables;

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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->full_name).'&color=ffffff&background=3b82f6'),
                TextColumn::make('full_name')
                    ->label('Nama')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->description(fn ($record) => $record->affiliation),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('roles.name')
                    ->label('Peran')
                    ->badge()
                    ->colors([
                        'danger'  => 'super_admin',
                        'warning' => 'journal_manager',
                        'info'    => 'editor',
                        'success' => 'reviewer',
                        'gray'    => 'author',
                    ])
                    ->separator(', '),
                TextColumn::make('orcid')
                    ->label('ORCID')
                    ->searchable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_disabled')
                    ->label('Dinonaktifkan')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle'),
                TextColumn::make('email_verified_at')
                    ->label('Verifikasi Email')
                    ->date('d M Y')
                    ->placeholder('Belum Verifikasi')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_login_at')
                    ->label('Login Terakhir')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Belum pernah'),
                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('roles')
                    ->label('Peran')
                    ->relationship('roles', 'name')
                    ->preload(),
                TernaryFilter::make('is_disabled')
                    ->label('Status Akun')
                    ->trueLabel('Dinonaktifkan')
                    ->falseLabel('Aktif'),
                TernaryFilter::make('email_verified_at')
                    ->label('Verifikasi Email')
                    ->nullable()
                    ->trueLabel('Terverifikasi')
                    ->falseLabel('Belum Verifikasi'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('loginAs')
                    ->label('Login As')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => 'Login sebagai ' . $record->full_name . '?')
                    ->modalDescription('Anda akan masuk sebagai user ini. Klik "Kembali ke Admin" di banner untuk kembali.')
                    ->modalSubmitActionLabel('Ya, Login As')
                    ->visible(fn ($record) => $record->id !== Auth::id())
                    ->action(function ($record) {
                        session([
                            'impersonator_id'   => Auth::id(),
                            'impersonating_as'  => $record->id,
                        ]);
                        Auth::loginUsingId($record->id);
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
