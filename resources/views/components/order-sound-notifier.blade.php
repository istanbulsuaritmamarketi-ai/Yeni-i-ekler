{{-- 🔔 ÇİÇEKÇİ CANLI SİPARİŞ BİLDİRİMİ & ZİL / SES MOTORU --}}
<div x-data="orderSoundNotifier()" x-init="initNotifier()" class="relative">

    <!-- Üst Bar Hızlı Bildirim & Ses Kontrol Butonu -->
    <div class="flex items-center gap-1.5">
        <button type="button" 
                @click="toggleSound()" 
                :title="soundEnabled ? 'Sipariş Sesi Açık (Kapatmak için tıkla)' : 'Sipariş Sesi Kapalı (Açmak için tıkla)'"
                class="px-2.5 py-1 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 border"
                :class="soundEnabled ? 'bg-blue-50 border-blue-300 text-blue-700 hover:bg-blue-100 shadow-2xs' : 'bg-slate-100 border-slate-200 text-slate-500 hover:bg-slate-200'">
            <i class="fas" :class="soundEnabled ? 'fa-bell text-blue-600 animate-wiggle' : 'fa-bell-slash text-slate-400'"></i>
            <span class="hidden md:inline text-[11px]" x-text="soundEnabled ? 'Zil Açık' : 'Zil Kapalı'"></span>
        </button>

        <button type="button" 
                @click="playTestSound()" 
                title="Sipariş zil sesini test et"
                class="p-1 px-2 rounded-xl text-[10px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-600 transition hidden sm:inline-flex items-center gap-1">
            <i class="fas fa-volume-up text-slate-500"></i>
            <span>Test</span>
        </button>
    </div>

    <!-- 🌟 EKRANDA PARLAYAN YENİ SİPARİŞ BİLDİRİM KARTI (TOAST / MODAL) -->
    <template x-for="notif in activeNotifications" :key="notif.id">
        <div class="fixed bottom-5 right-5 z-[9999] max-w-sm w-full bg-white rounded-3xl shadow-2xl border-2 border-blue-400 p-4 transform transition-all duration-300 animate-bounce-short ring-4 ring-blue-200/60"
             role="alert">
            
            <!-- Üst Bar: Başlık & Kapat Butonu -->
            <div class="flex items-center justify-between pb-2 mb-2 border-b border-blue-100">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs animate-pulse">
                        <i class="fas fa-bell"></i>
                    </span>
                    <div>
                        <span class="font-black text-xs text-blue-950 uppercase tracking-wider block">YENİ SİPARİŞ GELDİ!</span>
                        <span class="font-mono text-[10px] text-slate-400 font-bold" x-text="'#' + notif.order_number"></span>
                    </div>
                </div>

                <div class="flex items-center gap-1.5">
                    <template x-if="notif.is_vip">
                        <span class="bg-amber-100 text-amber-800 text-[9px] font-black px-1.5 py-0.5 rounded-md animate-pulse flex items-center gap-1">
                            <i class="fas fa-bolt text-amber-600 text-[8px]"></i>
                            <span>VIP HIZLI</span>
                        </span>
                    </template>
                    <button type="button" @click="dismissNotification(notif.id)" class="text-slate-400 hover:text-slate-600 p-1 text-xs">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- İçerik: Çiçek Resmi & Bilgiler -->
            <div class="flex gap-3 items-center">
                <img :src="notif.product_image" alt="Çiçek" class="w-14 h-14 rounded-2xl object-cover border border-slate-200 shadow-xs shrink-0">
                <div class="flex-1 min-w-0">
                    <h4 class="font-black text-slate-900 text-xs truncate" x-text="notif.product_name"></h4>
                    <div class="text-[11px] text-slate-600 font-bold mt-0.5 flex items-center gap-1">
                        <i class="fas fa-map-marker-alt text-blue-500 text-[10px]"></i>
                        <span class="truncate" x-text="notif.recipient_district + ' • ' + notif.recipient_name"></span>
                    </div>
                    <div class="text-[11px] text-blue-600 font-black mt-0.5 flex items-center justify-between">
                        <span x-text="notif.total_amount"></span>
                        <span class="text-[10px] text-slate-400 font-normal" x-text="notif.delivery_time_slot"></span>
                    </div>
                </div>
            </div>

            <!-- Hızlı Butonlar -->
            <div class="grid grid-cols-2 gap-2 mt-3 pt-2 border-t border-slate-100">
                <a :href="notif.workshop_url" 
                   class="py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-center font-black text-[11px] shadow-xs transition flex items-center justify-center gap-1.5">
                    <i class="fas fa-camera text-[10px]"></i>
                    <span>Atölyede Aç</span>
                </a>
                <a :href="notif.order_url" 
                   class="py-2 px-3 bg-slate-900 hover:bg-black text-white rounded-xl text-center font-bold text-[11px] shadow-xs transition flex items-center justify-center gap-1.5">
                    <i class="fas fa-eye text-[10px]"></i>
                    <span>Sipariş Detayı</span>
                </a>
            </div>

        </div>
    </template>

</div>

