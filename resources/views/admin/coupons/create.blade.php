<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Yeni İndirim Kuponu Oluştur') }}
            </h2>
            <a href="{{ route('admin.coupons.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-bold">
                <i class="fas fa-arrow-left"></i> Listeye Dön
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-6 sm:p-8">
                
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 text-red-700 rounded-r">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kupon Kodu *</label>
                        <input type="text" name="code" value="{{ old('code') }}" required placeholder="Örn: ISTANBUL100, YAZ2026"
                            class="w-full rounded-xl border-gray-300 font-mono uppercase text-sm font-extrabold focus:border-blue-500 focus:ring-blue-500">
                        <p class="text-[11px] text-gray-400 mt-1">Büyük harf ve rakamlar kullanabilirsiniz.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">İndirim Türü *</label>
                            <select name="type" required class="w-full rounded-xl border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Sabit TL İndirim (Örn: 100 TL)</option>
                                <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>Yüzdelik İndirim (Örn: %10)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">İndirim Miktarı *</label>
                            <input type="number" step="0.01" name="value" value="{{ old('value') }}" required placeholder="Örn: 150 veya 10"
                                class="w-full rounded-xl border-gray-300 text-sm font-bold text-blue-600 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Minimum Sepet Tutarı (TL)</label>
                            <input type="number" step="0.01" name="min_order_amount" value="{{ old('min_order_amount', 0) }}" placeholder="Örn: 1000 (0 ise limitsiz)"
                                class="w-full rounded-xl border-gray-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Toplam Kullanım Limiti</label>
                            <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" placeholder="Örn: 50 (Boşsa sınırsız)"
                                class="w-full rounded-xl border-gray-300 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Son Geçerlilik Tarihi (İsteğe Bağlı)</label>
                        <input type="date" name="expires_at" value="{{ old('expires_at') }}"
                            class="w-full rounded-xl border-gray-300 text-sm">
                    </div>

                    <div class="flex items-center gap-2 pt-2 border-t">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <label for="is_active" class="text-sm font-bold text-gray-700">Kupon hemen aktif olsun</label>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-200">
                            İptal
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow">
                            Kuponu Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
