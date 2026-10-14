<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Order;
use App\Services\PayTRService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayTRController extends Controller
{
    protected PayTRService $payTR;

    public function __construct(PayTRService $payTR)
    {
        $this->payTR = $payTR;
    }

    /**
     * PayTR Webhook Bildirim Endpoint'i (Sıfır Güvenlik Açığı / HMAC Doğrulamalı)
     */
    public function callback(Request $request)
    {
        $merchantOid = $request->input('merchant_oid');
        $status = $request->input('status');
        $totalAmount = $request->input('total_amount');
        $receivedHash = $request->input('hash');

        if (! $merchantOid || ! $status || ! $totalAmount || ! $receivedHash) {
            Log::warning('PayTR Callback Eksik Parametre', $request->all());

            return response('PAYTR notification failed: missing parameters', 400);
        }

        // 1. HMAC-SHA256 Güvenlik İmzası Doğrulama
        if (! $this->payTR->verifyCallbackHash($merchantOid, $status, $totalAmount, $receivedHash)) {
            Log::alert('PayTR Callback Sahte/Geçersiz İmza (Olası Saldırı Engellendi)!', [
                'merchant_oid' => $merchantOid,
                'ip' => $request->ip(),
                'data' => $request->all(),
            ]);

            return response('PAYTR notification failed: bad hash', 400);
        }

        // 2. Siparişi Bul
        $order = Order::where('order_number', $merchantOid)->first();
        if (! $order) {
            Log::error('PayTR Callback Sipariş Bulunamadı: '.$merchantOid);

            return response('PAYTR notification failed: order not found', 404);
        }

        // 3. Tutar Doğrulaması (Kuruş Kuruşuna Eşleşme Kontrolü)
        $expectedAmountInCents = (int) round($order->total_amount * 100);
        if ((int) $totalAmount !== $expectedAmountInCents) {
            Log::alert("PayTR Callback Tutar Uyuşmazlığı! Beklenen: {$expectedAmountInCents}, Gelen: {$totalAmount}", [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);

            return response('PAYTR notification failed: amount mismatch', 400);
        }

        // 4. Ödeme Sonucunu İşle
        if ($status === 'success') {
            $installment = $request->input('installment_count', 1);
            $paymentType = $request->input('payment_type', 'Kredi Kartı');
            $currency = $request->input('currency', 'TL');

            $paymentNote = "💳 PayTR Online Ödeme Başarılı ({$order->total_amount} {$currency} - {$paymentType} - {$installment} Taksit)";

            $order->update([
                'status' => 'Ödeme Alındı (Onaylandı)',
                'notes' => trim($order->notes ? $order->notes.' | '.$paymentNote : $paymentNote),
            ]);

            ActivityLog::create([
                'user_id' => null,
                'action' => 'PayTR Ödemesi Onaylandı',
                'model_type' => 'Order',
                'model_id' => $order->id,
                'details' => "Sipariş #{$order->order_number} için PayTR üzerinden {$order->total_amount} TL tahsil edildi.",
                'ip_address' => $request->ip(),
            ]);

            Log::info("PayTR Sipariş Ödemesi Başarıyla Alındı: #{$order->order_number}");
        } else {
            $failedReason = $request->input('failed_reason_msg', 'Kart reddedildi veya limit yetersiz');
            $order->update([
                'status' => 'Ödeme Başarısız',
                'notes' => trim($order->notes ? $order->notes.' | ✕ PayTR Hata: '.$failedReason : '✕ PayTR Hata: '.$failedReason),
            ]);

            Log::warning("PayTR Ödeme Başarısız: #{$order->order_number} - Neden: {$failedReason}");
        }

        // PayTR standart yanıt olarak "OK" bekler
        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    /**
     * PayTR Ödeme Başarısız / İptal Sayfası
     */
    public function fail(Order $order)
    {
        return view('cart.paytr-fail', compact('order'));
    }
}
