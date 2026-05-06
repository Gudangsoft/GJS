<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    Select::make('journal_id')
                        ->label('Jurnal')
                        ->relationship('journal', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Select::make('user_id')
                        ->label('Dibuat Oleh')
                        ->relationship('author', 'email')
                        ->searchable()
                        ->nullable()
                        ->placeholder('— Admin —'),
                    TextInput::make('title')
                        ->label('Judul Pengumuman')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2),
                ]),

                Textarea::make('description_short')
                    ->label('Ringkasan Singkat')
                    ->rows(2)
                    ->maxLength(500)
                    ->helperText('Tampil di daftar pengumuman')
                    ->columnSpanFull(),

                RichEditor::make('description')
                    ->label('Isi Pengumuman')
                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList', 'link', 'h2', 'h3'])
                    ->columnSpanFull(),

                Grid::make(2)->schema([
                    DateTimePicker::make('date_posted')
                        ->label('Tanggal Posting')
                        ->default(now())
                        ->nullable(),
                    DateTimePicker::make('date_expire')
                        ->label('Tanggal Kadaluarsa')
                        ->nullable()
                        ->helperText('Kosongkan jika tidak ada batas waktu'),
                    Toggle::make('send_email')
                        ->label('Kirim Notifikasi Email ke Pembaca')
                        ->inline(false)
                        ->helperText('Email akan dikirim saat disimpan'),
                ]),
            ]);
    }
}
