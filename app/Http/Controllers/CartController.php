<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Region;
use App\Models\Setting;
use App\Services\PayTRService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $couponData = session()->get('coupon', null);
        $flowerDelivery = session()->get('flower_delivery', []);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discount = 0;
        if ($couponData && ! empty($cart)) {
            $coupon = Coupon::where('code', $couponData['code'])->first();
            if ($coupon && $coupon->isValidForAmount($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                session()->forget('coupon');
                $couponData = null;
            }
        }

        $total = max(0, $subtotal - $discount);
        $upsellProducts = Product::where('is_active', true)
            ->whereHas('category', fn ($q) => $q->where('slug', 'cikolata-ve-hediye-eslikcileri'))
            ->whereNotIn('id', array_keys($cart))
            ->take(4)
            ->get();

        return view('cart.index', compact('cart', 'subtotal', 'discount', 'total', 'couponData', 'flowerDelivery', 'upsellProducts'));
    }

    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        $quantity = max(1, (int) $request->input('quantity', 1));

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->effective_price,
                'original_price' => $product->price,
                'campaign_price' => $product->campaign_price,
                'quantity' => $quantity,
                'image' => $product->image_path,
                'slug' => $product->slug,
            ];
        }

        // Çiçek teslimat ve kart notu tercihlerini oturumda sakla
        if ($request->filled('delivery_date') || $request->filled('card_note') || $request->filled('delivery_time_slot')) {
            $flowerDelivery = session()->get('flower_delivery', []);
            $flowerDelivery = array_merge($flowerDelivery, array_filter([
                'delivery_date' => $request->input('delivery_date'),
                'delivery_time_slot' => $request->input('delivery_time_slot'),
                'recipient_district' => $request->input('recipient_district'),
                'card_note' => $request->input('card_note'),
                'card_template_id' => $request->input('card_template_id'),
                'sender_name' => $request->input('sender_name'),
                'is_anonymous' => $request->boolean('is_anonymous'),
            ], fn ($v) => ! is_null($v)));
            session()->put('flower_delivery', $flowerDelivery);
        }

        // Çapraz satış (Upsell) ürünlerini sepete ekle
        if ($request->has('upsell_products') && is_array($request->upsell_products)) {
            foreach ($request->upsell_products as $upsellId) {
                $upsellProd = Product::find($upsellId);
                if ($upsellProd) {
                    if (isset($cart[$upsellProd->id])) {
                        $cart[$upsellProd->id]['quantity']++;
                    } else {
                        $cart[$upsellProd->id] = [
                            'id' => $upsellProd->id,
                            'name' => $upsellProd->name,
                            'price' => $upsellProd->effective_price,
                            'original_price' => $upsellProd->price,
                            'campaign_price' => $upsellProd->campaign_price,
                            'quantity' => 1,
                            'image' => $upsellProd->image_path,
                            'slug' => $upsellProd->slug,
                            'is_upsell' => true,
                        ];
                    }
                }
            }
        }

        session()->put('cart', $cart);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Ürün sepete eklendi!',
                'cartCount' => count($cart),
                'cart' => $cart,
            ]);
        }

        if ($request->input('redirect_to') === 'checkout') {
            return redirect()->route('checkout');
        }

        return redirect()->route('cart.index')->with('success', 'Ürün sepete eklendi!');
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        $action = $request->input('action');

        if (isset($cart[$id])) {
            if ($action === 'increase') {
                $cart[$id]['quantity']++;
            } elseif ($action === 'decrease') {
                $cart[$id]['quantity']--;
                if ($cart[$id]['quantity'] <= 0) {
                    unset($cart[$id]);
                }
            } elseif ($request->has('quantity')) {
                $qty = (int) $request->input('quantity');
                if ($qty > 0) {
                    $cart[$id]['quantity'] = $qty;
                } else {
                    unset($cart[$id]);
                }
            }
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Sepet güncellendi.');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Ürün sepetten kaldırıldı.');
    }

    public function clear()
    {
        session()->forget(['cart', 'coupon', 'flower_delivery']);

        return redirect()->route('cart.index')->with('success', 'Sepetiniz temizlendi.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return back()->with('error', 'Sepetiniz boşken kupon uygulayamazsınız.');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $code = strtoupper(trim($request->code));
        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon) {
            return back()->with('error', 'Geçersiz veya bulunamayan kupon kodu.');
        }

        if (! $coupon->isValidForAmount($subtotal)) {
            if ($coupon->min_order_amount > $subtotal) {
                return back()->with('error', "Bu kuponu kullanabilmek için sepet tutarınız en az {$coupon->min_order_amount} TL olmalıdır.");
            }
            if ($coupon->expires_at && $coupon->expires_at->isPast()) {
                return back()->with('error', 'Bu kuponun kullanım süresi dolmuştur.');
            }
            if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
                return back()->with('error', 'Bu kupon maksimum kullanım limitine ulaşmıştır.');
            }

            return back()->with('error', 'Kupon şu anda kullanılamıyor.');
        }

        $discount = $coupon->calculateDiscount($subtotal);

        session()->put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount,
        ]);

        return back()->with('success', "Tebrikler! \"{$coupon->code}\" kuponu uygulandı ve {$discount} TL indirim kazandınız.");
    }

    public function removeCoupon()
    {
        session()->forget('coupon');

        return back()->with('success', 'Kupon sepetten kaldırıldı.');
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('home');
        }

        $couponData = session()->get('coupon', null);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $discount = 0;
        if ($couponData) {
            $coupon = Coupon::where('code', $couponData['code'])->first();
            if ($coupon && $coupon->isValidForAmount($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            }
        }
        $total = max(0, $subtotal - $discount);

        $regions = Region::where('is_active', true)->orderBy('city')->orderBy('name')->get();
        $flowerDelivery = session()->get('flower_delivery', []);

        $timeSlots = [
            '09:00 - 12:00 (Sabah)',
            '12:00 - 15:00 (Öğle)',
            '15:00 - 18:00 (Öğleden Sonra)',
            '18:00 - 22:00 (Akşam)',
            'Özel Kurye VIP (1 Saatte Teslimat)',
        ];

        $expressCourierActive = (bool) Setting::get('express_courier_active', '1');
        $expressCourierFee = (float) Setting::get('express_courier_fee', '150');
        $expressCourierTitle = Setting::get('express_courier_title', 'VIP Hızlı Kurye (60-90 Dk)');
        $expressCourierSubtitle = Setting::get('express_courier_subtitle', 'Öncelikli atölye hazırlığı & ekspres motorlu kurye');

        return view('cart.checkout', compact(
            'cart',
            'regions',
            'subtotal',
            'discount',
            'total',
            'couponData',
            'flowerDelivery',
            'timeSlots',
            'expressCourierActive',
            'expressCourierFee',
            'expressCourierTitle',
            'expressCourierSubtitle'
        ));
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:255',
            'address' => 'required|string',
            'region_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('regions', 'id')->where('is_active', true),
            ],
            'recipient_name' => 'nullable|string|max:255',
            'recipient_phone' => 'nullable|string|max:255',
            'delivery_date' => 'nullable|string',
            'delivery_time_slot' => 'nullable|string',
            'card_note' => 'nullable|string|max:1000',
            'delivery_type' => 'nullable|string|in:standard,express',
            'is_express_courier' => 'nullable|boolean',
            'payment_method' => 'nullable|string|in:paytr,cash,cod',
        ], [
            'region_id.exists' => 'Seçilen ilçe için şu anda teslimat hizmeti verilmemektedir.',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('home');
        }

        // Calculate total amount and total cost
        $subtotal = 0;
        $totalCost = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];

            $product = Product::find($item['id']);
            if ($product && $product->cost_price) {
                $totalCost += $product->cost_price * $item['quantity'];
            }
        }

        // Apply coupon if exists
        $couponData = session()->get('coupon', null);
        $discount = 0;
        $couponCode = null;
        if ($couponData) {
            $coupon = Coupon::where('code', $couponData['code'])->first();
            if ($coupon && $coupon->isValidForAmount($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
                $couponCode = $coupon->code;
                $coupon->increment('used_count');
            }
        }

        // Express Courier / Hızlı Kurye Ücret Hesaplama
        $isExpressCourier = $request->input('delivery_type') === 'express' || $request->boolean('is_express_courier');
        $expressActive = (bool) Setting::get('express_courier_active', '1');
        $deliveryFee = 0;
        $expressTitle = Setting::get('express_courier_title', 'VIP Hızlı Kurye (60-90 Dk)');

        if ($isExpressCourier && $expressActive) {
            $deliveryFee = (float) Setting::get('express_courier_fee', 150);
        }

        $totalAmount = max(0, $subtotal - $discount + $deliveryFee);

        // Handle Customer
        $cleanPhone = preg_replace('/[^0-9]/', '', $request->customer_phone);
        $customer = Customer::where('phone', $request->customer_phone)
            ->orWhere('phone', $cleanPhone)
            ->first();

        if ($customer) {
            $customer->update([
                'name' => $request->customer_name ?: $customer->name,
                'address' => $request->address ?: $customer->address,
                'region_id' => $request->region_id ?: $customer->region_id,
            ]);
        } else {
            $customer = Customer::create([
                'name' => $request->customer_name,
                'phone' => $request->customer_phone,
                'email' => $request->input('customer_email'),
                'address' => $request->address,
                'region_id' => $request->region_id,
            ]);
        }

        $utmData = session('utm_data', []);
        $flowerSession = session('flower_delivery', []);

        $deliveryDate = $request->input('delivery_date') ?: ($flowerSession['delivery_date'] ?? now()->format('Y-m-d'));
        $deliveryTimeSlot = $request->input('delivery_time_slot') ?: ($flowerSession['delivery_time_slot'] ?? '12:00 - 15:00');
        $recipientName = $request->input('recipient_name') ?: $request->customer_name;
        $recipientPhone = $request->input('recipient_phone') ?: $request->customer_phone;
        $cardNote = $request->input('card_note') ?: ($flowerSession['card_note'] ?? null);
        $senderName = $request->input('sender_name') ?: ($flowerSession['sender_name'] ?? $request->customer_name);
        $isAnonymous = $request->boolean('is_anonymous') ?: ($flowerSession['is_anonymous'] ?? false);

        $notes = $request->notes;
        if ($deliveryFee > 0) {
            $notes = ($notes ? $notes.' | ' : '')."⚡ [{$expressTitle} (+{$deliveryFee} TL)]";
        }
        if ($couponCode) {
            $notes = ($notes ? $notes.' | ' : '')."Kupon: {$couponCode} (-{$discount} TL)";
        }
        if ($cardNote) {
            $notes = ($notes ? $notes.' | ' : '')."🌸 Kart Notu: \"{$cardNote}\"";
        }
        if ($isAnonymous) {
            $notes = ($notes ? $notes.' | ' : '')." [İsim Gizli Gönderim]";
        }

        // Özel Tasarım Buket Reçetesini Sipariş Notlarına Ekle (Atölye, fiş ve kurye için)
        foreach ($cart as $item) {
            if (!empty($item['is_custom_bouquet']) && !empty($item['options']['summary'])) {
                $notes = ($notes ? $notes . " | \n" : '') . "🌸 [ÖZEL BUKET REÇETESİ]: " . $item['options']['summary'];
                if (!empty($item['options']['instructions'])) {
                    $notes .= " (Müşteri Notu: " . $item['options']['instructions'] . ")";
                }
            }
        }

        $selectedRegion = Region::find($request->region_id);
        $districtName = $selectedRegion ? $selectedRegion->name : null;

        $paymentMethod = $request->input('payment_method', 'paytr');
        $isPayTR = $paymentMethod === 'paytr';
        $orderStatus = $isPayTR ? 'Ödeme Bekliyor (PayTR)' : 'Sipariş alındı';

        $order = Order::create([
            'order_number' => 'ORD-'.strtoupper(uniqid()),
            'customer_id' => $customer->id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'address' => $request->address,
            'region_id' => $request->region_id,
            'recipient_name' => $recipientName,
            'recipient_phone' => $recipientPhone,
            'recipient_district' => $districtName,
            'delivery_date' => $deliveryDate,
            'delivery_time_slot' => $deliveryTimeSlot,
            'delivery_fee' => $deliveryFee,
            'card_note' => $cardNote,
            'sender_name' => $senderName,
            'is_anonymous' => $isAnonymous,
            'payment_method' => $paymentMethod,
            'total_amount' => $totalAmount,
            'total_cost' => $totalCost,
            'status' => $orderStatus,
            'wants_service' => false,
            'notes' => $notes,
            'utm_source' => $utmData['utm_source'] ?? null,
            'utm_medium' => $utmData['utm_medium'] ?? null,
            'utm_campaign' => $utmData['utm_campaign'] ?? null,
            'utm_content' => $utmData['utm_content'] ?? null,
            'customer_ip' => $request->ip(),
            'customer_user_agent' => $request->userAgent(),
            'kvkk_accepted_at' => now(),
            'contract_accepted_at' => now(),
        ]);

        foreach ($cart as $item) {
            $itemType = 'product';
            if (isset($item['is_upsell']) && $item['is_upsell']) {
                $itemType = 'upsell';
            } elseif (isset($item['is_custom_bouquet']) && $item['is_custom_bouquet']) {
                $itemType = 'custom_bouquet';
            }

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'item_type' => $itemType,
                'options' => $item['options'] ?? null,
            ]);
        }

        // PayTR ile Kredi Kartı Ödemesi
        if ($isPayTR && config('services.paytr.merchant_id')) {
            $payTRService = app(PayTRService::class);
            $paytrResult = $payTRService->createPaymentUrl($order, $cart, $request->ip());

            if ($paytrResult['status'] === 'success') {
                session()->forget(['cart', 'coupon', 'flower_delivery']);

                return redirect()->away($paytrResult['payment_url']);
            }
        }

        session()->forget(['cart', 'coupon', 'flower_delivery']);

        return redirect()->route('cart.success', $order->order_number);
    }

    public function success(Order $order)
    {
        return view('cart.success', compact('order'));
    }
}
