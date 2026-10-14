<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('sort_order')->get();

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'sort_order' => 'integer',
        ]);

        Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'SSS eklendi.');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'sort_order' => 'integer',
        ]);

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'SSS güncellendi.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('success', 'SSS silindi.');
    }
}
