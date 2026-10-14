<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount(['orders', 'serviceRequests'])->latest()->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $orders = $customer->orders()->latest()->get();
        $services = $customer->serviceRequests()->latest()->get();

        return view('admin.customers.show', compact('customer', 'orders', 'services'));
    }

    /**
     * Müşteri Rehberini Excel (CSV) Formatında Dışa Aktar
     */
    public function exportExcel(Request $request)
    {
        $customers = Customer::withCount(['orders', 'serviceRequests'])->with('region')->latest()->get();
        $fileName = 'Musteri_Rehberi_'.date('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->stream(function () use ($customers) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID',
                'Müşteri Adı',
                'Telefon',
                'E-Posta',
                'İlçe / Bölge',
                'Adres',
                'Toplam Sipariş Sayısı',
                'Toplam Servis Sayısı',
                'Kayıt Tarihi',
            ], ';');

            foreach ($customers as $c) {
                fputcsv($handle, [
                    $c->id,
                    $c->name,
                    $c->phone,
                    $c->email ?: '-',
                    $c->region->name ?? '-',
                    $c->address ?: '-',
                    $c->orders_count,
                    $c->service_requests_count,
                    $c->created_at->format('d.m.Y H:i:s'),
                ], ';');
            }
            fclose($handle);
        }, 200, $headers);
    }
}
