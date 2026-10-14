@php
    $wheelPrizes = [
        [
            'label' => $siteSettings['wheel_slice_1_title'] ?? '500 TL İndirim',
            'code' => $siteSettings['wheel_slice_1_code'] ?? 'ARITMA500',
            'color' => $siteSettings['wheel_slice_1_color'] ?? '#2563eb',
            'short' => '500 TL'
        ],
        [
            'label' => $siteSettings['wheel_slice_2_title'] ?? '1 Yıllık Yedek Filtre Hediye',
            'code' => $siteSettings['wheel_slice_2_code'] ?? 'FILTREHEDIYE',
            'color' => $siteSettings['wheel_slice_2_color'] ?? '#10b981',
            'short' => 'Yedek Filtre'
        ],
        [
            'label' => $siteSettings['wheel_slice_3_title'] ?? '%15 Sepet İndirimi',
            'code' => $siteSettings['wheel_slice_3_code'] ?? 'INDIRIM15',
            'color' => $siteSettings['wheel_slice_3_color'] ?? '#f59e0b',
            'short' => '%15 İndirim'
        ],
        [
            'label' => $siteSettings['wheel_slice_4_title'] ?? 'Ücretsiz Montaj Servisi',
            'code' => $siteSettings['wheel_slice_4_code'] ?? 'UCRETSIZMONTAJ',
            'color' => $siteSettings['wheel_slice_4_color'] ?? '#8b5cf6',
            'short' => 'Ücretsiz Montaj'
        ],
        [
            'label' => $siteSettings['wheel_slice_5_title'] ?? 'Alkali pH+ Filtre Hediye',
            'code' => $siteSettings['wheel_slice_5_code'] ?? 'ALKALIPH',
            'color' => $siteSettings['wheel_slice_5_color'] ?? '#06b6d4',
            'short' => 'Alkali Filtre'
        ],
        [
            'label' => $siteSettings['wheel_slice_6_title'] ?? '300 TL Ekstra İndirim',
            'code' => $siteSettings['wheel_slice_6_code'] ?? 'EKSTRA300',
            'color' => $siteSettings['wheel_slice_6_color'] ?? '#ef4444',
            'short' => '300 TL'
        ],
    ];
@endphp

