<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Support\Str;

class WhatsAppCheckoutService
{
    /**
     * Process checkout submission, persist order, and generate WhatsApp direct link.
     */
    public function processCheckout(Tenant $tenant, array $data): array
    {
        $items = $data['items'] ?? [];
        if (empty($items)) {
            throw new \InvalidArgumentException('Keranjang belanja kosong.');
        }

        $currencySymbol = $tenant->storeSetting?->currency_symbol ?? 'Rp';
        $orderNumber = 'ORD-' . strtoupper(Str::random(4)) . '-' . date('ymd');

        $totalAmount = 0;
        $orderItemsData = [];

        foreach ($items as $item) {
            $product = Product::where('tenant_id', $tenant->id)->find($item['product_id']);
            $productName = $product ? $product->name : ($item['name'] ?? 'Produk');
            $price = $product ? (float) $product->price : (float) ($item['price'] ?? 0);
            $qty = max(1, (int) ($item['quantity'] ?? 1));
            $subtotal = $price * $qty;
            $totalAmount += $subtotal;

            $orderItemsData[] = [
                'product_id' => $product?->id,
                'product_name' => $productName,
                'sku' => $product?->sku,
                'price' => $price,
                'quantity' => $qty,
                'subtotal' => $subtotal,
            ];
        }

        // 1. Create Order in database
        $order = Order::create([
            'tenant_id' => $tenant->id,
            'order_number' => $orderNumber,
            'customer_name' => $data['customer_name'] ?? 'Pelanggan',
            'customer_phone' => $data['customer_phone'] ?? '-',
            'customer_email' => $data['customer_email'] ?? null,
            'customer_address' => $data['customer_address'] ?? null,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'whatsapp_checkout',
            'notes' => $data['notes'] ?? null,
            'metadata' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'items_count' => count($orderItemsData),
            ],
        ]);

        // 2. Create Order Items
        foreach ($orderItemsData as $itemData) {
            $order->items()->create($itemData);
        }

        // 3. Format WhatsApp Message
        $waMessage = $this->formatWhatsAppMessage($tenant, $order, $orderItemsData, $currencySymbol);

        // 4. Sanitize Phone number
        $cleanPhone = preg_replace('/[^0-9]/', '', $tenant->phone_whatsapp);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $waUrl = "https://wa.me/{$cleanPhone}?text=" . rawurlencode($waMessage);

        return [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total_amount' => $totalAmount,
            'whatsapp_url' => $waUrl,
            'message' => $waMessage,
        ];
    }

    /**
     * Format professional and clear WhatsApp order template.
     */
    protected function formatWhatsAppMessage(Tenant $tenant, Order $order, array $items, string $currencySymbol): string
    {
        $storeName = $tenant->storeSetting?->store_name ?? $tenant->name;
        $lines = [];

        $lines[] = "Halo *{$storeName}*, saya ingin konfirmasi pesanan baru:";
        $lines[] = "━━━━━━━━━━━━━━━━━━━━";
        $lines[] = "🔖 *No. Pesanan:* {$order->order_number}";
        $lines[] = "👤 *Nama Pembeli:* {$order->customer_name}";
        $lines[] = "📱 *No. Kontak:* {$order->customer_phone}";
        if ($order->customer_address) {
            $lines[] = "📍 *Alamat Kirim:* {$order->customer_address}";
        }
        $lines[] = "━━━━━━━━━━━━━━━━━━━━";
        $lines[] = "🛒 *DETAIL PRODUK:*";

        foreach ($items as $idx => $item) {
            $no = $idx + 1;
            $formattedPrice = $currencySymbol . ' ' . number_format($item['price'], 0, ',', '.');
            $formattedSubtotal = $currencySymbol . ' ' . number_format($item['subtotal'], 0, ',', '.');
            $lines[] = "{$no}. *{$item['product_name']}*";
            $lines[] = "   {$item['quantity']}x @ {$formattedPrice} = *{$formattedSubtotal}*";
        }

        $totalFormatted = $currencySymbol . ' ' . number_format($order->total_amount, 0, ',', '.');
        $lines[] = "━━━━━━━━━━━━━━━━━━━━";
        $lines[] = "💰 *TOTAL PEMBAYARAN:* *{$totalFormatted}*";
        
        if ($order->notes) {
            $lines[] = "📝 *Catatan:* {$order->notes}";
        }

        $lines[] = "━━━━━━━━━━━━━━━━━━━━";
        $lines[] = "Mohon informasikan ketersediaan stok & nomor rekening untuk pembayaran. Terima kasih!";

        return implode("\n", $lines);
    }
}
