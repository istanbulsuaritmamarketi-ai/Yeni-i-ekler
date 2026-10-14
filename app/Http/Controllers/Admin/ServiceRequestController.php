<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Region;
use App\Models\ServiceRequest;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $query = ServiceRequest::with('region');

        if ($tab === 'today') {
            $query->whereDate('preferred_date', Carbon::today());
        } elseif ($tab === 'pending') {
            $query->whereNotIn('status', ['Tamamlandı', 'İptal Edildi']);
        } elseif ($tab === 'completed') {
            $query->where('status', 'Tamamlandı');
        } elseif ($tab === 'cancelled') {
            $query->where('status', 'İptal Edildi');
        }

        $requests = $query->orderBy('preferred_date', 'asc')->paginate(20)->withQueryString();
        $trashedCount = ServiceRequest::onlyTrashed()->count();

        return view('admin.services.index', compact('requests', 'trashedCount', 'tab'));
    }

    public function create()
    {
        $regions = Region::where('is_active', true)->orderBy('name')->get();

        return view('admin.services.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'region_id' => 'required|exists:regions,id',
            'address' => 'required|string',
            'service_type' => 'required|string|max:255',
            'device_info' => 'nullable|string|max:255',
            'issue_description' => 'nullable|string',
            'preferred_date' => 'required|date',
            'preferred_time' => 'required|string|max:50',
            'status' => 'required|string|max:50',
            'service_price' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'assigned_staff' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
            'next_maintenance_date' => 'nullable|date',
        ]);

        $customer = Customer::firstOrCreate(
            ['phone' => $validated['customer_phone']],
            [
                'name' => $validated['customer_name'],
                'address' => $validated['address'],
                'region_id' => $validated['region_id'],
            ]
        );

        $validated['customer_id'] = $customer->id;

        if (empty($validated['next_maintenance_date'])) {
            $validated['next_maintenance_date'] = Carbon::parse($validated['preferred_date'])->addMonths(6);
        }

        $service = ServiceRequest::create($validated);

        return redirect()->route('admin.services.show', $service)->with('success', 'Yeni servis kaydı başarıyla açıldı.');
    }

    public function calendar()
    {
        $services = ServiceRequest::whereNotNull('preferred_date')->get()->map(function ($service) {
            return [
                'title' => $service->service_type.' - '.$service->customer_name,
                'start' => Carbon::parse($service->preferred_date)->format('Y-m-d'),
                'url' => route('admin.services.show', $service->id),
                'color' => $service->status == 'Tamamlandı' ? 'green' : ($service->status == 'İptal Edildi' ? 'red' : 'orange'),
            ];
        });

        return view('admin.services.calendar', compact('services'));
    }

    public function show(ServiceRequest $service)
    {
        return view('admin.services.show', compact('service'));
    }

    public function update(Request $request, ServiceRequest $service)
    {
        $request->validate([
            'status' => 'required|string',
            'service_price' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'admin_notes' => 'nullable|string',
            'assigned_staff' => 'nullable|string',
            'next_maintenance_date' => 'nullable|date',
        ]);

        $service->update([
            'status' => $request->status,
            'service_price' => $request->service_price,
            'payment_method' => $request->payment_method,
            'admin_notes' => $request->admin_notes,
            'assigned_staff' => $request->assigned_staff,
            'next_maintenance_date' => $request->next_maintenance_date,
        ]);

        return redirect()->route('admin.services.show', $service)->with('success', 'Servis talebi güncellendi.');
    }

    public function destroy(ServiceRequest $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Servis kaydı çöp kutusuna taşındı.');
    }

    public function print(ServiceRequest $service)
    {
        $service->load('region');
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        return view('admin.services.print', compact('service', 'settings'));
    }

    // Geri Dönüşüm Kutusu (Trash) Metodları
    public function trash()
    {
        $trashedRequests = ServiceRequest::onlyTrashed()->with('region')->latest('deleted_at')->paginate(20);

        return view('admin.services.trash', compact('trashedRequests'));
    }

    public function restore($id)
    {
        $service = ServiceRequest::onlyTrashed()->findOrFail($id);
        $service->restore();

        return redirect()->route('admin.services.trash')->with('success', "{$service->customer_name} müşterisine ait servis kaydı başarıyla geri yüklendi.");
    }

    public function forceDelete($id)
    {
        $service = ServiceRequest::onlyTrashed()->findOrFail($id);
        $service->forceDelete();

        return redirect()->route('admin.services.trash')->with('success', 'Servis kaydı kalıcı olarak silindi.');
    }

    public function emptyTrash()
    {
        ServiceRequest::onlyTrashed()->forceDelete();

        return redirect()->route('admin.services.index')->with('success', 'Geri dönüşüm kutusundaki tüm silinmiş kayıtlar kalıcı olarak temizlendi.');
    }

    // Yapılan / Tamamlanan İşler Bölümü
    public function completed(Request $request)
    {
        $query = ServiceRequest::with('region')->where('status', 'Tamamlandı');

        if ($request->filled('staff')) {
            $query->where('assigned_staff', 'like', '%'.$request->staff.'%');
        }
        if ($request->filled('region_id')) {
            $query->where('region_id', $request->region_id);
        }
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('device_info', 'like', "%{$search}%");
            });
        }

        $completedJobs = (clone $query)->orderBy('preferred_date', 'desc')->paginate(20)->withQueryString();

        $totalCompletedCount = ServiceRequest::where('status', 'Tamamlandı')->count();
        $totalCompletedRevenue = ServiceRequest::where('status', 'Tamamlandı')->whereNotNull('service_price')->sum('service_price');
        $thisMonthJobs = ServiceRequest::where('status', 'Tamamlandı')
            ->whereBetween('preferred_date', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()])
            ->count();
        $thisMonthRevenue = ServiceRequest::where('status', 'Tamamlandı')
            ->whereBetween('preferred_date', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()])
            ->whereNotNull('service_price')
            ->sum('service_price');

        $regions = Region::where('is_active', true)->orderBy('name')->get();

        return view('admin.services.completed', compact(
            'completedJobs',
            'totalCompletedCount',
            'totalCompletedRevenue',
            'thisMonthJobs',
            'thisMonthRevenue',
            'regions'
        ));
    }

    /**
     * Servis ve Montaj Kayıtlarını Excel (CSV) Formatında Dışa Aktar
     */
    public function exportExcel(Request $request)
    {
        $requests = ServiceRequest::with('region')->orderBy('preferred_date', 'desc')->get();
        $fileName = 'Servis_Montaj_Kayitlari_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->stream(function () use ($requests) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID',
                'Müşteri Adı',
                'Telefon',
                'İlçe / Bölge',
                'Adres',
                'Hizmet Türü',
                'Cihaz Bilgisi',
                'Arıza / Açıklama',
                'Randevu Tarihi',
                'Randevu Saati',
                'Durum',
                'Servis Ücreti (TL)',
                'Ödeme Yöntemi',
                'Atanan Teknisyen',
                'Sonraki Bakım Tarihi',
            ], ';');

            foreach ($requests as $s) {
                fputcsv($handle, [
                    $s->id,
                    $s->customer_name,
                    $s->customer_phone,
                    $s->region->name ?? '-',
                    $s->address,
                    $s->service_type,
                    $s->device_info ?: '-',
                    $s->issue_description ?: '-',
                    $s->preferred_date ? Carbon::parse($s->preferred_date)->format('d.m.Y') : '-',
                    $s->preferred_time ?: '-',
                    $s->status,
                    number_format($s->service_price ?? 0, 2, ',', ''),
                    $s->payment_method ?: '-',
                    $s->assigned_staff ?: '-',
                    $s->next_maintenance_date ? Carbon::parse($s->next_maintenance_date)->format('d.m.Y') : '-',
                ], ';');
            }
            fclose($handle);
        }, 200, $headers);
    }
}