<div x-data="{
    open: false,
    hasSpun: false,
    isSpinning: false,
    alreadySpun: false,
    copied: false,
    name: '',
    phone: '',
    district: '',
    phoneError: '',
    wonPrize: null,
    couponCode: '',
    rotation: 0,
    prizes: {{ json_encode($wheelPrizes) }},
    init() {
        this.alreadySpun = !!localStorage.getItem('wheel_has_spun');
    },
    closeModal() {
        if (!this.isSpinning) {
            this.open = false;
        }
    },
    spinWheel() {
        if (!this.phone || this.phone.length < 10) {
            this.phoneError = 'Lütfen geçerli bir telefon numarası giriniz.';
            return;
        }
        this.phoneError = '';
        this.isSpinning = true;

        // Rastgele bir ödül seç (Admin tarafından belirlenen 6 ödülden biri)
        const winningIndex = Math.floor(Math.random() * this.prizes.length);
        const prize = this.prizes[winningIndex];

        // 360 / 6 = 60 derece her dilim
        const sliceDegree = 360 / this.prizes.length;
        const targetDegree = 360 - (winningIndex * sliceDegree) - (sliceDegree / 2);
        const totalRotations = 5 * 360; // 5 tam tur
        this.rotation = totalRotations + targetDegree;

        // Lead verisini AJAX ile kaydet
        fetch('{{ route('leads.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                name: this.name,
                phone: this.phone,
                district: this.district,
                source: 'spin_wheel',
                prize_won: prize.label,
                coupon_code: prize.code
            })
        }).then(res => res.json()).catch(err => console.error(err));

        setTimeout(() => {
            this.isSpinning = false;
            this.hasSpun = true;
            this.alreadySpun = true;
            this.wonPrize = prize;
            this.couponCode = prize.code;
            localStorage.setItem('wheel_has_spun', '1');
            localStorage.setItem('wheel_auto_opened', '1');
        }, 4500);
    },
    copyCoupon() {
        navigator.clipboard.writeText(this.couponCode);
        this.copied = true;
        setTimeout(() => this.copied = false, 2500);
    }
}" @open-lucky-wheel.window="if(!alreadySpun) { open = true; hasSpun = false; rotation = 0; }" class="relative z-50">

    <!-- Şans Çarkı Modal Penceresi -->
    <div x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div @click.away="closeModal()"
             class="bg-gradient-to-br from-slate-900 via-indigo-950 to-blue-950 text-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-blue-800/80 relative overflow-hidden">

            <!-- Kapatma Butonu -->
            <button type="button" 
                    @click="closeModal()" 
                    :disabled="isSpinning"
                    aria-label="Pencereyi Kapat"
                    title="Kapat"
                    class="absolute top-4 right-4 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-sm transition-colors z-20">
                <i class="fas fa-times"></i>
            </button>

            <!-- Dekoratif Arka Plan -->
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-amber-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <!-- 1. FORM & ÇARK EKRANI -->
            <div x-show="!hasSpun" class="relative z-10 text-center">
                
                <span class="inline-flex items-center gap-1.5 bg-amber-500/20 text-amber-300 text-[11px] font-black px-3.5 py-1 rounded-full border border-amber-400/30 mb-2">
                    <i class="fas fa-sparkles text-amber-400"></i> ŞANSINI DENE &amp; KAZAN
                </span>

                <h3 class="text-xl sm:text-2xl font-black text-white tracking-tight">
                    İndirim &amp; Hediye Çarkını Çevirin!
                </h3>
                <p class="text-xs text-blue-200/80 mt-1 mb-6">
                    Numaranızı girin, anında sepetinizde geçerli indirim veya hediye kazanın!
                </p>

                <!-- ÇARK GRAFİĞİ -->
                <div class="relative w-56 h-56 sm:w-64 sm:h-64 mx-auto mb-6 flex items-center justify-center">
                    
                    <!-- Çark İğnesi (Pointer) -->
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 z-20 text-amber-400 text-3xl filter drop-shadow-md">
                        <i class="fas fa-caret-down"></i>
                    </div>

                    <!-- Çark Dairesi -->
                    <div class="w-full h-full rounded-full border-4 border-amber-400 shadow-2xl overflow-hidden relative"
                         :style="'transform: rotate(' + rotation + 'deg); transition: transform 4.5s cubic-bezier(0.15, 0.9, 0.25, 1);'">
                        
                        <!-- Dilimler (SVG) - Tüm yazılar yatay, net ve düzgün -->
                        <svg viewBox="0 0 100 100" class="w-full h-full select-none">
                            <defs>
                                <filter id="textGlow" x="-20%" y="-20%" width="140%" height="140%">
                                    <feDropShadow dx="0" dy="1" stdDeviation="0.8" flood-color="#000" flood-opacity="0.6"/>
                                </filter>
                            </defs>

                            <!-- 1. Dilim (0 - 60 deg) Mavi: 500 TL -->
                            <path d="M50 50 L50 0 A50 50 0 0 1 93.3 25 Z" fill="{{ $wheelPrizes[0]['color'] }}" />
                            <text x="66" y="27" fill="#fff" font-size="4.5" font-weight="900" text-anchor="middle" filter="url(#textGlow)">{{ $wheelPrizes[0]['short'] }}</text>

                            <!-- 2. Dilim (60 - 120 deg) Yeşil: Yedek Filtre -->
                            <path d="M50 50 L93.3 25 A50 50 0 0 1 93.3 75 Z" fill="{{ $wheelPrizes[1]['color'] }}" />
                            <text x="76" y="49" fill="#fff" font-size="3.5" font-weight="900" text-anchor="middle" filter="url(#textGlow)">
                                <tspan x="76" dy="0">Yedek</tspan>
                                <tspan x="76" dy="4">Filtre</tspan>
                            </text>

                            <!-- 3. Dilim (120 - 180 deg) Sarı: %15 İndirim -->
                            <path d="M50 50 L93.3 75 A50 50 0 0 1 50 100 Z" fill="{{ $wheelPrizes[2]['color'] }}" />
                            <text x="66" y="78" fill="#fff" font-size="4.2" font-weight="900" text-anchor="middle" filter="url(#textGlow)">{{ $wheelPrizes[2]['short'] }}</text>

                            <!-- 4. Dilim (180 - 240 deg) Mor: Ücretsiz Montaj -->
                            <path d="M50 50 L50 100 A50 50 0 0 1 6.7 75 Z" fill="{{ $wheelPrizes[3]['color'] }}" />
                            <text x="34" y="75" fill="#fff" font-size="3.5" font-weight="900" text-anchor="middle" filter="url(#textGlow)">
                                <tspan x="34" dy="0">Ücretsiz</tspan>
                                <tspan x="34" dy="4">Montaj</tspan>
                            </text>

                            <!-- 5. Dilim (240 - 300 deg) Cyan: Alkali Filtre -->
                            <path d="M50 50 L6.7 75 A50 50 0 0 1 6.7 25 Z" fill="{{ $wheelPrizes[4]['color'] }}" />
                            <text x="24" y="49" fill="#fff" font-size="3.5" font-weight="900" text-anchor="middle" filter="url(#textGlow)">
                                <tspan x="24" dy="0">Alkali</tspan>
                                <tspan x="24" dy="4">Filtre</tspan>
                            </text>

                            <!-- 6. Dilim (300 - 360 deg) Kırmızı: 300 TL -->
                            <path d="M50 50 L6.7 25 A50 50 0 0 1 50 0 Z" fill="{{ $wheelPrizes[5]['color'] }}" />
                            <text x="34" y="27" fill="#fff" font-size="4.5" font-weight="900" text-anchor="middle" filter="url(#textGlow)">{{ $wheelPrizes[5]['short'] }}</text>
                        </svg>

                        <!-- Orta Göbek -->
                        <div class="absolute inset-0 m-auto w-10 h-10 bg-slate-900 rounded-full border-2 border-amber-400 flex items-center justify-center text-amber-400 text-xs shadow-inner">
                            <i class="fas fa-tint"></i>
                        </div>
                    </div>
                </div>

                <!-- FORM ALANLARI (SADECE TELEFON NUMARASI) -->
                <form @submit.prevent="spinWheel()" class="space-y-4 max-w-xs mx-auto text-center">
                    <div>
                        <input type="tel" x-model="phone" placeholder="05XX XXX XX XX" required autofocus
                               :disabled="isSpinning"
                               class="w-full text-sm sm:text-base font-black text-center rounded-2xl border border-blue-400/50 bg-white/10 text-white placeholder-blue-200/50 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 px-4 py-3 shadow-inner tracking-wider">
                        <span x-show="phoneError" x-text="phoneError" class="text-rose-400 text-xs font-bold mt-1.5 block text-center"></span>
                    </div>

                    <button type="submit" :disabled="isSpinning"
                            class="w-full bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 hover:from-amber-600 hover:to-red-600 text-white font-black text-sm py-3.5 rounded-2xl shadow-xl hover:shadow-amber-500/50 transition-all transform active:scale-95 flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fas fa-play" x-show="!isSpinning"></i>
                        <i class="fas fa-spinner fa-spin" x-show="isSpinning"></i>
                        <span x-text="isSpinning ? 'Çark Dönüyor...' : 'Çarkı Çevir &amp; Hediyeni Al!'"></span>
                    </button>
                </form>

            </div>

            <!-- 2. KAZANMA & TEBRİKLER EKRANI -->
            <div x-show="hasSpun" x-transition class="relative z-10 text-center py-4">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 text-slate-950 text-3xl flex items-center justify-center mx-auto mb-4 shadow-xl animate-bounce">
                    <i class="fas fa-trophy"></i>
                </div>

                <h3 class="text-2xl sm:text-3xl font-black text-white">
                    Tebrikler, Kazandınız!
                </h3>

                <div class="my-5 p-4 bg-white/10 rounded-2xl border border-amber-400/40">
                    <div class="text-xs text-amber-300 font-bold uppercase tracking-wider mb-1">KAZANDIĞINIZ HEDİYE:</div>
                    <div class="text-xl font-black text-white" x-text="wonPrize?.label"></div>
                    <div class="mt-3 pt-3 border-t border-white/10 flex items-center justify-center gap-3">
                        <span class="text-xs text-blue-200">İndirim Kodunuz:</span>
                        <span class="bg-amber-400 text-slate-950 font-mono font-black text-base px-3.5 py-1 rounded-lg select-all" x-text="couponCode"></span>
                        <button type="button" @click="copyCoupon()" class="bg-white/20 hover:bg-white/30 text-white text-xs font-bold px-2.5 py-1.5 rounded-lg transition-colors">
                            <i class="fas fa-copy mr-1"></i> <span x-text="copied ? 'Kopyalandı!' : 'Kopyala'"></span>
                        </button>
                    </div>
                </div>

                <p class="text-xs text-blue-200/90 mb-6 leading-relaxed">
                    Kupon kodunuz adınıza tanımlandı! Hem sepette ödeme yaparken kullanabilir hem de doğrudan WhatsApp üzerinden sipariş verebilirsiniz.
                </p>

                <div class="space-y-3">
                    <a :href="'https://wa.me/{{ $siteSettings['whatsapp'] ?? '905555555555' }}?text=' + encodeURIComponent('Merhaba, web sitenizdeki Şans Çarkından ' + wonPrize?.label + ' (' + couponCode + ') kazandım! Randevulu montaj ve cihaz siparişi için detayları görüşmek istiyorum.')"
                       target="_blank"
                       class="w-full inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-black text-sm py-3.5 rounded-xl shadow-lg transition-all transform active:scale-95">
                        <i class="fab fa-whatsapp text-lg"></i> WhatsApp ile İndirimi Kullan
                    </a>
                    <a href="{{ route('products.index') }}"
                       class="w-full inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-2.5 rounded-xl transition-colors">
                        <i class="fas fa-shopping-bag"></i> Ürünleri İncele &amp; Sepete Git
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
