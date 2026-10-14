<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-gray-800 leading-tight flex items-center gap-2.5">
                    <i class="fas fa-bullseye text-red-500"></i> Aday Müşteri &amp; Lead Havuzu (Şans Çarkı / Talepler)
                </h2>
                <p class="text-xs text-gray-500 mt-1">Sitede şans çarkı çeviren veya form doldurarak numara bırakan potansiyel müşteriler</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.leads.export') }}" 
                   class="bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs px-4 py-2 rounded-2xl shadow-sm transition-all flex items-center gap-2">
                    <i class="fas fa-file-excel"></i> Numaraları Excel'e Aktar
                </a>
                <span class="bg-red-100 text-red-700 text-xs font-black px-3.5 py-2 rounded-2xl flex items-center gap-1.5 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                    {{ $stats['new'] }} Yeni Aday
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- 📊 İSTATİSTİK KARTLARI -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-gray-400">Toplam Aday</div>
                        <div class="text-2xl font-black text-gray-900">{{ $stats['total'] }}</div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                        <i class="fas fa-phone-volume"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-amber-600 font-extrabold">Yeni (Bekleyen)</div>
                        <div class="text-2xl font-black text-amber-600">{{ $stats['new'] }}</div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-gray-400">Görüşüldü</div>
                        <div class="text-2xl font-black text-gray-900">{{ $stats['contacted'] }}</div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-emerald-600 font-extrabold">Satış Yapıldı</div>
                        <div class="text-2xl font-black text-emerald-600">{{ $stats['converted'] }}</div>
                    </div>
                </div>
            </div>

            <!-- 🔍 FİLTRELEME & ARAMA FORMU -->
            <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm">
                <form method="GET" action="{{ route('admin.leads.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div class="sm:col-span-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="İsim, telefon, ilçe veya kupon kodu ara..."
                               class="w-full text-xs font-medium rounded-2xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 px-4 py-2.5">
                    </div>
                    <div>
                        <select name="status" class="w-full text-xs font-bold rounded-2xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 py-2.5">
                            <option value="">Tüm Durumlar</option>
                            <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>🟡 Yeni (Aranacak)</option>
                            <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>🟣 İletişime Geçildi</option>
                            <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>🟢 Satış Yapıldı</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>⚪ İptal / İlgilenmiyor</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-2xl py-2.5 shadow transition-colors">
                            <i class="fas fa-search mr-1"></i> Filtrele
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('admin.leads.index') }}" class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-2xl text-xs flex items-center justify-center font-bold">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- 📋 LEAD TABLOSU -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-black text-gray-500 uppercase tracking-wider">
                                <th class="p-4">Müşteri / İletişim</th>
                                <th class="p-4">Kazanılan Ödül / Kupon</th>
                                <th class="p-4">Kaynak &amp; Tarih</th>
                                <th class="p-4">Durum</th>
                                <th class="p-4 text-right">Aksiyonlar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            @forelse($leads as $lead)
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $lead->phone);
                                    if(str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '90' . substr($cleanPhone, 1);
                                    } elseif(!str_starts_with($cleanPhone, '90')) {
                                        $cleanPhone = '90' . $cleanPhone;
                                    }

                                    $leadName = $lead->name ?: 'Değerli Müşterimiz';
                                    $prize = $lead->prize_won ?: 'Özel İndirim Kuponu';
                                    $code = $lead->coupon_code ? ' (Kod: ' . $lead->coupon_code . ')' : '';

                                    $wpMsg = "Merhaba " . $leadName . ",\n\n"
                                           . config('app.name', 'Müşteri Hizmetleri') . " ekibinden ulaşıyorum.\n"
                                           . "Sitemiz üzerinden oluşturduğunuz talep ve kazandığınız *" . $prize . "*" . $code . " için ulaşıyoruz! 🎉\n\n"
                                           . "Size en uygun seçenek ve teklifleri görüşmek için müsait misiniz?\n\n"
                                           . "📞 İletişim Hattı: " . ($siteSettings['phone'] ?? '0850 555 55 55') . "\n"
                                           . "🌐 " . config('app.url', url('/'));

                                    $wpUrl = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($wpMsg);
                                @endphp
                                <tr class="hover:bg-blue-50/30 transition-colors {{ $lead->status == 'new' ? 'bg-amber-50/20 font-medium' : '' }}">
                                    <!-- Müşteri & Telefon -->
                                    <td class="p-4">
                                        <div class="font-extrabold text-gray-900 text-sm flex items-center gap-1.5">
                                            <i class="fas fa-user-circle text-gray-400"></i>
                                            {{ $lead->name ?: 'İsim Belirtilmedi' }}
                                        </div>
                                        <div class="font-mono font-bold text-blue-600 mt-1 flex items-center gap-1">
                                            <i class="fas fa-phone-alt text-[10px]"></i>
                                            <a href="tel:{{ $lead->phone }}" class="hover:underline">{{ $lead->phone }}</a>
                                        </div>
                                        @if($lead->district)
                                            <span class="inline-block text-[10px] text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md mt-1">
                                                📍 {{ $lead->district }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Ödül & Kupon -->
                                    <td class="p-4">
                                        <div class="inline-flex items-center gap-1.5 bg-gradient-to-r from-amber-500/15 to-orange-500/15 text-amber-900 px-3 py-1 rounded-xl text-xs font-black border border-amber-200/60">
                                            <i class="fas fa-gift text-amber-600"></i>
                                            {{ $lead->prize_won ?: 'Genel Teklif Talebi' }}
                                        </div>
                                        @if($lead->coupon_code)
                                            <div class="font-mono text-[11px] text-gray-600 mt-1">
                                                Kupon: <strong class="text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded">{{ $lead->coupon_code }}</strong>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Kaynak & Tarih -->
                                    <td class="p-4 text-gray-500 text-[11px]">
                                        <span class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 px-2 py-0.5 rounded-md font-bold text-[10px]">
                                            @if($lead->source == 'spin_wheel')
                                                🎡 Şans Çarkı
                                            @elseif($lead->source == 'water_test')
                                                💧 Su Testi Talebi
                                            @else
                                                📄 {{ $lead->source }}
                                            @endif
                                        </span>
                                        <div class="mt-1 font-medium text-gray-400">
                                            {{ $lead->created_at->format('d.m.Y H:i') }}
                                            <span class="text-[10px]">({{ $lead->created_at->diffForHumans() }})</span>
                                        </div>
                                    </td>

                                    <!-- Durum -->
                                    <td class="p-4">
                                        <form method="POST" action="{{ route('admin.leads.update', $lead) }}" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" onchange="this.form.submit()"
                                                    class="text-xs font-black rounded-xl border-gray-200 py-1 px-2.5 focus:ring-blue-500 focus:border-blue-500 shadow-sm
                                                    {{ $lead->status == 'new' ? 'bg-amber-100 text-amber-800' : '' }}
                                                    {{ $lead->status == 'contacted' ? 'bg-purple-100 text-purple-800' : '' }}
                                                    {{ $lead->status == 'converted' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                    {{ $lead->status == 'cancelled' ? 'bg-gray-100 text-gray-600' : '' }}">
                                                <option value="new" {{ $lead->status == 'new' ? 'selected' : '' }}>🟡 Yeni</option>
                                                <option value="contacted" {{ $lead->status == 'contacted' ? 'selected' : '' }}>🟣 İletişime Geçildi</option>
                                                <option value="converted" {{ $lead->status == 'converted' ? 'selected' : '' }}>🟢 Satış Yapıldı</option>
                                                <option value="cancelled" {{ $lead->status == 'cancelled' ? 'selected' : '' }}>⚪ İptal</option>
                                            </select>
                                        </form>
                                    </td>

                                    <!-- Aksiyonlar -->
                                    <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                                        <a href="{{ $wpUrl }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black px-3.5 py-1.5 rounded-xl text-xs shadow-sm transition-all transform active:scale-95">
                                            <i class="fab fa-whatsapp text-sm"></i> WhatsApp'tan Yaz
                                        </a>

                                        <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}" class="inline-block" onsubmit="return confirm('Bu kaydı silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition-colors" title="Sil">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-400">
                                        <i class="fas fa-bullseye text-4xl mb-3 text-gray-300 block"></i>
                                        Henüz aday müşteri kaydı bulunmuyor. Şans çarkı veya formlardan gelen numaralar burada listelenecektir.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($leads->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $leads->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
