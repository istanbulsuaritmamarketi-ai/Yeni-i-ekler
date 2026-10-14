<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::latest()->get();

        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'comment' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Testimonial::create([
            'customer_name' => $request->customer_name,
            'comment' => $request->comment,
            'rating' => $request->rating,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.testimonials.index')->with('success', 'Yorum eklendi.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'comment' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $testimonial->update([
            'customer_name' => $request->customer_name,
            'comment' => $request->comment,
            'rating' => $request->rating,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.testimonials.index')->with('success', 'Yorum güncellendi.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('success', 'Yorum silindi.');
    }
}
