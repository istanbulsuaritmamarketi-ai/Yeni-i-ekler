<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WorkshopController extends Controller
{
    /**
     * Çiçekçi Atölye ve Mobil Kamera Uygulaması
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'preparing');
        $search = $request->get('search');

        // Canlı Atölye Sayaçları
        $counts = [
            'preparing' => Order::whereIn('status', ['Yeni Sipariş', 'Atölyede Hazırlanıyor', 'Hazırlanıyor', 'pending', 'processing'])->count(),
            'photo_review' => Order::where('status', 'Fotoğraf Onayında')->count(),
            'on_delivery' => Order::whereIn('status', ['Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Kuryede'])->count(),
            'completed' => Order::whereIn('status', ['Teslim Edildi', 'Tamamlandı'])->count(),
            'all' => Order::count(),
        ];

        $query = Order::with(['region', 'items.product']);

        // Arama (Sipariş No, Alıcı Adı, Telefon, Semt)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('recipient_phone', 'like', "%{$search}%")
                  ->orWhere('recipient_district', 'like', "%{$search}%");
            });
        }

        // Sekmeye Göre Filtre
        if ($tab === 'preparing') {
            $query->whereIn('status', ['Yeni Sipariş', 'Atölyede Hazırlanıyor', 'Hazırlanıyor', 'pending', 'processing']);
        } elseif ($tab === 'photo_review') {
            $query->where('status', 'Fotoğraf Onayında');
        } elseif ($tab === 'on_delivery') {
            $query->whereIn('status', ['Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Kuryede']);
        } elseif ($tab === 'completed') {
            $query->whereIn('status', ['Teslim Edildi', 'Tamamlandı']);
        }

        // Öncelik Sıralaması: Hızlı Teslimatlar & Yakın Saatler Öne
        $orders = $query->orderByRaw('CASE WHEN delivery_fee > 0 THEN 0 ELSE 1 END')
            ->orderBy('delivery_date', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(30)
            ->withQueryString();

        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $latestOrderId = Order::max('id') ?? 0;

        return view('admin.workshop.index', compact('orders', 'tab', 'search', 'counts', 'settings', 'latestOrderId'));
    }

    /**
     * Atölye Kamerası İle Fotoğraf Yükleme & Durum Güncelleme
     */
    public function uploadPhoto(Request $request, Order $order)
    {
        $request->validate([
            'preparation_photo' => 'required|file|mimes:jpeg,png,jpg,webp|max:15360',
            'status' => 'nullable|string',
        ]);

        if ($request->hasFile('preparation_photo')) {
            // Eski fotoğraf varsa temizle (varsa)
            if ($order->preparation_photo && Storage::disk('public')->exists($order->preparation_photo)) {
                // opsiyonel
            }

            $path = $request->file('preparation_photo')->store('orders/preparation', 'public');
            $newStatus = $request->input('status', 'Fotoğraf Onayında');

            $order->update([
                'preparation_photo' => $path,
                'status' => $newStatus,
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => '🌸 Atölye fotoğrafı başarıyla yüklendi ve durum güncellendi!',
                    'photo_url' => asset('storage/' . $path),
                    'status' => $order->status,
                ]);
            }

            return redirect()->back()->with('success', '🌸 Atölye fotoğrafı başarıyla yüklendi. Sipariş durumu: '.$newStatus);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Fotoğraf dosyası bulunamadı.'], 422);
        }

        return redirect()->back()->with('error', 'Lütfen geçerli bir fotoğraf seçin.');
    }

    /**
     * Hızlı Durum Güncelleme (Tek Dokunuş)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $status = $request->input('status');
        $order->update(['status' => $status]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Sipariş durumu '{$status}' olarak güncellendi.",
                'status' => $status,
            ]);
        }

        return redirect()->back()->with('success', "Sipariş durumu '{$status}' olarak güncellendi.");
    }

    /**
     * 🔔 Canlı Sipariş Kontrolü & Yeni Sipariş Bildirim API'si
     */
    public function checkNewOrders(Request $request)
    {
        $hasLastId = $request->has('last_id') && $request->input('last_id') !== null && $request->input('last_id') !== '';
        $lastId = (int) $request->get('last_id', 0);
        $latestId = Order::max('id') ?? 0;

        // Parametre gönderilmediyse sadece mevcut başlangıç durumunu bildir
        if (! $hasLastId) {
            return response()->json([
                'has_new' => false,
                'count' => 0,
                'latest_id' => $latestId,
                'orders' => [],
                'counts' => [
                    'preparing' => Order::whereIn('status', ['Yeni Sipariş', 'Atölyede Hazırlanıyor', 'Hazırlanıyor', 'pending', 'processing'])->count(),
                    'photo_review' => Order::where('status', 'Fotoğraf Onayında')->count(),
                    'on_delivery' => Order::whereIn('status', ['Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Kuryede'])->count(),
                    'completed' => Order::whereIn('status', ['Teslim Edildi', 'Tamamlandı'])->count(),
                    'all' => Order::count(),
                ],
            ]);
        }

        // Son bilinen id'den sonra yeni siparişler var mı?
        $newOrders = Order::with(['region', 'items.product'])
            ->where('id', '>', $lastId)
            ->latest('id')
            ->get();

        $counts = [
            'preparing' => Order::whereIn('status', ['Yeni Sipariş', 'Atölyede Hazırlanıyor', 'Hazırlanıyor', 'pending', 'processing'])->count(),
            'photo_review' => Order::where('status', 'Fotoğraf Onayında')->count(),
            'on_delivery' => Order::whereIn('status', ['Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Kuryede'])->count(),
            'completed' => Order::whereIn('status', ['Teslim Edildi', 'Tamamlandı'])->count(),
            'all' => Order::count(),
        ];

        return response()->json([
            'has_new' => $newOrders->isNotEmpty(),
            'count' => $newOrders->count(),
            'latest_id' => $latestId,
            'orders' => $newOrders->map(function ($order) {
                $firstItem = $order->items->first();
                $itemNames = $order->items->map(fn($item) => ($item->product->name ?? 'Çiçek') . ' (' . $item->quantity . ' Adet)')->implode(', ');
                $district = $order->recipient_district ?: ($order->region->name ?? 'İstanbul');
                $productName = $firstItem && $firstItem->product ? $firstItem->product->name : 'Özel Çiçek Tasarımı';

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'recipient_name' => $order->recipient_name ?: $order->customer_name,
                    'recipient_district' => $district,
                    'product_name' => $productName,
                    'product_image' => $firstItem && $firstItem->product ? $firstItem->product->image_url : asset('images/products/6.png'),
                    'total_amount' => number_format($order->total_amount, 2, ',', '.') . ' TL',
                    'delivery_time_slot' => $order->delivery_time_slot ?: 'Standart Aralık',
                    'is_vip' => $order->delivery_fee > 0,
                    'card_note' => $order->card_note,
                    'sender_name' => $order->is_anonymous ? 'Gizli Gönderici' : ($order->sender_name ?: $order->customer_name),
                    'items_summary' => $itemNames,
                    'recipe_notes' => $order->notes,
                    'status' => $order->status,
                    'created_at_human' => $order->created_at->diffForHumans(),
                    'order_url' => route('admin.orders.show', $order),
                    'workshop_url' => route('admin.workshop.index', ['search' => $order->order_number]),
                    'craftsman_url' => route('admin.workshop.craftsman', ['search' => $order->order_number]),
                    'speech_text' => 'Yeni sipariş düştü. ' . $district . ', ' . $productName,
                ];
            }),
            'counts' => $counts,
        ]);
    }

    /**
     * 👨‍🌾 Usta Sipariş Hazırlama Uygulaması (Florist Workshop Preparation Screen)
     * Sadece sipariş hazırlamak için ustaya özel tam ekran, sesli bildirimli atölye ekranı
     */
    public function craftsman(Request $request)
    {
        $tab = $request->get('tab', 'preparing');
        $search = $request->get('search');

        // Canlı Atölye Sayaçları
        $counts = [
            'preparing' => Order::whereIn('status', ['Yeni Sipariş', 'Atölyede Hazırlanıyor', 'Hazırlanıyor', 'pending', 'processing'])->count(),
            'photo_review' => Order::where('status', 'Fotoğraf Onayında')->count(),
            'on_delivery' => Order::whereIn('status', ['Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Kuryede'])->count(),
            'today' => Order::whereDate('created_at', Carbon::today())->count(),
            'all' => Order::count(),
        ];

        $query = Order::with(['region', 'items.product']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('recipient_district', 'like', "%{$search}%");
            });
        }

        if ($tab === 'preparing') {
            $query->whereIn('status', ['Yeni Sipariş', 'Atölyede Hazırlanıyor', 'Hazırlanıyor', 'pending', 'processing']);
        } elseif ($tab === 'photo_review') {
            $query->where('status', 'Fotoğraf Onayında');
        } elseif ($tab === 'on_delivery') {
            $query->whereIn('status', ['Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Kuryede']);
        } elseif ($tab === 'today') {
            $query->whereDate('created_at', Carbon::today());
        }

        // Öncelik Sıralaması: VIP hızlı kurye ilk sırada, sonra teslimat saati, sonra en yeni
        $orders = $query->orderByRaw('CASE WHEN delivery_fee > 0 THEN 0 ELSE 1 END')
            ->orderBy('delivery_date', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        $settings = Setting::all()->pluck('value', 'key')->toArray();
        $latestOrderId = Order::max('id') ?? 0;

        return view('admin.workshop.craftsman', compact('orders', 'tab', 'search', 'counts', 'settings', 'latestOrderId'));
    }
}
