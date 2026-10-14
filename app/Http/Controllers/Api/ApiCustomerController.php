<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class ApiCustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Customer::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('device_brand', 'like', "%{$search}%")
                    ->orWhere('device_model', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->get()->map(function ($c) {
            return $this->formatCustomer($c);
        });

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
        ]);

        $fullName = trim($request->name.' '.($request->surname ?? ''));
        $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);

        $customer = Customer::where('phone', $request->phone)
            ->orWhere('phone', $cleanPhone)
            ->first();

        if ($customer) {
            $customer->update([
                'name' => $fullName,
                'address' => $request->address ?? $customer->address,
                'device_brand' => $request->device_brand ?? $customer->device_brand,
                'device_model' => $request->device_model ?? $customer->device_model,
                'tds_value' => $request->tds_value ?? $customer->tds_value,
            ]);
        } else {
            $customer = Customer::create([
                'name' => $fullName,
                'phone' => $request->phone,
                'address' => $request->address,
                'device_brand' => $request->device_brand,
                'device_model' => $request->device_model,
                'tds_value' => $request->tds_value,
                'lead_source' => 'Mobil Teknik Servis',
                'status' => 'Aktif',
            ]);
        }

        return response()->json($this->formatCustomer($customer), 201);
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);

        return response()->json($this->formatCustomer($customer));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $fullName = $customer->name;
        if ($request->filled('name')) {
            $fullName = trim($request->name.' '.($request->surname ?? ''));
        }

        $customer->update([
            'name' => $fullName,
            'phone' => $request->phone ?? $customer->phone,
            'address' => $request->address ?? $customer->address,
            'device_brand' => $request->device_brand ?? $customer->device_brand,
            'device_model' => $request->device_model ?? $customer->device_model,
            'tds_value' => $request->tds_value ?? $customer->tds_value,
        ]);

        return response()->json($this->formatCustomer($customer));
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(['message' => 'Müşteri başarıyla silindi']);
    }

    private function formatCustomer(Customer $c): array
    {
        $parts = explode(' ', trim($c->name ?? ''));
        $surname = count($parts) > 1 ? array_pop($parts) : '';
        $firstName = count($parts) > 0 ? implode(' ', $parts) : ($c->name ?? '');

        return [
            'id' => (string) $c->id,
            'name' => $firstName ?: ($c->name ?? 'Müşteri'),
            'surname' => $surname ?: '-',
            'phone' => $c->phone ?? '',
            'address' => $c->address ?? '',
            'device_brand' => $c->device_brand ?? '',
            'device_model' => $c->device_model ?? '',
            'tds_value' => $c->tds_value !== null ? (float) $c->tds_value : null,
            'created_at' => $c->created_at?->toIso8601String(),
        ];
    }
}
