<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected ?string $token;
    protected ?string $chatId;

    public function __construct()
    {
        $this->token  = config('services.telegram.token');
        $this->chatId = config('services.telegram.chat_id');
    }

    public function sendMessage(string $text, array $options = []): bool
    {
        if (!$this->token || !$this->chatId) {
            Log::warning('Telegram config missing token or chat_id');
            return false;
        }

        $verifySsl = config('services.telegram.verify_ssl', true);

        $payload = array_merge([
            'chat_id'                  => $this->chatId,
            'text'                     => $text,
            'parse_mode'               => 'HTML',
            'disable_web_page_preview' => true,
        ], $options);

        try {
            $response = Http::timeout(10)
                ->withOptions(['verify' => $verifySsl])
                ->post(
                "https://api.telegram.org/bot{$this->token}/sendMessage",
                $payload
            );

            if (!$response->successful()) {
                Log::error('Telegram send failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Telegram send exception', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
