<x-frontend-layout>
    <div class="bg-gray-50 py-16">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Başlık -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Sosyal Medyamız</h1>
                <p class="text-gray-500 text-lg">Bizi takip edin, kampanyalardan ve yeniliklerden haberdar olun!</p>
            </div>

            <!-- Kanallar -->
            <div class="space-y-4">

                <!-- Instagram -->
                <a href="{{ $siteSettings['instagram'] ?? '#' }}" target="_blank"
                   class="flex items-center gap-6 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 via-pink-500 to-orange-400 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fab fa-instagram text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xl font-bold text-gray-900 group-hover:text-pink-600 transition-colors">Instagram</div>
                        <div class="text-gray-500 text-sm mt-1">Kurulum videoları, müşteri yorumları ve kampanyalarımız için Instagram sayfamızı takip edin.</div>
                        <div class="text-pink-500 text-sm font-semibold mt-2">{{ $siteSettings['instagram'] ? '@instagramhesabiniz' : 'Yakında aktif olacak' }}</div>
                    </div>
                    <i class="fas fa-arrow-right text-gray-300 group-hover:text-pink-500 group-hover:translate-x-1 transition-all"></i>
                </a>

                <!-- Facebook -->
                <a href="{{ $siteSettings['facebook'] ?? '#' }}" target="_blank"
                   class="flex items-center gap-6 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fab fa-facebook-f text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Facebook</div>
                        <div class="text-gray-500 text-sm mt-1">Duyurularımızı, ürün lansmanlarımızı ve müşteri hikayelerini Facebook sayfamızdan takip edin.</div>
                        <div class="text-blue-500 text-sm font-semibold mt-2">{{ $siteSettings['facebook'] ? 'Facebook Sayfamız' : 'Yakında aktif olacak' }}</div>
                    </div>
                    <i class="fas fa-arrow-right text-gray-300 group-hover:text-blue-500 group-hover:translate-x-1 transition-all"></i>
                </a>

                <!-- WhatsApp -->
                <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905555555555' }}?text=Merhaba, sosyal medya sayfanızdan ulaşıyorum." target="_blank"
                   class="flex items-center gap-6 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group">
                    <div class="w-16 h-16 rounded-2xl bg-green-500 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:scale-110 transition-transform">
                        <i class="fab fa-whatsapp text-white text-3xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-xl font-bold text-gray-900 group-hover:text-green-600 transition-colors">WhatsApp</div>
                        <div class="text-gray-500 text-sm mt-1">Sorularınız, fiyat bilgisi ve servis talepleriniz için WhatsApp destek hattımızdan bize ulaşın.</div>
                        <div class="text-green-500 text-sm font-semibold mt-2">{{ $siteSettings['whatsapp'] ?? 'Destek Hattımız' }}</div>
                    </div>
                    <i class="fas fa-arrow-right text-gray-300 group-hover:text-green-500 group-hover:translate-x-1 transition-all"></i>
                </a>

            </div>

            <!-- CTA -->
            <div class="text-center mt-12 p-8 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl text-white">
                <h2 class="text-2xl font-bold mb-2">Hemen İletişime Geçin!</h2>
                <p class="text-blue-200 mb-6">Ürünlerimiz ve hizmetlerimiz hakkında her türlü sorunuzu yanıtlamaktan mutluluk duyarız.</p>
                <a href="{{ route('contact') }}" class="bg-white text-blue-700 font-bold px-8 py-3 rounded-full hover:bg-blue-50 transition-colors inline-block">
                    İletişim Formu →
                </a>
            </div>

        </div>
    </div>
</x-frontend-layout>
