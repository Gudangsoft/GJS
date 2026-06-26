<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Mail;

class EmailSettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-envelope';
    protected static ?string               $navigationLabel = 'Pengaturan Email';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?int                  $navigationSort  = 11;
    protected static ?string               $title           = 'Pengaturan Email & SMTP';

    public ?array $data = [];

    public function mount(): void
    {
        $stored = Setting::getGroup('email');

        $this->form->fill([
            'driver'       => $stored['driver']       ?? 'smtp',
            'host'         => $stored['host']         ?? config('mail.mailers.smtp.host', ''),
            'port'         => $stored['port']         ?? config('mail.mailers.smtp.port', '587'),
            'encryption'   => $stored['encryption']   ?? config('mail.mailers.smtp.encryption', 'tls'),
            'username'     => $stored['username']     ?? config('mail.mailers.smtp.username', ''),
            'password'     => '',
            'from_address' => $stored['from_address'] ?? config('mail.from.address', ''),
            'from_name'    => $stored['from_name']    ?? config('mail.from.name', config('app.name')),
            'reply_to'     => $stored['reply_to']     ?? '',
            'ssl_verify'   => ($stored['ssl_verify']  ?? '1') === '1',
            'timeout'      => $stored['timeout']      ?? '30',
        ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('Simpan Konfigurasi')
                            ->icon('heroicon-o-check')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ]),
                ]),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                Section::make('Mail Driver')
                    ->icon('heroicon-o-server')
                    ->columns(2)
                    ->schema([
                        Select::make('driver')
                            ->label('Driver Mail')
                            ->options([
                                'smtp'    => 'SMTP',
                                'mailgun' => 'Mailgun',
                                'ses'     => 'Amazon SES',
                                'log'     => 'Log (Development)',
                                'array'   => 'Array (Testing)',
                            ])
                            ->default('smtp')
                            ->required()
                            ->native(false),
                    ]),

                Section::make('Konfigurasi SMTP')
                    ->description('Pengaturan server SMTP untuk pengiriman email.')
                    ->icon('heroicon-o-wifi')
                    ->columns(2)
                    ->schema([
                        TextInput::make('host')
                            ->label('SMTP Host')
                            ->required()
                            ->placeholder('smtp.gmail.com'),

                        TextInput::make('port')
                            ->label('SMTP Port')
                            ->required()
                            ->numeric()
                            ->placeholder('587'),

                        Select::make('encryption')
                            ->label('Enkripsi')
                            ->options([
                                'tls' => 'TLS (port 587)',
                                'ssl' => 'SSL (port 465)',
                                ''    => 'None',
                            ])
                            ->default('tls')
                            ->native(false),

                        Toggle::make('ssl_verify')
                            ->label('Verifikasi SSL Certificate')
                            ->helperText('Nonaktifkan untuk self-signed certificate')
                            ->default(true)
                            ->inline(false),

                        TextInput::make('username')
                            ->label('Username / Email SMTP')
                            ->placeholder('user@gmail.com')
                            ->columnSpanFull(),

                        TextInput::make('password')
                            ->label('Password SMTP / App Password')
                            ->password()
                            ->revealable()
                            ->placeholder('Kosongkan jika tidak berubah')
                            ->helperText('Gmail: gunakan App Password, bukan password akun')
                            ->columnSpanFull(),

                        TextInput::make('timeout')
                            ->label('Timeout (detik)')
                            ->numeric()
                            ->default(30)
                            ->minValue(5)
                            ->maxValue(120),
                    ]),

                Section::make('Identitas Pengirim')
                    ->description('Nama dan alamat email yang dilihat oleh penerima.')
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->schema([
                        TextInput::make('from_address')
                            ->label('From — Alamat Email')
                            ->email()
                            ->required()
                            ->placeholder('noreply@gjs.ac.id'),

                        TextInput::make('from_name')
                            ->label('From — Nama Pengirim')
                            ->required()
                            ->placeholder('GJS — Go Journal System'),

                        TextInput::make('reply_to')
                            ->label('Reply-To (opsional)')
                            ->email()
                            ->placeholder('support@gjs.ac.id')
                            ->helperText('Jika kosong, reply diarahkan ke From address'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testEmail')
                ->label('Kirim Test Email')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->form([
                    TextInput::make('to')
                        ->label('Kirim Ke')
                        ->email()
                        ->required()
                        ->default(fn () => auth()->user()?->email ?? ''),
                ])
                ->action(function (array $data) {
                    $this->sendTestEmail($data['to']);
                }),

            Action::make('save')
                ->label('Simpan')
                ->icon('heroicon-o-check')
                ->action('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
        } catch (Halt) {
            return;
        }

        // Jangan simpan password jika kosong (tidak berubah)
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['ssl_verify'] = $data['ssl_verify'] ? '1' : '0';

        Setting::setGroup('email', $data);
        $this->applyToConfig(Setting::getGroup('email'));

        Notification::make()
            ->title('Pengaturan email berhasil disimpan')
            ->success()
            ->send();
    }

    private function applyToConfig(array $cfg): void
    {
        $mailer = $cfg['driver'] ?? 'smtp';
        config([
            'mail.default'                            => $mailer,
            "mail.mailers.{$mailer}.host"             => $cfg['host']       ?? '',
            "mail.mailers.{$mailer}.port"             => (int) ($cfg['port'] ?? 587),
            "mail.mailers.{$mailer}.encryption"       => $cfg['encryption'] ?? 'tls',
            "mail.mailers.{$mailer}.username"         => $cfg['username']   ?? '',
            "mail.mailers.{$mailer}.password"         => $cfg['password']   ?? config("mail.mailers.{$mailer}.password"),
            'mail.from.address'                       => $cfg['from_address'] ?? '',
            'mail.from.name'                          => $cfg['from_name']    ?? '',
        ]);
    }

    private function sendTestEmail(string $to): void
    {
        try {
            $saved = Setting::getGroup('email');
            $this->applyToConfig($saved);

            Mail::raw(
                "Ini adalah email pengujian dari GJS — Go Journal System.\n\n"
                . "Jika Anda menerima email ini, konfigurasi SMTP sudah benar.\n\n"
                . "Server: " . ($saved['host'] ?? '?') . ':' . ($saved['port'] ?? '?') . "\n"
                . "Enkripsi: " . ($saved['encryption'] ?: 'none') . "\n"
                . "Dikirim pada: " . now()->format('d M Y H:i:s'),
                fn ($m) => $m
                    ->to($to)
                    ->subject('[GJS] Test Email — ' . now()->format('d M Y H:i'))
                    ->from(
                        $saved['from_address'] ?? config('mail.from.address'),
                        $saved['from_name']    ?? config('mail.from.name')
                    )
            );

            Notification::make()
                ->title('Test email berhasil dikirim ke ' . $to)
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Gagal mengirim test email')
                ->body($e->getMessage())
                ->danger()
                ->duration(10000)
                ->send();
        }
    }
}
