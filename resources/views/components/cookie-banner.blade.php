<div x-data="{
    open: false,
    showManage: false,
    analytics: true,
    marketing: true,
    init() {
        if (!localStorage.getItem('kvkk_consent_token')) {
            const showBanner = () => {
                if (!this.open) {
                    this.open = true;
                    window.removeEventListener('scroll', showBanner);
                    window.removeEventListener('touchstart', showBanner);
                    window.removeEventListener('mousemove', showBanner);
                }
            };
            window.addEventListener('scroll', showBanner, { passive: true, once: true });
            window.addEventListener('touchstart', showBanner, { passive: true, once: true });
            window.addEventListener('mousemove', showBanner, { passive: true, once: true });
            setTimeout(showBanner, 5000);
        }
    },
    generateToken() {
        return 'cst_' + Date.now().toString(36) + '_' + Math.random().toString(36).substring(2, 10);
    },
    saveConsent(type, analyticsVal, marketingVal) {
        const token = this.generateToken();
        localStorage.setItem('kvkk_consent_token', token);
        localStorage.setItem('kvkk_consent_type', type);
        this.open = false;

        // AJAX ile yasal ispat kütüğüne kaydet
        fetch('{{ route('cookieConsent.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                consent_token: token,
                consent_type: type,
                necessary: true,
                analytics: analyticsVal,
                marketing: marketingVal
            })
        }).then(res => res.json()).catch(err => console.error(err));
    },
    acceptAll() {
        this.saveConsent('all', true, true);
    },
    rejectAll() {
        this.saveConsent('rejected', false, false);
    },
    saveCustom() {
        this.saveConsent('custom', this.analytics, this.marketing);
    }
}" x-show="open" x-cloak class="relative z-50">

    <!-- KVKK ÇEREZ BİLDİRİMİ -->
    <div class="fixed bottom-3 left-3 right-3 sm:left-auto sm:right-5 sm:bottom-5 sm:max-w-md z-50 p-3 sm:p-3.5 bg-slate-950/95 backdrop-blur-md text-white border border-slate-800 rounded-2xl shadow-2xl transition-all"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4">
        
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 text-left">
                <i class="fas fa-shield-alt text-cyan-400 text-base flex-shrink-0"></i>
                <p class="text-[11px] text-gray-300 leading-snug">
                    Sitemizde yasal çerezler kullanılmaktadır. 
                    <a href="{{ route('page.show', 'gizlilik-politikasi') }}" class="text-cyan-400 underline hover:text-cyan-300 font-bold">Çerez Politikası</a>
                </p>
            </div>

            <!-- Kompakt Butonlar -->
            <div class="flex items-center gap-1.5 flex-shrink-0">
                <button type="button" @click="rejectAll()"
                        class="px-2.5 py-1.5 rounded-lg bg-white/5 hover:bg-white/15 text-gray-400 hover:text-white text-[11px] font-bold transition-colors">
                    Reddet
                </button>
                <button type="button" @click="acceptAll()"
                        class="px-3.5 py-1.5 rounded-lg bg-gradient-to-r from-cyan-400 to-blue-500 hover:from-cyan-300 hover:to-blue-400 text-gray-950 font-black text-[11px] shadow transition-all transform active:scale-95">
                    Kabul Et
                </button>
            </div>
        </div>
    </div>

</div>
