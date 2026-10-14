<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $search = $request->get('search');

        // Canlı Sayaçlar (Florist Badge Counts)
        $counts = [
            'all' => Order::count(),
            'today' => Order::where(function ($q) {
                $q->whereDate('delivery_date', Carbon::today())
                  ->orWhereDate('created_at', Carbon::today());
            })->count(),
            'preparing' => Order::whereIn('status', ['Yeni Sipariş', 'Atölyede Hazırlanıyor', 'Hazırlanıyor', 'pending', 'processing'])->count(),
            'photo_review' => Order::where('status', 'Fotoğraf Onayında')->count(),
            'on_delivery' => Order::whereIn('status', ['Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Kuryede'])->count(),
            'completed' => Order::whereIn('status', ['Teslim Edildi', 'Tamamlandı'])->count(),
            'cancelled' => Order::whereIn('status', ['İptal Edildi', 'İptal edildi', 'cancelled'])->count(),
        ];

        $query = Order::with(['region', 'items.product'])->latest();

        // Arama Filtresi
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

        // Sekme Filtreleri
        if ($tab === 'today') {
            $query->where(function ($q) {
                $q->whereDate('delivery_date', Carbon::today())
                  ->orWhereDate('created_at', Carbon::today());
            });
        } elseif ($tab === 'preparing') {
            $query->whereIn('status', ['Yeni Sipariş', 'Atölyede Hazırlanıyor', 'Hazırlanıyor', 'pending', 'processing']);
        } elseif ($tab === 'photo_review') {
            $query->where('status', 'Fotoğraf Onayında');
        } elseif ($tab === 'on_delivery') {
            $query->whereIn('status', ['Kuryede / Dağıtımda', 'Yolda', 'on_delivery', 'Kuryede']);
        } elseif ($tab === 'completed') {
            $query->whereIn('status', ['Teslim Edildi', 'Tamamlandı']);
        } elseif ($tab === 'cancelled') {
            $query->whereIn('status', ['İptal Edildi', 'İptal edildi', 'cancelled']);
        } elseif ($tab === 'active') {
            $query->whereNotIn('status', ['Teslim Edildi', 'Tamamlandı', 'İptal Edildi', 'İptal edildi', 'cancelled']);
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders', 'tab', 'counts', 'search'));
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'region']);
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.orders.show', compact('order', 'settings'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
            'delivery_date' => 'nullable|date',
            'delivery_time_slot' => 'nullable|string',
            'notes' => 'nullable|string',
            'card_note' => 'nullable|string',
            'recipient_name' => 'nullable|string',
            'recipient_phone' => 'nullable|string',
            'recipient_district' => 'nullable|string',
            'payment_status' => 'nullable|string',
        ]);

        $order->update($request->only([
            'status',
            'delivery_date',
            'delivery_time_slot',
            'notes',
            'card_note',
            'recipient_name',
            'recipient_phone',
            'recipient_district',
            'payment_status',
        ]));

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Sipariş detayları ve teslimat durumu başarıyla güncellendi.');
    }

    /**
     * 🌸 Çiçekçilik Özel: Atölye Hazırlık Fotoğrafı Yükleme
     */
    public function uploadPhoto(Request $request, Order $order)
    {
        $request->validate([
            'preparation_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:12288',
        ]);

        if ($request->hasFile('preparation_photo')) {
            $path = $request->file('preparation_photo')->store('orders/preparation', 'public');
            $newStatus = $request->input('status', 'Fotoğraf Onayında');
            $order->update([
                'preparation_photo' => $path,
                'status' => $newStatus,
            ]);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', '🌸 Atölye hazırlık fotoğrafı başarıyla yüklendi ve canlı sipariş takip sayfasına yansıtıldı.');
    }

    public function print(Request $request, Order $order)
    {
        $order->load(['items.product', 'region']);
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.orders.print', [
            'orders' => collect([$order]),
            'order' => $order,
            'settings' => $settings,
            'isBulk' => false,
        ]);
    }

    /**
     * Seçilen Siparişleri 4'lü Perforajlı A4 Kağıdına Toplu Yazdır
     */
    public function bulkPrint(Request $request)
    {
        $ids = $request->input('order_ids', $request->input('ids', []));
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        $orders = Order::with(['items.product', 'region'])->whereIn('id', $ids)->latest()->get();
        if ($orders->isEmpty()) {
            return redirect()->route('admin.orders.index')->with('error', 'Lütfen yazdırılacak en az bir sipariş seçiniz.');
        }

        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.orders.print', [
            'orders' => $orders,
            'order' => $orders->first(),
            'settings' => $settings,
            'isBulk' => true,
        ]);
    }

    /**
     * Siparişleri Excel (CSV) Formatında Dışa Aktar
     */
    public function exportExcel(Request $request)
    {
        $orders = Order::with(['region', 'items.product'])->latest()->get();
        $fileName = 'Tazecicekler_Siparisler_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->stream(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Sipariş No',
                'Müşteri / Gönderici',
                'Alıcı Adı',
                'Alıcı Telefonu',
                'Teslimat İlçesi',
                'Teslimat Tarihi',
                'Teslimat Saat Aralığı',
                'Adres',
                'Kart Notu',
                'Toplam Tutar (TL)',
                'Ödeme Yöntemi',
                'Ödeme Durumu',
                'Sipariş Durumu',
                'Hazırlık Fotoğrafı',
                'Çiçek & Ürünler',
                'Sipariş Tarihi',
            ], ';');

            foreach ($orders as $order) {
                $itemNames = $order->items->map(fn ($item) => ($item->product->name ?? 'Ürün')." ({$item->quantity} Adet)")->implode(', ');
                fputcsv($handle, [
                    $order->order_number,
                    $order->customer_name,
                    $order->recipient_name ?? $order->customer_name,
                    $order->recipient_phone ?? $order->customer_phone,
                    $order->recipient_district ?? ($order->region->name ?? '-'),
                    $order->delivery_date ? Carbon::parse($order->delivery_date)->format('d.m.Y') : '-',
                    $order->delivery_time_slot ?? 'Standart',
                    $order->address,
                    $order->card_note ?? '-',
                    number_format($order->total_amount, 2, ',', ''),
                    $order->payment_method ?? 'Kredi Kartı',
                    $order->payment_status ?? 'Bekliyor',
                    $order->status,
                    $order->preparation_photo ? 'Yüklendi' : 'Yok',
                    $itemNames,
                    $order->created_at->format('d.m.Y H:i:s'),
                ], ';');
            }
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Siparişi Sistemden Kalıcı Olarak Sil
     */
    public function destroy(Order $order)
    {
        $orderNumber = $order->order_number;
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', "#{$orderNumber} numaralı sipariş başarıyla silindi.");
    }
}
