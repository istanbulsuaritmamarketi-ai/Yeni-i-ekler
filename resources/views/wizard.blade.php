<x-frontend-layout title="Evinize Uygun Su Arıtma Cihazını Bulun | Test & Sihirbaz" description="30 saniyede ailenizin kişi sayısına, su basıncına ve kullanım amacına en uygun su arıtma cihazını keşfedin.">
    <!-- Sihirbaz Bileşeni -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
        <x-quiz-wizard />

        <!-- Doğrudan Destek & Keşif -->
        <div class="mt-8 bg-white border border-gray-200/80 rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xs">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl flex-shrink-0">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-gray-900">İstanbul İçi Ücretsiz Keşif &amp; Danışmanlık</h4>
                    <p class="text-xs text-gray-500 mt-0.5">39 ilçede adresinize gelip su basıncınızı ve TDS su kalitenizi ücretsiz ölçüyoruz.</p>
                </div>
            </div>
            <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text=Merhaba,%20cihaz%20sihirbaz%C4%B1ndan%20geliyorum.%20Evim%20i%C3%A7in%20en%20do%C4%9Fru%20modeli%20birlikte%20se%C3%A7ebilir%20miyiz?" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black px-5 py-3 rounded-2xl shadow transition-all flex items-center gap-2 flex-shrink-0">
                <i class="fab fa-whatsapp text-sm"></i> WhatsApp'tan Sorun
            </a>
        </div>
    </div>
</x-frontend-layout>
