<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('district', 'like', "%{$s}%")
                    ->orWhere('coupon_code', 'like', "%{$s}%");
            });
        }

        $leads = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Lead::count(),
            'new' => Lead::where('status', 'new')->count(),
            'contacted' => Lead::where('status', 'contacted')->count(),
            'converted' => Lead::where('status', 'converted')->count(),
        ];

        return view('admin.leads.index', compact('leads', 'stats'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,converted,cancelled',
            'notes' => 'nullable|string',
        ]);

        $lead->update($validated);

        return back()->with('success', 'Müşteri durumu başarıyla güncellendi.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return back()->with('success', 'Kayıt silindi.');
    }

    /**
     * Müşteri Adaylarını (Leads) Excel (CSV) Formatında Dışa Aktar
     */
    public function exportExcel(Request $request)
    {
        $leads = Lead::latest()->get();
        $fileName = 'Musteri_Adaylari_Leads_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->stream(function () use ($leads) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID',
                'Müşteri Adı',
                'Telefon Numarası',
                'İlçe',
                'Kaynak',
                'Kazanılan Ödül / Tercih',
                'Kupon Kodu',
                'Durum',
                'Admin Notları',
                'Kayıt Tarihi',
            ], ';');

            foreach ($leads as $lead) {
                fputcsv($handle, [
                    $lead->id,
                    $lead->name ?: 'İsimsiz Ziyaretçi',
                    $lead->phone,
                    $lead->district ?: 'İstanbul',
                    $lead->source === 'spin_wheel' ? 'Şans Çarkı' : ($lead->source === 'quiz_wizard' ? 'Cihaz Bulucu Sihirbaz' : $lead->source),
                    $lead->prize_won ?: '-',
                    $lead->coupon_code ?: '-',
                    $lead->status === 'new' ? 'Yeni' : ($lead->status === 'contacted' ? 'Görüşüldü' : ($lead->status === 'converted' ? 'Satışa Döndü' : 'İptal')),
                    $lead->notes ?: '',
                    $lead->created_at->format('d.m.Y H:i:s'),
                ], ';');
            }
            fclose($handle);
        }, 200, $headers);
    }
}
