<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                    <i class="fas fa-comments"></i>
                </span>
                <div>
                    <h2 class="font-black text-sm text-slate-900 leading-none">
                        {{ __('Müşteri Yorumları & Google Haritalar Entegrasyonu') }}
                    </h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Google Maps ve site üzerinden gelen müşteri değerlendirmeleri</p>
                </div>
            </div>

            @if($pendingCount > 0)
                <span class="bg-amber-50 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-xl border border-amber-200 flex items-center gap-1.5 self-start sm:self-auto shadow-2xs">
                    <i class="fas fa-bell text-amber-500"></i>
                    <span>{{ $pendingCount }} Onay Bekleyen Yorum</span>
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-6 space-y-6" x-data="{ manualModal: false }">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 shadow-xs flex items-center justify-between gap-3 text-xs font-bold">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <!-- 1. GOOGLE HARİTALAR (GOOGLE MAPS) ENTEGRASYON KARTI -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                    <div class="space-y-1 max-w-xl">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-800 text-xs font-bold">
                            <i class="fab fa-google text-blue-600"></i>
                            <span>Google Haritalar Canlı Yorum Motoru</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">
                            Google Maps Bağlantısı ile Otomatik Yorum Çekme
                        </h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Google Haritalar işletme bağlantınızı veya Place ID bilginizi ekleyin. Sistem yorumları otomatik olarak tarar, onaylar ve ana sayfadaki müşteri deneyimleri vitrinine aktarır.
                        </p>
                    </div>

                    <!-- Canlı Google Skor Rozeti -->
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200/80 shrink-0">
                        <div class="w-12 h-12 rounded-2xl bg-white shadow-xs border border-slate-200 flex items-center justify-center text-2xl text-blue-600">
                            <i class="fab fa-google"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-1 text-amber-500 text-sm">
                                <span class="text-slate-900 font-black text-lg mr-1">{{ $settings['google_reviews_rating'] ?? '4.9' }}</span>
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                            <span class="text-[11px] font-bold text-slate-500 block">
                                {{ $settings['google_reviews_total_count'] ?? '486' }}+ Doğrulanmış Google Yorumu
                            </span>
                            <span class="text-[10px] text-emerald-600 font-extrabold flex items-center gap-1 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $googleReviewsCount }} adet sisteme bağlı
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Google Ayar & Senkronizasyon Formu -->
                <form action="{{ route('admin.reviews.syncGoogle') }}" method="POST" class="mt-6 pt-6 border-t border-slate-100">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Google Haritalar Bağlantı Linki veya İşletme Adı
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-map-marker-alt text-xs text-blue-600"></i>
                                </div>
                                <input type="text" name="google_maps_url" 
                                       value="{{ $settings['google_maps_url'] ?? 'https://maps.google.com/?q=Tazecicekler+Kadikoy+Istanbul' }}" 
                                       placeholder="Örn: https://maps.app.goo.gl/... veya Taze Çiçekler Kadıköy" 
                                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs font-bold text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Google Place ID <span class="text-slate-400 font-normal">(Opsiyonel)</span>
                            </label>
                            <input type="text" name="google_maps_place_id" 
                                   value="{{ $settings['google_maps_place_id'] ?? 'ChIJv8X1T3pkyhQRQ-CICEK' }}" 
                                   placeholder="ChIJ..." 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white text-xs font-bold text-slate-800 focus:outline-none focus:border-blue-500 shadow-2xs">
                        </div>
                    </div>

                    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                            <i class="fas fa-check-circle text-emerald-500"></i>
                            <span>Bağlantıyı aratıp 5 yıldızlı müşteri yorumlarını ve Google skorunu tek tıkla otomatik çeker.</span>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button type="button" @click="manualModal = true"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition">
                                <i class="fas fa-plus text-xs"></i>
                                <span>Manuel Google Yorumu Ekle</span>
                            </button>

                            <button type="submit" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-extrabold shadow-sm transition active:scale-95">
                                <i class="fab fa-google text-amber-400 text-xs"></i>
                                <span>Google'dan Otomatik Çek &amp; Güncelle</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Manuel Google Yorum Ekleme Modalı -->
            <div x-show="manualModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 p-0">
                    <div x-show="manualModal" x-transition.opacity @click="manualModal = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>
                    <div x-show="manualModal" x-transition class="relative bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 z-10">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                            <div class="flex items-center gap-2">
                                <i class="fab fa-google text-blue-600 text-base"></i>
                                <h3 class="text-sm font-black text-slate-900">Manuel Google Harita Yorumu Ekle</h3>
                            </div>
                            <button type="button" @click="manualModal = false" class="text-slate-400 hover:text-slate-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <form action="{{ route('admin.reviews.storeGoogle') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Müşteri Adı Soyadı</label>
                                <input type="text" name="name" required placeholder="Örn: Mehmet Can" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Teslimat İlçesi / Bölge</label>
                                <input type="text" name="location" placeholder="Örn: Kadıköy / İstanbul" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Puan</label>
                                <select name="rating" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold focus:border-blue-500">
                                    <option value="5" selected>5 Yıldız (Mükemmel)</option>
                                    <option value="4">4 Yıldız (Çok İyi)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Google Yorum Metni</label>
                                <textarea name="comment" rows="3" required placeholder="Müşterinin harita yorumunu buraya yapıştırın..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-blue-500"></textarea>
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="manualModal = false" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700">İptal</button>
                                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-extrabold bg-blue-600 hover:bg-blue-700 text-white shadow-xs">Yorumu Ekle &amp; Yayına Al</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 2. FİLTRELEME SEKMELERİ -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.reviews.index') }}" 
                   class="px-4 py-2 text-xs font-bold rounded-xl transition {{ !request('status') && !request('source') ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                    <span>Tüm Yorumlar</span>
                    <span class="ml-1.5 text-[10px] opacity-75">({{ $reviews->total() }})</span>
                </a>

                <a href="{{ route('admin.reviews.index', ['source' => 'google']) }}" 
                   class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-1.5 {{ request('source') === 'google' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-blue-700 border border-blue-200 hover:bg-blue-50' }}">
                    <i class="fab fa-google text-xs"></i>
                    <span>Google Haritalar ({{ $googleReviewsCount }})</span>
                </a>

                <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" 
                   class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-1.5 {{ request('status') === 'pending' ? 'bg-amber-500 text-white shadow-xs' : 'bg-white text-amber-800 border border-amber-200 hover:bg-amber-50' }}">
                    <i class="fas fa-clock text-xs"></i>
                    <span>Onay Bekleyenler ({{ $pendingCount }})</span>
                </a>

                <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" 
                   class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-1.5 {{ request('status') === 'approved' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-emerald-800 border border-emerald-200 hover:bg-emerald-50' }}">
                    <i class="fas fa-check text-xs"></i>
                    <span>Onaylanmışlar</span>
                </a>
            </div>

            <!-- 3. YORUMLAR LİSTESİ TABLOSU -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3.5 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Kaynak</th>
                                <th class="px-6 py-3.5 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Müşteri &amp; Bölge</th>
                                <th class="px-6 py-3.5 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Puan</th>
                                <th class="px-6 py-3.5 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Yorum Detayı</th>
                                <th class="px-6 py-3.5 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Durum</th>
                                <th class="px-6 py-3.5 text-left text-xs font-black text-slate-500 uppercase tracking-wider">Tarih</th>
                                <th class="px-6 py-3.5 text-right text-xs font-black text-slate-500 uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($reviews as $rev)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(($rev->source ?? 'site') === 'google')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-black bg-blue-50 text-blue-700 border border-blue-200">
                                                <i class="fab fa-google text-blue-600"></i> Google Maps
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">
                                                <i class="fas fa-shopping-bag text-slate-500"></i> Site Yorumu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-xs text-slate-900">{{ $rev->name }}</div>
                                        <div class="text-[11px] text-slate-400">
                                            {{ $rev->location ?: ($rev->product ? $rev->product->name : 'İstanbul') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex text-amber-400 text-xs gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="{{ $i <= $rev->rating ? 'fas' : 'far' }} fa-star"></i>
                                            @endfor
                                        </div>
                                        <span class="text-[10px] font-black text-slate-600">{{ $rev->rating }}/5</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-xs text-slate-700 max-w-sm line-clamp-2 leading-relaxed">{{ $rev->comment }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($rev->is_approved)
                                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Yayında
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-lg bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                                Onay Bekliyor
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-[11px] text-slate-400 font-medium">
                                        {{ $rev->created_at->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold space-x-2">
                                        <form action="{{ route('admin.reviews.toggle', $rev) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $rev->is_approved ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }}">
                                                <i class="fas {{ $rev->is_approved ? 'fa-eye-slash' : 'fa-check' }}"></i> {{ $rev->is_approved ? 'Kaldır' : 'Onayla' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.reviews.destroy', $rev) }}" method="POST" class="inline" onsubmit="return confirm('Bu yorumu silmek istediğinize emin misiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 px-3 py-1.5 rounded-xl text-xs font-bold transition">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                        <i class="fas fa-comments text-4xl text-slate-300 mb-3 block"></i>
                                        <p class="font-bold text-xs">Henüz gösterilecek müşteri yorumu bulunamadı.</p>
                                        <p class="text-[11px] text-slate-400 mt-1">Yukarıdaki Google butonunu kullanarak Google Haritalar yorumlarınızı anında çekebilirsiniz.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($reviews->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
