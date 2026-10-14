<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiReminderController extends Controller
{
    public function index(Request $request)
    {
        $filterType = $request->query('filter_type', 'weekly');
        $today = Carbon::today();

        $query = ServiceRequest::whereNotNull('next_maintenance_date')
            ->where('is_reminder_sent', false);

        if ($filterType === 'daily') {
            $query->whereDate('next_maintenance_date', '<=', $today);
        } elseif ($filterType === 'weekly') {
            $query->whereDate('next_maintenance_date', '<=', $today->copy()->endOfWeek());
        } elseif ($filterType === 'monthly') {
            $query->whereDate('next_maintenance_date', '<=', $today->copy()->endOfMonth());
        }

        $reminders = $query->orderBy('next_maintenance_date', 'asc')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => (string) $service->id,
                    'customer_name' => $service->customer_name,
                    'customer_id' => (string) ($service->customer_id ?? $service->id),
                    'customer_phone' => $service->customer_phone,
                    'reminder_date' => Carbon::parse($service->next_maintenance_date)->setTime(9, 0)->toIso8601String(),
                    'is_sent' => (bool) $service->is_reminder_sent,
                    'service_type' => $service->service_type,
                    'device_info' => $service->device_info,
                ];
            });

        return response()->json($reminders);
    }

    public function markSent($id)
    {
        $service = ServiceRequest::findOrFail($id);
        $service->is_reminder_sent = true;
        $service->save();

        return response()->json([
            'message' => 'Hatırlatıcı tamamlandı olarak işaretlendi',
        ]);
    }
}
