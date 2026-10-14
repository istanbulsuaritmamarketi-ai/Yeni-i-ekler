<x-frontend-layout 
    title="Arimo - Yapay Zeka Su Arıtma Danışmanı | İstanbul Su Arıtma Marketi"
    description="Arimo yapay zeka asistanı ile evinize veya iş yerinize en uygun su arıtma cihazını ve %100 uyumlu yedek filtre setini 30 saniyede bulun.">

    <div class="bg-slate-100/80 min-h-[calc(100vh-140px)] py-4 sm:py-8 px-3 sm:px-6 lg:px-8 flex flex-col justify-center"
         x-data="arimoChat()">

        <div class="max-w-4xl mx-auto w-full flex flex-col h-[82vh] sm:h-[84vh] bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden">
            
            <!-- Arimo Başlık Bandı -->
            <div class="p-4 sm:p-5 bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 text-white flex items-center justify-between shadow-md shrink-0">
                <div class="flex items-center gap-3 sm:gap-3.5">
                    <div class="relative">
                        <div class="w-11 h-11 sm:w-13 sm:h-13 rounded-2xl bg-white/15 backdrop-blur-md border border-white/25 flex items-center justify-center text-xl sm:text-2xl shadow-inner">
                            <i class="fas fa-robot"></i>
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-400 border-2 border-slate-900 rounded-full animate-pulse" title="Çevrimiçi"></span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-base sm:text-xl font-black text-white tracking-tight">Arimo</h1>
                            <span class="text-[10px] font-black uppercase tracking-wider bg-blue-500/40 text-blue-100 border border-white/20 px-2 py-0.5 rounded-full">
                                Canlı Asistan
                            </span>
                        </div>
                        <p class="text-xs text-blue-100/90 font-medium">İstanbul Su Arıtma Marketi Akıllı Danışmanı</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text=Merhaba,%20Arimo%20asistan%C4%B1n%C4%B1zdan%20destek%20ald%C4%B1m.%20Canl%C4%B1%20uzmanla%20g%C3%B6r%C3%BC%C5%9Fmek%20istiyorum." 
                       target="_blank" 
                       class="flex items-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs shadow transition-all">
                        <i class="fab fa-whatsapp text-sm sm:text-base"></i> 
                        <span class="hidden sm:inline">Canlı Uzmana Bağlan</span>
                        <span class="sm:hidden">WhatsApp</span>
                    </a>
                </div>
            </div>

            <!-- Sohbet Alanı (Kaydırılabilir Mesaj Penceresi) -->
            <div id="chatMessages" class="flex-1 overflow-y-auto space-y-4 p-4 sm:p-6 bg-slate-50/80">
                
                <!-- Başlangıç Karşılama Balonu -->
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-base sm:text-lg shadow-sm shrink-0 border border-blue-500/20">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="space-y-3 max-w-2xl">
                        <div class="bg-white border border-gray-200/90 p-4 sm:p-5 rounded-3xl rounded-tl-sm text-xs sm:text-sm text-gray-800 leading-relaxed shadow-sm">
                            <p class="font-black text-gray-900 text-sm sm:text-base mb-1.5 flex items-center gap-2">
                                <span>Merhaba! Ben Arimo</span> 
                                <i class="fas fa-tint text-blue-500"></i>
                            </p>
                            <p class="text-gray-700">
                                İstanbul Su Arıtma Marketi'nin akıllı danışmanıyım. Eviniz, iş yeriniz veya mevcut cihazınız için en doğru su arıtma cihazını ve %100 uyumlu filtre setini saniyeler içinde birlikte bulabiliriz!
                            </p>
                            <p class="mt-3 text-gray-500 text-xs font-semibold flex items-center gap-1.5">
                                <i class="fas fa-lightbulb text-amber-500"></i>
                                <span>Aşağıdaki hazır sorulardan birine dokunabilir veya sorunuzu alttaki alana yazabilirsiniz:</span>
                            </p>
                        </div>

                        <!-- Hızlı Öneri Butonları -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                            <button type="button" @click="askQuick('4-5 kişilik bir aileyiz, en sağlıklı su arıtma cihazı hangisi?')"
                                    class="text-left text-xs font-bold p-3 rounded-xl bg-white hover:bg-blue-50 border border-gray-200 hover:border-blue-400 text-gray-800 hover:text-blue-700 shadow-2xs transition-all cursor-pointer flex items-center gap-2 group">
                                <span class="text-sm text-blue-600 group-hover:scale-110 transition-transform"><i class="fas fa-users"></i></span>
                                <span>4-5 kişilik aile için en iyi cihaz hangisi?</span>
                            </button>
                            <button type="button" @click="askQuick('Fiyat/performans olarak en ekonomik su arıtma cihazı hangisi?')"
                                    class="text-left text-xs font-bold p-3 rounded-xl bg-white hover:bg-blue-50 border border-gray-200 hover:border-blue-400 text-gray-800 hover:text-blue-700 shadow-2xs transition-all cursor-pointer flex items-center gap-2 group">
                                <span class="text-sm text-amber-500 group-hover:scale-110 transition-transform"><i class="fas fa-coins"></i></span>
                                <span>En ekonomik ve kaliteli cihaz hangisi?</span>
                            </button>
                            <button type="button" @click="askQuick('Pompalı mı yoksa pompasız mı almalıyım? Basınç farkı nedir?')"
                                    class="text-left text-xs font-bold p-3 rounded-xl bg-white hover:bg-blue-50 border border-gray-200 hover:border-blue-400 text-gray-800 hover:text-blue-700 shadow-2xs transition-all cursor-pointer flex items-center gap-2 group">
                                <span class="text-sm text-slate-500 group-hover:scale-110 transition-transform"><i class="fas fa-cog"></i></span>
                                <span>Pompalı mı pompasız mı almalıyım?</span>
                            </button>
                            <button type="button" @click="askQuick('İhlas Aura Cebilon su arıtma cihazım var, uyumlu filtre arıyorum')"
                                    class="text-left text-xs font-bold p-3 rounded-xl bg-white hover:bg-purple-50 border border-gray-200 hover:border-purple-400 text-gray-800 hover:text-purple-700 shadow-2xs transition-all cursor-pointer flex items-center gap-2 group">
                                <span class="text-sm text-purple-600 group-hover:scale-110 transition-transform"><i class="fas fa-search"></i></span>
                                <span>İhlas / LG / BMS cihazım için filtre</span>
                            </button>
                            <button type="button" @click="askQuick('Kireçli ve sert şebeke suları için hangi membran filtre en iyisi?')"
                                    class="text-left text-xs font-bold p-3 rounded-xl bg-white hover:bg-cyan-50 border border-gray-200 hover:border-cyan-400 text-gray-800 hover:text-cyan-700 shadow-2xs transition-all cursor-pointer flex items-center gap-2 group">
                                <span class="text-sm text-amber-600 group-hover:scale-110 transition-transform"><i class="fas fa-mug-hot"></i></span>
                                <span>Kireç yapmayan membran filtre hangisi?</span>
                            </button>
                            <button type="button" @click="askQuick('İstanbul içi montaj ve teknik servis şartlarınız nedir?')"
                                    class="text-left text-xs font-bold p-3 rounded-xl bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-400 text-gray-800 hover:text-emerald-700 shadow-2xs transition-all cursor-pointer flex items-center gap-2 group">
                                <span class="text-sm text-emerald-600 group-hover:scale-110 transition-transform"><i class="fas fa-wrench"></i></span>
                                <span>Ücretsiz montaj ve servis şartları</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Dinamik Mesajlar Listesi -->
                <template x-for="(msg, idx) in messages" :key="idx">
                    <div>
                        <!-- Kullanıcı Mesajı -->
                        <template x-if="msg.isUser">
                            <div class="flex items-start justify-end gap-2.5 my-3">
                                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-3.5 sm:p-4 rounded-3xl rounded-tr-sm text-xs sm:text-sm font-medium max-w-lg shadow-md leading-relaxed">
                                    <p x-text="msg.text"></p>
                                </div>
                                <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-black shrink-0 border border-blue-200">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                        </template>

                        <!-- Arimo Mesajı -->
                        <template x-if="!msg.isUser">
                            <div class="flex items-start gap-3 my-3">
                                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-base sm:text-lg shadow-sm shrink-0 border border-blue-500/20">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <div class="space-y-3 max-w-2xl">
                                    <!-- Metin Balonu -->
                                    <div class="bg-white border border-gray-200/90 p-4 sm:p-5 rounded-3xl rounded-tl-sm text-xs sm:text-sm text-gray-800 leading-relaxed shadow-sm">
                                        <div class="whitespace-pre-line text-gray-800" x-html="formatReply(msg.text)"></div>

                                        <!-- Hızlı Aksiyon Linki (Varsa) -->
                                        <template x-if="msg.actionUrl">
                                            <div class="mt-3.5 pt-3 border-t border-gray-100">
                                                <a :href="msg.actionUrl" 
                                                   class="inline-flex items-center gap-1.5 text-xs font-black text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3.5 py-2 rounded-xl border border-blue-200 transition-colors">
                                                    <span x-text="msg.actionText || 'İlgili Sayfayı İncele'"></span>
                                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                                </a>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Önerilen Ürünler Vitrini (Varsa) -->
                                    <template x-if="msg.products && msg.products.length > 0">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                            <template x-for="prod in msg.products" :key="prod.id">
                                                <div class="bg-white hover:bg-blue-50/50 border border-gray-200 hover:border-blue-400 rounded-2xl p-3 flex gap-3 items-center transition-all shadow-2xs hover:shadow-md group">
                                                    <template x-if="prod.image">
                                                        <img :src="prod.image" :alt="prod.name" class="w-16 h-16 object-contain rounded-xl bg-gray-50 border border-gray-100 p-1 shrink-0">
                                                    </template>
                                                    <template x-if="!prod.image">
                                                        <div class="w-16 h-16 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-xl shrink-0 text-blue-500">
                                                            <i class="fas fa-tint"></i>
                                                        </div>
                                                    </template>
                                                    <div class="flex-1 min-w-0">
                                                        <h4 class="text-xs font-bold text-gray-900 group-hover:text-blue-700 line-clamp-2 leading-snug" x-text="prod.name"></h4>
                                                        <div class="mt-1 flex items-baseline gap-2">
                                                            <span class="text-xs font-black text-blue-600" x-text="prod.campaign_price || prod.price"></span>
                                                            <template x-if="prod.campaign_price">
                                                                <span class="text-[10px] text-gray-400 line-through" x-text="prod.price"></span>
                                                            </template>
                                                        </div>
                                                        <a :href="prod.url" target="_blank" 
                                                           class="inline-flex items-center gap-1 mt-1 text-[11px] font-bold text-blue-600 hover:text-blue-800 underline">
                                                            <span>İncele &amp; Satın Al</span>
                                                            <i class="fas fa-arrow-right text-[9px]"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Yazıyor... İndikatörü -->
                <div x-show="isTyping" x-transition class="flex items-center gap-3 my-2">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-base sm:text-lg shadow-sm shrink-0 border border-blue-500/20">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="bg-white border border-gray-200 px-4 py-2.5 rounded-2xl rounded-tl-sm flex items-center gap-2 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-bounce"></span>
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-bounce [animation-delay:0.2s]"></span>
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-bounce [animation-delay:0.4s]"></span>
                        <span class="text-xs text-gray-700 font-bold ml-1.5">Arimo düşünüyor...</span>
                    </div>
                </div>

            </div>

            <!-- Alt Mesaj Yazma Çubuğu -->
            <form @submit.prevent="sendMessage()" class="p-3 sm:p-4 bg-white border-t border-gray-200 shrink-0">
                <div class="relative flex items-center">
                    <input type="text" x-model="inputText" :disabled="isTyping"
                           placeholder="Arimo'ya su arıtma ile ilgili dilediğinizi sorun... (Örn: Pompalı mı pompasız mı almalıyım?)"
                           class="w-full bg-gray-50 border-2 border-gray-200 focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-100 text-gray-900 placeholder-gray-400 rounded-2xl py-3.5 sm:py-4 pl-4 sm:pl-5 pr-28 text-xs sm:text-sm font-medium shadow-inner transition-all">
                    
                    <button type="submit" :disabled="isTyping || !inputText.trim()"
                            class="absolute right-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs px-4 sm:px-5 py-2.5 sm:py-3 rounded-xl shadow-md transition-all transform active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-1.5 cursor-pointer">
                        <span>Gönder</span>
                        <i class="fas fa-paper-plane text-xs"></i>
                    </button>
                </div>
            </form>

        </div>

    </div>

    <script>
        function arimoChat() {
            return {
                inputText: '',
                isTyping: false,
                messages: [],
                askQuick(text) {
                    this.inputText = text;
                    this.sendMessage();
                },
                sendMessage() {
                    const text = this.inputText.trim();
                    if (!text || this.isTyping) return;

                    // Kullanıcı mesajını ekle
                    this.messages.push({
                        isUser: true,
                        text: text
                    });
                    this.inputText = '';
                    this.isTyping = true;
                    this.scrollToBottom();

                    fetch('{{ url('/arimo/ask') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: text })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isTyping = false;
                        this.messages.push({
                            isUser: false,
                            text: data.reply || 'Cevap alınamadı.',
                            products: data.products || [],
                            actionUrl: data.action_url,
                            actionText: data.action_text
                        });
                        this.scrollToBottom();
                    })
                    .catch(err => {
                        this.isTyping = false;
                        this.messages.push({
                            isUser: false,
                            text: 'Bağlantı hatası oluştu. Lütfen tekrar deneyiniz veya WhatsApp hattımızdan destek alınız.',
                            products: []
                        });
                        this.scrollToBottom();
                    });
                },
                scrollToBottom() {
                    setTimeout(() => {
                        const container = document.getElementById('chatMessages');
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    }, 100);
                },
                formatReply(text) {
                    if (!text) return '';
                    // Kalın metinleri yüksek kontrastlı mavi ile biçimlendir (**kelime**)
                    let formatted = text.replace(/\*\*(.*?)\*\*/g, '<strong class="text-blue-700 font-extrabold">$1</strong>');
                    return formatted;
                }
            }
        }
    </script>
</x-frontend-layout>
