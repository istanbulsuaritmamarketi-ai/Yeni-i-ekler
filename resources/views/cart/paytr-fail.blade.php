<x-frontend-layout>
    <div class="bg-gray-50 py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-sm border border-gray-200/80 text-center space-y-6">
                
                <div class="w-20 h-20 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto text-3xl shadow-sm animate-bounce">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <div>
                    <span class="text-xs font-black text-rose-600 bg-rose-50 px-3 py-1 rounded-full uppercase tracking-wider border border-rose-200">
                        ÖDEME TAMAMLANAMADI
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 mt-3">
                        Kredi Kartı Ödemesi Alınamadı
                    </h1>
                    <p class="text-sm text-gray-500 mt-2 max-w-md mx-auto leading-relaxed">
                        Kart limitiniz yetersiz olabilir, 3D Secure SMS kodu yanlış girilmiş olabilir veya işlem bankanız tarafından onaylanmamış olabilir.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 max-w-md mx-auto text-left text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Sipariş No:</span>
                        <span class="font-mono font-bold text-gray-900">{{ $order->order_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Sipariş Tutarı:</span>
                        <span class="font-extrabold text-blue-600">{{ number_format($order->total_amount, 2, ',', '.') }} TL</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Kayıtlı Adres:</span>
                        <span class="font-medium text-gray-800 text-right truncate max-w-[200px]">{{ $order->address }}</span>
                    </div>
                </div>

                <!-- Aksiyon Butonları -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-4">
                    <a href="{{ route('checkout') }}" 
                       class="w-full sm:w-auto px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs rounded-2xl shadow-lg transition-all transform active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-redo"></i> Tekrar Dene / Kapıda Ödemeye Geç
                    </a>

                    <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905555555555' }}?text=Merhaba,%20{{ $order->order_number }}%20numaralı%20siparişimde%20kredi%20kartı%20ödemesinde%20hata%20aldım.%20Yardımcı%20olur%20musunuz?" 
                       target="_blank"
                       class="w-full sm:w-auto px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-2xl shadow transition-all flex items-center justify-center gap-2">
                        <i class="fab fa-whatsapp text-sm"></i> WhatsApp ile Destek Al
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-frontend-layout>
