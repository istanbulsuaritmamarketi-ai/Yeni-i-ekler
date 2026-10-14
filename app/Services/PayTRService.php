<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayTRService
{
    protected array $settings;

    protected string $merchantId;

    protected string $merchantKey;

    protected string $merchantSalt;

    protected bool $isTest;

    public function __construct()
    {
        $dbSettings = Setting::all()->pluck('value', 'key')->toArray();
        $this->settings = $dbSettings;

        $this->merchantId = $dbSettings['paytr_merchant_id'] ?? env('PAYTR_MERCHANT_ID', '');
        $this->merchantKey = $dbSettings['paytr_merchant_key'] ?? env('PAYTR_MERCHANT_KEY', '');
        $this->merchantSalt = $dbSettings['paytr_merchant_salt'] ?? env('PAYTR_MERCHANT_SALT', '');
        $this->isTest = ($dbSettings['paytr_test_mode'] ?? env('PAYTR_TEST_MODE', '1')) == '1';
    }

    /**
     * PayTR aktif mi kontrol et
     */
    public function isEnabled(): bool
    {
        $active = ($this->settings['paytr_active'] ?? '0') == '1';

        return $active && ! empty($this->merchantId) && ! empty($this->merchantKey) && ! empty($this->merchantSalt);
    }

    /**
     * PayTR Güvenli Ödeme Token'ı ve Yönlendirme URL'si Oluştur
     */
    public function createPaymentUrl(Order $order, array $cart, string $userIp): array
    {
        if (empty($this->merchantId) || empty($this->merchantKey) || empty($this->merchantSalt)) {
            return [
                'status' => 'failed',
                'reason' => 'PayTR entegrasyon anahtarları (Merchant ID, Key veya Salt) henüz tanımlanmamış.',
            ];
        }

        $appHost = parse_url(config('app.url', 'http://tazecicekler.test'), PHP_URL_HOST) ?: 'tazecicekler.com';
        $email = $order->customer->email ?? ('musteri_'.$order->id.'@'.$appHost);
        $paymentAmount = (int) round($order->total_amount * 100); // Kuruş cinsinden (örn: 9500 TL -> 950000)
        $userName = $order->customer_name ?: 'Değerli Müşteri';
        $userAddress = $order->address ?: 'İstanbul';
        $userPhone = preg_replace('/[^0-9]/', '', $order->customer_phone) ?: '05555555555';
        $merchantOid = $order->order_number;

        // Sepet İçeriğini PayTR formatında hazırla
        $basketItems = [];
        foreach ($cart as $item) {
            $basketItems[] = [
                $item['name'],
                number_format($item['price'], 2, '.', ''),
                (int) $item['quantity'],
            ];
        }
        if ($order->delivery_fee > 0) {
            $basketItems[] = [
                'Hızlı Kurye / Teslimat Hizmeti',
                number_format($order->delivery_fee, 2, '.', ''),
                1,
            ];
        }
        if (empty($basketItems)) {
            $basketItems[] = [(config('app.name', 'Yeniçiçek')).' Siparişi ('.$order->order_number.')', number_format($order->total_amount, 2, '.', ''), 1];
        }
        $userBasket = base64_encode(json_encode($basketItems));

        $merchantOkUrl = route('cart.success', $order->order_number);
        $merchantFailUrl = route('paytr.fail', $order->order_number);

        $noInstallment = 0; // 0: Taksit aktif, 1: Sadece tek çekim
        $maxInstallment = (int) ($this->settings['paytr_max_installment'] ?? 12);
        $currency = 'TL';
        $testMode = $this->isTest ? '1' : '0';

        // HMAC-SHA256 Token İmzası
        $hashStr = $this->merchantId.$userIp.$merchantOid.$email.$paymentAmount.$userBasket.$noInstallment.$maxInstallment.$currency.$testMode;
        $paytrToken = base64_encode(hash_hmac('sha256', $hashStr.$this->merchantSalt, $this->merchantKey, true));

        $postData = [
            'merchant_id' => $this->merchantId,
            'user_ip' => $userIp,
            'merchant_oid' => $merchantOid,
            'email' => $email,
            'payment_amount' => $paymentAmount,
            'paytr_token' => $paytrToken,
            'user_basket' => $userBasket,
            'debug_on' => $this->isTest ? 1 : 0,
            'no_installment' => $noInstallment,
            'max_installment' => $maxInstallment,
            'user_name' => $userName,
            'user_address' => $userAddress,
            'user_phone' => $userPhone,
            'merchant_ok_url' => $merchantOkUrl,
            'merchant_fail_url' => $merchantFailUrl,
            'timeout_limit' => 30, // 30 dakika
            'currency' => $currency,
            'test_mode' => $testMode,
        ];

        try {
            $response = Http::asForm()->timeout(20)->post('https://www.paytr.com/odeme/api/get-token', $postData);
            $result = $response->json();

            if (isset($result['status']) && $result['status'] === 'success') {
                return [
                    'status' => 'success',
                    'token' => $result['token'],
                    'payment_url' => 'https://www.paytr.com/odeme/guvenli/'.$result['token'],
                ];
            }

            Log::error('PayTR Token Alınamadı: '.($result['reason'] ?? 'Bilinmeyen Hata'), ['response' => $result, 'order_id' => $order->id]);

            return [
                'status' => 'failed',
                'reason' => $result['reason'] ?? 'PayTR token oluşturulamadı.',
            ];
        } catch (\Exception $e) {
            Log::error('PayTR İstek Hatası: '.$e->getMessage());

            return [
                'status' => 'failed',
                'reason' => 'PayTR sunucusuna bağlanırken hata oluştu: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Webhook Bildiriminin (Callback) HMAC-SHA256 İmzasını Doğrula
     */
    public function verifyCallbackHash(string $merchantOid, string $status, string $totalAmount, string $receivedHash): bool
    {
        $expectedHash = base64_encode(hash_hmac('sha256', $merchantOid.$this->merchantSalt.$status.$totalAmount, $this->merchantKey, true));

        return hash_equals($expectedHash, $receivedHash);
    }
}
