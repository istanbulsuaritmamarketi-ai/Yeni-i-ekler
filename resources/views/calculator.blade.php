<x-frontend-layout title="Damacana vs Su Arıtma Kâr & Tasarruf Hesaplama | İstanbul Su Arıtma Marketi" description="Damacana ve hazır su harcamanızı hesaplayın, su arıtma cihazı ile ayda ve yılda ne kadar tasarruf edeceğinizi görün.">
    <!-- Ana Hesaplayıcı Bileşeni -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
        <x-savings-calculator />

        <!-- Neden Arıtma Cihazı? Bilgi Kartları -->
        <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="bg-white p-6 rounded-3xl border border-gray-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3 class="text-sm font-black text-gray-900 mb-1.5">Kendi Kendini Amorti Eder</h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Damacana fiyatlarının arttığı günümüzde, bir su arıtma cihazı ortalama 3-4 ay içerisinde kendi maliyetini tamamen çıkarır.
                </p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-gray-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-tint"></i>
                </div>
                <h3 class="text-sm font-black text-gray-900 mb-1.5">Kesintisiz ve Taze Su</h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Damacana siparişi bekleme, suyun bitmesi veya güneş altında bekleyen plastik damacanalardaki BPA riskine maruz kalmazsınız.
                </p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-gray-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="text-sm font-black text-gray-900 mb-1.5">2 Yıl Resmî Garanti</h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Tüm cihazlarımız 2 yıl resmî garantilidir ve İstanbul genelinde aynı gün randevulu ücretsiz montaj hizmetimiz bulunmaktadır.
                </p>
            </div>
        </div>

        <!-- Hızlı Eylem & WhatsApp Danışma -->
        <div class="mt-8 bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h4 class="text-base font-black text-emerald-950">Aklınıza Takılan Bir Soru mu Var?</h4>
                <p class="text-xs text-emerald-800/80 mt-1">Uzman müşteri danışmanımız su tüketiminize en uygun cihazı belirlemeniz için ücretsiz destek sunar.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('products.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-black px-5 py-3 rounded-2xl shadow transition-all">
                    Cihazları İncele
                </a>
                <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text=Merhaba,%20tasarruf%20hesaplama%20sayfan%C4%B1zdan%20yaz%C4%B1yorum.%20Evim%20i%C3%A7in%20en%20uygun%20ar%C4%B1tma%20cihaz%C4%B1n%C4%B1%20%C3%B6%C4%9Frenmek%20istiyorum." target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black px-5 py-3 rounded-2xl shadow transition-all flex items-center gap-2">
                    <i class="fab fa-whatsapp text-sm"></i> WhatsApp Danışma
                </a>
            </div>
        </div>
    </div>
</x-frontend-layout>