<style>
    @keyframes wiggle {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(-12deg); }
        75% { transform: rotate(12deg); }
    }
    .animate-wiggle {
        animation: wiggle 1s ease-in-out infinite;
    }
    @keyframes bounceShort {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    .animate-bounce-short {
        animation: bounceShort 0.5s ease-in-out 3;
    }
</style>

<script>
    function orderSoundNotifier() {
        return {
            soundEnabled: true,
            lastOrderId: {{ \App\Models\Order::max('id') ?? 0 }},
            pollInterval: 8000, // 8 saniyede bir canlı kontrol
            activeNotifications: [],
            pollTimer: null,

            initNotifier() {
                // LocalStorage'dan ses tercihini yükle
                const storedSound = localStorage.getItem('florist_order_sound_enabled');
                if (storedSound !== null) {
                    this.soundEnabled = (storedSound === 'true');
                }

                // Tarayıcı Bildirim İzni Talep Et (Kullanıcı etkileşimiyle)
                if ('Notification' in window && Notification.permission === 'default') {
                    // İlk tıklamada izin istenir
                }

                // Periyodik kontrolü başlat
                this.startPolling();
            },

            toggleSound() {
                this.soundEnabled = !this.soundEnabled;
                localStorage.setItem('florist_order_sound_enabled', this.soundEnabled);

                if (this.soundEnabled) {
                    this.playBellMelody();
                    if ('Notification' in window && Notification.permission !== 'granted') {
                        Notification.requestPermission();
                    }
                }
            },

            playTestSound() {
                this.playBellMelody();
                if ('Notification' in window && Notification.permission === 'default') {
                    Notification.requestPermission();
                }
            },

            // Web Audio API ile Zengin, Kristal Çiçekçi Zil Sesi
            playBellMelody() {
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (!AudioContext) return;
                    const ctx = new AudioContext();

                    // Modern POS / Sipariş Melodisi (E5 -> G#5 -> B5 -> E6 ve çan vuruşu)
                    const melody = [
                        { f: 659.25, start: 0.00, dur: 0.18, type: 'triangle', gain: 0.35 },
                        { f: 830.61, start: 0.16, dur: 0.18, type: 'triangle', gain: 0.40 },
                        { f: 987.77, start: 0.32, dur: 0.22, type: 'sine',     gain: 0.45 },
                        { f: 1318.51, start: 0.48, dur: 0.85, type: 'triangle', gain: 0.50 }, // Çan Çınlaması
                        { f: 2637.02, start: 0.49, dur: 0.40, type: 'sine',     gain: 0.15 }  // Üst Harmonik
                    ];

                    melody.forEach(n => {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();

                        osc.type = n.type;
                        osc.frequency.setValueAtTime(n.f, ctx.currentTime + n.start);

                        // Ses Zarfı (Doğal Çan Akışı)
                        gain.gain.setValueAtTime(0.0001, ctx.currentTime + n.start);
                        gain.gain.linearRampToValueAtTime(n.gain, ctx.currentTime + n.start + 0.015);
                        gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + n.start + n.dur);

                        osc.connect(gain);
                        gain.connect(ctx.destination);

                        osc.start(ctx.currentTime + n.start);
                        osc.stop(ctx.currentTime + n.start + n.dur);
                    });
                } catch (e) {
                    console.warn('AudioContext ses hatası:', e);
                }
            },

            // Cep Telefonu Titreşimi
            triggerVibration() {
                if ('vibrate' in navigator) {
                    try {
                        navigator.vibrate([300, 100, 300, 100, 500]);
                    } catch(e) {}
                }
            },

            // Tarayıcı Sistem Bildirimi (PWA / Masaüstü / Kilit Ekranı)
            sendBrowserNotification(order) {
                if ('Notification' in window && Notification.permission === 'granted') {
                    try {
                        const notif = new Notification('YENİ ÇİÇEK SİPARİŞİ!', {
                            body: '#' + order.order_number + ' - ' + order.product_name + ' (' + order.recipient_district + ') • ' + order.total_amount,
                            icon: '/images/logo-badge.png',
                            badge: '/favicon-32x32.png',
                            tag: 'yenicicek-order-' + order.id,
                            requireInteraction: true // Kullanıcı tıklayana kadar ekranda kalsın
                        });

                        notif.onclick = function() {
                            window.focus();
                            window.location.href = order.workshop_url || order.order_url;
                        };
                    } catch(e) {
                        console.warn('Browser notification error:', e);
                    }
                }
            },

            // Periyodik Sunucu Kontrolü
            startPolling() {
                if (this.pollTimer) clearInterval(this.pollTimer);

                this.pollTimer = setInterval(async () => {
                    await this.checkOrders();
                }, this.pollInterval);
            },

            async checkOrders() {
                try {
                    const response = await fetch(`/admin/atolye/canli-kontrol?last_id=${this.lastOrderId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) return;

                    const data = await response.json();

                    if (data.has_new && data.orders && data.orders.length > 0) {
                        // Yeni sipariş(ler) var!
                        this.lastOrderId = data.latest_id;

                        // 1. Zili Çal
                        if (this.soundEnabled) {
                            this.playBellMelody();
                        }

                        // 2. Titreşim Gönder
                        this.triggerVibration();

                        // 3. Her yeni sipariş için bildirim kartı oluştur
                        data.orders.forEach(order => {
                            this.activeNotifications.push(order);
                            this.sendBrowserNotification(order);
                        });

                        // 4. Global JavaScript Olayı Yayınla (Atölye veya liste sayfaları dinleyebilsin)
                        window.dispatchEvent(new CustomEvent('new-order-received', { detail: data }));
                    } else if (data.latest_id > this.lastOrderId) {
                        this.lastOrderId = data.latest_id;
                    }
                } catch (error) {
                    // Sessizce devam et
                }
            },

            dismissNotification(id) {
                this.activeNotifications = this.activeNotifications.filter(n => n.id !== id);
            }
        };
    }
</script>
