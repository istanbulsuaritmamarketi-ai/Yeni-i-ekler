<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('İndirim Kuponları & Promosyonlar') }}
            </h2>
            <a href="{{ route('admin.coupons.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl text-sm flex items-center gap-2 shadow-sm">
                <i class="fas fa-plus"></i> Yeni Kupon Oluştur
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 text-green-700 rounded-r shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kupon Kodu</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">İndirim Tutarı</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Min. Sepet Tutarı</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kullanım</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Son Geçerlilik</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Durum</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($coupons as $coupon)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-blue-50 text-blue-700 font-mono font-extrabold text-sm border border-blue-200">
                                                <i class="fas fa-ticket-alt"></i> {{ $coupon->code }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-sm text-gray-900">
                                            @if($coupon->type === 'percent')
                                                %{{ number_format($coupon->value, 0) }} İndirim
                                            @else
                                                {{ number_format($coupon->value, 0) }} TL İndirim
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 font-medium">
                                            {{ $coupon->min_order_amount > 0 ? number_format($coupon->min_order_amount, 0) . ' TL' : 'Limit Yok' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                            <span class="font-bold text-gray-900">{{ $coupon->used_count }}</span> / {{ $coupon->usage_limit ? $coupon->usage_limit . ' kez' : 'Sınırsız' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                            {{ $coupon->expires_at ? $coupon->expires_at->format('d.m.Y') : 'Süresiz' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($coupon->is_active && (!$coupon->expires_at || !$coupon->expires_at->isPast()))
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                                    Pasif / Süresi Dolmuş
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold space-x-2">
                                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg">
                                                <i class="fas fa-edit"></i> Düzenle
                                            </a>
                                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline" onsubmit="return confirm('Bu kuponu silmek istediğinize emin misiniz?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg">
                                                    <i class="fas fa-trash"></i> Sil
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">
                                            <i class="fas fa-tags text-4xl text-gray-300 mb-3"></i>
                                            <p class="font-medium">Henüz oluşturulmuş kupon veya kampanya yok.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $coupons->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
