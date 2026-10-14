<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Region;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function create()
    {
        $regions = Region::where('is_active', true)->orderBy('name')->get();

        return view('service-request', compact('regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'region_id' => 'required|exists:regions,id',
            'address' => 'required|string',
            'service_type' => 'required|string',
            'preferred_date' => 'required|date|after_or_equal:tomorrow',
            'preferred_time' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        // Handle Customer (Find or Create)
        $customer = Customer::firstOrCreate(
            ['phone' => $request->customer_phone],
            [
                'name' => $request->customer_name,
                'email' => null,
                'address' => $request->address,
                'region_id' => $request->region_id,
            ]
        );

        $data = $request->except('image');
        $data['customer_id'] = $customer->id;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('service_requests', 'public');
        }

        ServiceRequest::create($data);

        return redirect()->route('service.create')->with('success', 'Servis talebiniz başarıyla alındı. Müşteri temsilcilerimiz en kısa sürede sizinle iletişime geçecektir.');
    }
}
