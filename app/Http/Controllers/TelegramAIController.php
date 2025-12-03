<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api;
use App\Models\Barang;

class TelegramAIController extends Controller
{
    public function handle(Request $request)
    {
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $text = $request->input('message.text');
        $chat_id = $request->input('message.chat.id');

        if (!$text) {
            return response('OK', 200);
        }

        $textLower = strtolower($text);

        // ======================================================
        // 🔍 1. CARI BERDASARKAN KODE BARANG (cek xxx)
        // ======================================================
        if (preg_match('/^cek\s+([a-zA-Z0-9]+)/', $textLower, $m)) {

            $kode = $m[1];
            $barang = Barang::where('kode_barang', 'LIKE', "%$kode%")->first();

            if ($barang) {
                return $this->reply($telegram, $chat_id, $this->formatBarang($barang));
            }

            return $this->reply($telegram, $chat_id, "❌ Barang dengan kode *$kode* tidak ditemukan.");
        }

        // ======================================================
        // 🔍 2. CARI BERDASARKAN NAMA BARANG (cari laptop)
        // ======================================================
        if (preg_match('/^(cari|lihat|info|dimana)\s+(.+)/', $textLower, $m)) {

            $nama = $m[2];
            $barang = Barang::where('nama_barang', 'LIKE', "%$nama%")->first();

            if ($barang) {
                return $this->reply($telegram, $chat_id, $this->formatBarang($barang));
            }
        }

        // ======================================================
        // 🔍 3. CARI BERDASARKAN QR (qr xxx)
        // ======================================================
        if (preg_match('/^qr\s*([a-zA-Z0-9]+)/', $textLower, $m)) {

            $qr = $m[1];
            $barang = Barang::where('qr_string', 'LIKE', "%$qr%")->first();

            if ($barang) {
                return $this->reply($telegram, $chat_id, $this->formatBarang($barang));
            }
        }

        // ======================================================
        // 🔍 4. CARI SN (sn xxx)
        // ======================================================
        if (preg_match('/^sn\s*([a-zA-Z0-9]+)/', $textLower, $m)) {

            $sn = $m[1];
            $barang = Barang::where('sn', 'LIKE', "%$sn%")->first();

            if ($barang) {
                return $this->reply($telegram, $chat_id, $this->formatBarang($barang));
            }
        }

        // ======================================================
        // 🟦 DEFAULT HELP
        // ======================================================
        $default = "Halo! Saya *BroSigap Bot* 🤖📦\n"
            . "Saya membantu mencari data aset TVRI.\n\n"
            . "Perintah yang bisa digunakan:\n"
            . "• `cek KODEBARANG`\n"
            . "• `cari NAMA BARANG`\n"
            . "• `qr KODEQR`\n"
            . "• `sn NOMOR_SERI`\n\n"
            . "Contoh:\n"
            . "`cek 21313121`\n"
            . "`cari kamera`\n"
            . "`qr 45395u394`\n"
            . "`sn 293249u92`";

        return $this->reply($telegram, $chat_id, $default);
    }

    // ======================================================
    // 📨 Mengirim pesan ke user
    // ======================================================
    private function reply($telegram, $chat_id, $text)
    {
        return $telegram->sendMessage([
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'Markdown'
        ]);
    }

    // ======================================================
    // 📦 FORMAT INFORMASI BARANG
    // ======================================================
    private function formatBarang($b)
{
    $lokasi = $b->location ? $b->location->nama_lokasi : '-';

    return 
"📦 *DATA BARANG*
—————————————
*Nama:* $b->nama_barang
*Kode Barang:* $b->kode_barang
*Register:* $b->kode_register
*Kode Sakter:* $b->kode_sakter
*Special Code:* $b->special_code
*NUP:* $b->nup
*Merek:* $b->merek
*Lokasi:* $lokasi
*Kondisi:* $b->kondisi
*Nilai Perolehan:* Rp " . number_format($b->nilai_perolehan, 0, ',', '.') . "
*QR:* $b->qr_string
*SN:* $b->sn
—————————————
Diperbarui: $b->updated_at
";
}
}
