<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()->tabs([

                    Tab::make('Profil')->schema([
                        Grid::make(3)->schema([
                            FileUpload::make('avatar')
                                ->label('Foto Profil')
                                ->image()
                                ->imageResizeMode('cover')
                                ->imageCropAspectRatio('1:1')
                                ->imageResizeTargetWidth('200')
                                ->imageResizeTargetHeight('200')
                                ->directory('avatars')
                                ->visibility('public')
                                ->columnSpan(1),
                            Grid::make(2)->schema([
                                Select::make('salutation')
                                    ->label('Sapaan')
                                    ->options([
                                        'Dr.'   => 'Dr.',
                                        'Prof.' => 'Prof.',
                                        'Mr.'   => 'Mr.',
                                        'Mrs.'  => 'Mrs.',
                                        'Ms.'   => 'Ms.',
                                    ])
                                    ->placeholder('—'),
                                TextInput::make('first_name')
                                    ->label('Nama Depan')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('last_name')
                                    ->label('Nama Belakang')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                TextInput::make('orcid')
                                    ->label('ORCID iD')
                                    ->placeholder('0000-0000-0000-0000')
                                    ->maxLength(19),
                                TextInput::make('phone')
                                    ->label('Telepon')
                                    ->tel()
                                    ->maxLength(20),
                            ])->columnSpan(2),
                        ]),

                        Section::make('Institusi & Lokasi')->schema([
                            Grid::make(2)->schema([
                                TextInput::make('affiliation')
                                    ->label('Institusi / Afiliasi')
                                    ->maxLength(255),
                                Select::make('country')
                                    ->label('Negara')
                                    ->searchable()
                                    ->options([
                                        'ID' => 'Indonesia', 'MY' => 'Malaysia', 'SG' => 'Singapura',
                                        'US' => 'Amerika Serikat', 'GB' => 'Inggris', 'AU' => 'Australia',
                                        'SA' => 'Arab Saudi', 'JP' => 'Jepang', 'CN' => 'China', 'IN' => 'India',
                                    ]),
                                TextInput::make('url')
                                    ->label('Website / Profil')
                                    ->url()
                                    ->maxLength(255),
                                Select::make('locale')
                                    ->label('Bahasa Antarmuka')
                                    ->options(['id' => 'Bahasa Indonesia', 'en' => 'English'])
                                    ->default('id')
                                    ->required(),
                            ]),
                            Textarea::make('bio')
                                ->label('Biografi Singkat')
                                ->rows(3)
                                ->maxLength(1000)
                                ->columnSpanFull(),
                        ])->collapsible(),
                    ]),

                    Tab::make('Keamanan & Akses')->schema([
                        Grid::make(2)->schema([
                            TextInput::make('password')
                                ->label('Password Baru')
                                ->password()
                                ->revealable()
                                ->minLength(8)
                                ->dehydrated(fn ($state) => filled($state))
                                ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                ->nullable()
                                ->helperText('Kosongkan jika tidak ingin mengubah password'),
                            TextInput::make('password_confirmation')
                                ->label('Konfirmasi Password')
                                ->password()
                                ->revealable()
                                ->same('password')
                                ->dehydrated(false)
                                ->nullable(),
                        ]),

                        Section::make('Status Akun')->schema([
                            Grid::make(2)->schema([
                                Toggle::make('is_disabled')
                                    ->label('Nonaktifkan Akun')
                                    ->helperText('Jika diaktifkan, pengguna tidak bisa login')
                                    ->inline(false),
                                Toggle::make('email_verified_at')
                                    ->label('Email Terverifikasi')
                                    ->onIcon('heroicon-o-check-circle')
                                    ->offIcon('heroicon-o-x-circle')
                                    ->inline(false)
                                    ->dehydrateStateUsing(fn ($state) => $state ? now() : null)
                                    ->formatStateUsing(fn ($state) => filled($state)),
                            ]),
                        ]),
                    ]),

                    Tab::make('Peran & Izin')->schema([
                        Select::make('roles')
                            ->label('Peran (Roles)')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                    ]),

                ])->columnSpanFull(),
            ]);
    }
}
