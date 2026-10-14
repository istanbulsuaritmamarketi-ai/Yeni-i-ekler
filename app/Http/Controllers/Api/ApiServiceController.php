<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Region;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApiServiceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'service_type' => 'required|string',
        ]);

        $customer = Customer::findOrFail($request->customer_id);

        $reminderDate = null;
        if ($request->filled('reminder_date')) {
            try {
                $reminderDate = Carbon::parse($request->reminder_date);
            } catch (\Exception $e) {
                $reminderDate = null;
            }
        }

        if (! $reminderDate && in_array(strtolower($request->service_type), ['filtre_degisimi', 'filtre değişimi', 'bakim', 'bakım'])) {
            $reminderDate = Carbon::now()->addMonths(6);
        }

        $savedPhotos = [];
        if ($request->has('photos') && is_array($request->photos)) {
            foreach ($request->photos as $index => $photoData) {
                if (str_starts_with($photoData, 'data:image')) {
                    try {
                        @[$type, $data] = explode(';', $photoData);
                        @[, $data] = explode(',', $data);
                        $decoded = base64_decode($data);
                        $fileName = 'services/'.uniqid('srv_').'_'.$index.'.jpg';
                        Storage::disk('public')->put($fileName, $decoded);
                        $savedPhotos[] = Storage::disk('public')->url($fileName);
                    } catch (\Exception $e) {
                    }
                } elseif (filter_var($photoData, FILTER_VALIDATE_URL)) {
                    $savedPhotos[] = $photoData;
                }
            }
        }

        $serviceTypeLabels = [
            'montaj' => 'Yeni Montaj / Kurulum',
            'bakim' => 'Periyodik Bakım',
            'filtre_degisimi' => 'Filtre Değişimi',
            'ariza' => 'Arıza / Tamir',
        ];
        $typeFormatted = $serviceTypeLabels[strtolower($request->service_type)] ?? $request->service_type;

        $service = ServiceRequest::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'region_id' => $customer->region_id ?: (Region::first()?->id ?? null),
            'address' => $customer->address ?? 'Belirtilmedi',
            'service_type' => $typeFormatted,
            'changed_filters' => $request->changed_filters,
            'service_note' => $request->service_note,
            'issue_description' => $request->service_note,
            'device_info' => ($customer->device_brand ? $customer->device_brand.' ' : '').($customer->device_model ?? ''),
            'tds_value' => $request->tds_value,
            'photos' => $savedPhotos,
            'service_price' => $request->price,
            'status' => 'Tamamlandı',
            'preferred_date' => Carbon::now()->toDateString(),
            'preferred_time' => Carbon::now()->format('H:i'),
            'assigned_staff' => $request->user()?->name ?? 'Mobil Saha Servisi',
            'next_maintenance_date' => $reminderDate ? $reminderDate->toDateString() : null,
            'is_reminder_sent' => false,
        ]);

        if ($request->filled('tds_value')) {
            $customer->update(['tds_value' => $request->tds_value]);
        }

        return response()->json([
            'message' => 'Servis kaydı başarıyla oluşturuldu',
            'service' => [
                'id' => (string) $service->id,
                'service_type' => $service->service_type,
                'customer_name' => $service->customer_name,
                'created_at' => $service->created_at->toIso8601String(),
            ],
        ], 201);
    }

    public function customerServices($customerId)
    {
        $services = ServiceRequest::where('customer_id', $customerId)
            ->latest()
            ->get()
            ->map(function ($s) {
                return [
                    'id' => (string) $s->id,
                    'service_type' => $s->service_type,
                    'changed_filters' => $s->changed_filters,
                    'service_note' => $s->service_note ?: ($s->admin_notes ?: $s->issue_description),
                    'tds_value' => $s->tds_value !== null ? (float) $s->tds_value : null,
                    'price' => $s->service_price !== null ? (float) $s->service_price : null,
                    'photos' => is_array($s->photos) ? $s->photos : [],
                    'created_at' => $s->created_at?->toIso8601String(),
                ];
            });

        return response()->json($services);
    }
}
