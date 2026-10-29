<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiSalesAgentService
{
    /**
     * Respond to prospective buyer inquiries using injected product catalog (RAG-lite).
     */
    public function respond(Tenant $tenant, string $userMessage, array $conversationHistory = []): array
    {
        $setting = $tenant->storeSetting;
        $storeName = $setting?->store_name ?? $tenant->name;
        $phone = $tenant->phone_whatsapp;
        $currency = $setting?->currency_symbol ?? 'Rp';

        // Retrieve active catalog
        $products = Product::where('tenant_id', $tenant->id)
            ->where('status', 'published')
            ->limit(30)
            ->get();

        $catalogSummary = [];
        foreach ($products as $p) {
            $priceStr = $currency . ' ' . number_format($p->price, 0, ',', '.');
            $stockStr = $p->stock > 0 ? "Stok: {$p->stock}" : "Habis";
            $desc = $p->short_description ?? Str::limit(strip_tags($p->description), 100);
            $catalogSummary[] = "- {$p->name} | Harga: {$priceStr} | {$stockStr} | Info: {$desc}";
        }
        $catalogText = implode("\n", $catalogSummary);

        $systemPrompt = $setting?->llm_system_prompt ?? "Anda adalah AI Sales Consultant profesional dan ramah untuk toko {$storeName}.";
        $fullContextPrompt = <<<EOT
{$systemPrompt}

Profil Toko:
- Nama Toko: {$storeName}
- WhatsApp Hotline: {$phone}
- Mata Uang: {$currency}

Katalog Produk Saat Ini:
{$catalogText}

Panduan Anda:
1. Jawab pertanyaan pembeli dengan sopan, antusias, dan ramah.
2. Jelaskan spesifikasi dan keunggulan produk berdasarkan katalog di atas.
3. Jika calon pembeli menanyakan stok, harga, atau rekomendasi, rekomendasikan produk yang tepat.
4. Jangan memberikan informasi fiktif tentang produk di luar katalog.
5. Arahkan pembeli untuk menambahkan produk ke keranjang dan checkout langsung via WhatsApp.
EOT;

        // Try calling external LLM if OPENAI_API_KEY or GEMINI_API_KEY is configured
        $geminiKey = env('GEMINI_API_KEY');
        $openAiKey = env('OPENAI_API_KEY');

        if ($geminiKey) {
            $reply = $this->callGemini($geminiKey, $fullContextPrompt, $userMessage);
            if ($reply) {
                return ['reply' => $reply, 'source' => 'gemini'];
            }
        } elseif ($openAiKey) {
            $reply = $this->callOpenAi($openAiKey, $fullContextPrompt, $userMessage);
            if ($reply) {
                return ['reply' => $reply, 'source' => 'openai'];
            }
        }

        // High-quality contextual fallback agent
        $reply = $this->generateRuleBasedResponse($products, $storeName, $phone, $currency, $userMessage);

        return [
            'reply' => $reply,
            'source' => 'local_rag_agent',
        ];
    }

    protected function callGemini(string $apiKey, string $systemPrompt, string $userMessage): ?string
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}";
            $response = Http::timeout(10)->post($url, [
                'system_instruction' => [
                    'parts' => [['text' => $systemPrompt]],
                ],
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $userMessage]]],
                ],
            ]);

            if ($response->successful()) {
                return $response->json('candidates.0.content.parts.0.text');
            }
        } catch (\Throwable $e) {
            // Log fallback
        }
        return null;
    }

    protected function callOpenAi(string $apiKey, string $systemPrompt, string $userMessage): ?string
    {
        try {
            $response = Http::withToken($apiKey)->timeout(10)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }
        } catch (\Throwable $e) {
            // Log fallback
        }
        return null;
    }

    protected function generateRuleBasedResponse($products, string $storeName, string $phone, string $currency, string $query): string
    {
        $q = strtolower($query);

        // Check if query matches any product
        $matched = [];
        foreach ($products as $p) {
            $name = strtolower($p->name);
            if (str_contains($q, $name) || str_contains($name, $q) || similar_text($q, $name) > 4) {
                $matched[] = $p;
            }
        }

        if (!empty($matched)) {
            $target = $matched[0];
            $priceStr = $currency . ' ' . number_format($target->price, 0, ',', '.');
            $stockText = $target->stock > 0 ? "Stok tersedia ({$target->stock} unit)" : "Stok saat ini sedang kosong";
            return "Halo! Produk *{$target->name}* saat ini dibanderol dengan harga *{$priceStr}*. {$stockText}. Anda dapat langsung memasukkannya ke keranjang belanja atau melakukan checkout langsung via WhatsApp kami di {$phone}. Ada yang ingin ditanyakan lagi tentang produk ini?";
        }

        if (str_contains($q, 'ongkir') || str_contains($q, 'kirim') || str_contains($q, 'pengiriman')) {
            return "Untuk pengiriman dari {$storeName}, kami mendukung kurir reguler & instan. Estimasi dan konfirmasi ongkir akan kami bantu hitungkan langsung secara otomatis saat Anda checkout melalui WhatsApp ke nomor {$phone} ya!";
        }

        if (str_contains($q, 'bayar') || str_contains($q, 'pembayaran') || str_contains($q, 'rekening')) {
            return "Metode pembayaran dapat melalui Transfer Bank atau QRIS. Setelah Anda memilih produk di katalog dan menekan tombol Checkout WhatsApp, admin kami akan langsung memberikan nomor rekening resmi toko kami.";
        }

        if (str_contains($q, 'halo') || str_contains($q, 'hai') || str_contains($q, 'selamat') || str_contains($q, 'pagi') || str_contains($q, 'siang') || str_contains($q, 'malam')) {
            return "Halo! Selamat datang di {$storeName}. Saya asisten AI resmi toko. Silakan tanyakan seputar katalog produk, ketersediaan stok, atau harga. Kami siap membantu Anda!";
        }

        $sampleList = $products->take(3)->pluck('name')->implode(', ');
        return "Terima kasih telah menghubungi {$storeName}! Kami menyediakan berbagai pilihan produk unggulan seperti {$sampleList}. Silakan tanyakan produk yang Anda cari atau langsung klik produk di katalog untuk checkout via WhatsApp ke {$phone}.";
    }
}
