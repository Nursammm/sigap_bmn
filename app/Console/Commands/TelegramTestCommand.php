<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;

class TelegramTestCommand extends Command
{
    protected $signature = 'telegram:test {message? : Pesan yang dikirim}';

    protected $description = 'Kirim pesan percobaan ke Telegram untuk memastikan konfigurasi benar';

    public function handle(TelegramService $telegram): int
    {
        $text = $this->argument('message') ?? 'Tes notifikasi Telegram dari SIGAP-BMN';

        $ok = $telegram->sendMessage($text);

        if ($ok) {
            $this->info('Pesan terkirim ke Telegram.');
            return Command::SUCCESS;
        }

        $this->error('Pesan gagal terkirim. Cek log untuk detailnya.');
        return Command::FAILURE;
    }
}
