<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $tab = $request->get('tab', 'due_this_month');

        $query = ServiceRequest::with('region')
            ->whereNotNull('next_maintenance_date');

        if ($tab === 'overdue') {
            $query->whereDate('next_maintenance_date', '<', $today);
        } elseif ($tab === 'due_this_month') {
            $query->whereBetween('next_maintenance_date', [
                $today->copy()->startOfMonth(),
                $today->copy()->endOfMonth(),
            ]);
        } elseif ($tab === 'upcoming') {
            $query->whereDate('next_maintenance_date', '>', $today->copy()->endOfMonth());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('device_info', 'like', "%{$search}%");
            });
        }

        $records = $query->orderBy('next_maintenance_date')->paginate(15)->withQueryString();

        $overdueCount = ServiceRequest::whereNotNull('next_maintenance_date')
            ->whereDate('next_maintenance_date', '<', $today)
            ->count();

        $thisMonthCount = ServiceRequest::whereNotNull('next_maintenance_date')
            ->whereBetween('next_maintenance_date', [
                $today->copy()->startOfMonth(),
                $today->copy()->endOfMonth(),
            ])
            ->count();

        $upcomingCount = ServiceRequest::whereNotNull('next_maintenance_date')
            ->whereDate('next_maintenance_date', '>', $today->copy()->endOfMonth())
            ->count();

        return view('admin.maintenance.index', compact(
            'records',
            'tab',
            'overdueCount',
            'thisMonthCount',
            'upcomingCount'
        ));
    }

    public function renew(Request $request, ServiceRequest $service)
    {
        $request->validate([
            'months' => 'required|integer|min:1|max:36',
        ]);

        $newDate = Carbon::today()->addMonths((int) $request->months);
        $service->update([
            'next_maintenance_date' => $newDate,
        ]);

        return back()->with('success', "Bakım ve filtre değişim tarihi {$newDate->format('d.m.Y')} olarak yenilendi.");
    }
}
