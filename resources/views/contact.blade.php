<x-frontend-layout title="İletişim &amp; Çiçek Sipariş Hattı — Tazecicekler.com" description="Tazecicekler.com müşteri hizmetleri, WhatsApp sipariş ve danışma hattı. Sorularınız, özel aranjman ve kurumsal talepleriniz için bize ulaşın.">
    <!-- Header (Tek Renk & Sade) -->
    <div class="bg-rose-700 text-white py-8 sm:py-12 px-4 sm:px-6 lg:px-8 border-b border-rose-800">
        <div class="max-w-[1536px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-12 text-center">
            <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight mb-2">
                Bize Ulaşın
            </h1>
            <p class="text-xs sm:text-sm text-rose-100 max-w-xl mx-auto leading-relaxed">
                Sipariş durumunuz, teslimat detayları veya özel tasarım çiçek talepleriniz için ekibimiz daima yanınızda.
            </p>
        </div>
    </div>

    <div class="bg-slate-50 py-12 sm:py-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-white rounded-3xl shadow-sm border border-rose-100 overflow-hidden">
                <!-- İletişim Bilgileri -->
                <div class="p-8 sm:p-10 bg-rose-50/60 border-b md:border-b-0 md:border-r border-rose-100 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-black uppercase tracking-wider text-rose-600 block mb-1">Tazecicekler.com Atölye &amp; Merkez</span>
                        <h2 class="text-2xl font-black text-slate-900 mb-6">İletişim Bilgileri</h2>

                        <ul class="space-y-5 mb-8 text-sm">
                            <li class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <strong class="block text-slate-800 font-extrabold text-xs">Atölye &amp; Sevkiyat Merkezi:</strong>
                                    <span class="text-slate-600 text-xs">{{ $siteSettings['address'] ?? 'Acıbadem Mah. Çiçek Sok. No:14 Kadıköy, İstanbul' }}</span>
                                </div>
                            </li>
                            <li class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div>
                                    <strong class="block text-slate-800 font-extrabold text-xs">Müşteri Destek Hattı:</strong>
                                    <a href="tel:{{ $siteSettings['phone'] ?? '0850 300 34 34' }}" class="text-rose-600 hover:text-rose-700 font-bold text-xs">{{ $siteSettings['phone'] ?? '0850 300 34 34' }}</a>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Haftanın 7 günü 08:30 - 22:00</div>
                                </div>
                            </li>
                            <li class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                    <i class="fab fa-whatsapp text-lg"></i>
                                </div>
                                <div>
                                    <strong class="block text-slate-800 font-extrabold text-xs">WhatsApp Çiçek Danışmanı:</strong>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings['whatsapp'] ?? '905013798236') }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 font-bold text-xs">{{ $siteSettings['whatsapp'] ?? '0501 379 82 36' }}</a>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Anında fotoğraf ve sipariş desteği</div>
                                </div>
                            </li>
                            <li class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <strong class="block text-slate-800 font-extrabold text-xs">E-Posta:</strong>
                                    <a href="mailto:{{ $siteSettings['email'] ?? 'destek@tazecicekler.com' }}" class="text-rose-600 hover:text-rose-700 font-bold text-xs">{{ $siteSettings['email'] ?? 'destek@tazecicekler.com' }}</a>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Kurye Sözü -->
                    <div class="p-4 rounded-2xl bg-white border border-rose-200/80 shadow-2xs">
                        <div class="flex items-center gap-2 text-rose-600 font-black text-xs mb-1">
                            <i class="fas fa-shipping-fast"></i> Türkiye Geneli Hızlı &amp; Güvenli Teslimat
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            81 il ve 974 ilçeye çiçeklerinizi ezilmeden, su tüplerinde ve taptaze teslim ediyoruz.
                        </p>
                    </div>
                </div>

                <!-- İletişim Formu -->
                <div class="p-8 sm:p-10">
                    <span class="text-xs font-black uppercase tracking-wider text-rose-600 block mb-1">Hızlı İletişim Formu</span>
                    <h2 class="text-2xl font-black text-slate-900 mb-6">Bize Mesaj Gönderin</h2>
                    
                    @if(session('success'))
                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl mb-6 text-xs font-bold flex items-center gap-2">
                            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-slate-700 text-xs font-black mb-1.5" for="name">Adınız Soyadınız *</label>
                            <input class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500 @error('name') border-rose-500 @enderror" id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Örn: Ayşe Yılmaz" required>
                            @error('name')
                                <p class="text-rose-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-slate-700 text-xs font-black mb-1.5" for="phone">Telefon Numaranız *</label>
                            <input class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500 @error('phone') border-rose-500 @enderror" id="phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="05XX XXX XX XX" required>
                            @error('phone')
                                <p class="text-rose-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-slate-700 text-xs font-black mb-1.5" for="email">E-posta Adresiniz</label>
                            <input class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500" id="email" type="email" name="email" value="{{ old('email') }}" placeholder="adiniz@example.com">
                        </div>

                        <div>
                            <label class="block text-slate-700 text-xs font-black mb-1.5" for="message">Mesajınız *</label>
                            <textarea class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:border-rose-500 h-28 resize-none @error('message') border-rose-500 @enderror" id="message" name="message" placeholder="Siparişiniz veya özel tasarım talebiniz hakkında not bırakın..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-rose-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button class="w-full bg-rose-600 hover:bg-rose-700 text-white font-black py-3 px-4 rounded-xl shadow-md hover:shadow-lg transition-all text-xs flex items-center justify-center gap-2 cursor-pointer active:scale-98" type="submit">
                            <i class="fas fa-paper-plane"></i> Mesajı Gönder
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-frontend-layout>
