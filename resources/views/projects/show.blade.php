<x-frontend-layout :title="$project->title . ' | Proje Detayı'">
    <div class="bg-gray-50 py-10 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center gap-2 text-xs text-gray-500 mb-4">
                <a href="/" class="hover:text-theme-primary">Ana Sayfa</a>
                <span>/</span>
                <a href="{{ route('projects.index') }}" class="hover:text-theme-primary">Projelerimiz</a>
                <span>/</span>
                <span class="text-gray-900 font-bold truncate">{{ $project->title }}</span>
            </nav>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="inline-block px-3 py-1 bg-theme-light text-theme-primary text-xs font-black rounded-full mb-2 uppercase">
                        {{ $project->category ?? 'Proje' }} • {{ $project->status }}
                    </span>
                    <h1 class="text-2xl sm:text-4xl font-black text-gray-900">{{ $project->title }}</h1>
                    @if($project->location)
                        <p class="text-xs sm:text-sm text-gray-600 mt-1 flex items-center gap-1.5">
                            <i class="fas fa-map-marker-alt text-amber-500"></i> {{ $project->location }}
                        </p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <a href="https://wa.me/{{ $siteSettings['whatsapp'] ?? '905013798236' }}?text={{ urlencode('Merhaba, ' . $project->title . ' projesi hakkında bilgi ve teklif almak istiyorum.') }}" target="_blank"
                        class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-xs sm:text-sm flex items-center gap-2 shadow-md transition-all">
                        <i class="fab fa-whatsapp text-lg"></i>
                        <span>WhatsApp'tan Teklif İste</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Sol / Ana Alan (Görsel, Açıklama, Galeri) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Ana Görsel -->
                <div class="rounded-3xl overflow-hidden bg-gray-100 shadow-sm border border-gray-100">
                    @if($project->featured_image)
                        <img src="{{ asset('storage/' . $project->featured_image) }}" alt="{{ $project->title }}" class="w-full h-auto max-h-[500px] object-cover">
                    @else
                        <div class="w-full h-96 flex items-center justify-center bg-gray-100 text-gray-400">
                            <i class="fas fa-building text-6xl"></i>
                        </div>
                    @endif
                </div>

                <!-- Proje Açıklaması -->
                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-xs">
                    <h2 class="text-xl font-black text-gray-900 mb-4 pb-2 border-b border-gray-100">Proje Hakkında</h2>
                    <div class="prose max-w-none text-gray-700 text-sm sm:text-base leading-relaxed">
                        {!! nl2br(e($project->description ?? $project->summary)) !!}
                    </div>
                </div>

                <!-- Galeri Varsa -->
                @if(!empty($project->gallery) && is_array($project->gallery))
                    <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-xs">
                        <h2 class="text-xl font-black text-gray-900 mb-4 pb-2 border-b border-gray-100">Proje Görselleri</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach($project->gallery as $img)
                                <a href="{{ asset('storage/' . $img) }}" target="_blank" class="rounded-2xl overflow-hidden h-36 bg-gray-50 border border-gray-100 group block">
                                    <img src="{{ asset('storage/' . $img) }}" alt="Galeri" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sağ Alan: Proje Özellikleri & Hızlı Teklif Formu -->
            <div class="space-y-6">
                <!-- Özellik Kartı -->
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <h3 class="text-base font-black text-gray-900 mb-4 pb-2 border-b border-gray-100">Proje Künyesi</h3>
                    <dl class="space-y-3 text-xs sm:text-sm">
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <dt class="text-gray-500 font-semibold">Durum:</dt>
                            <dd class="font-black text-gray-900">{{ $project->status }}</dd>
                        </div>
                        @if($project->category)
                            <div class="flex justify-between py-1.5 border-b border-gray-50">
                                <dt class="text-gray-500 font-semibold">Kategori:</dt>
                                <dd class="font-bold text-gray-900">{{ $project->category }}</dd>
                            </div>
                        @endif
                        @if($project->location)
                            <div class="flex justify-between py-1.5 border-b border-gray-50">
                                <dt class="text-gray-500 font-semibold">Lokasyon:</dt>
                                <dd class="font-bold text-gray-900">{{ $project->location }}</dd>
                            </div>
                        @endif
                        @if($project->client)
                            <div class="flex justify-between py-1.5 border-b border-gray-50">
                                <dt class="text-gray-500 font-semibold">İşveren / Müşteri:</dt>
                                <dd class="font-bold text-gray-900">{{ $project->client }}</dd>
                            </div>
                        @endif
                        @if(!empty($project->specifications) && is_array($project->specifications))
                            @foreach($project->specifications as $key => $val)
                                <div class="flex justify-between py-1.5 border-b border-gray-50">
                                    <dt class="text-gray-500 font-semibold capitalize">{{ str_replace('_', ' ', $key) }}:</dt>
                                    <dd class="font-bold text-gray-900">{{ $val }}</dd>
                                </div>
                            @endforeach
                        @endif
                    </dl>
                </div>

                <!-- Hızlı Teklif / Keşif İste Formu -->
                <div class="bg-gradient-to-br from-gray-900 to-slate-900 text-white p-6 rounded-3xl shadow-xl">
                    <h3 class="text-lg font-black tracking-tight">Teklif &amp; Bilgi Alın</h3>
                    <p class="text-xs text-gray-300 mt-1">Bu proje veya benzer bir çalışma için bize hemen ulaşın.</p>
                    <form action="{{ route('contact.store') }}" method="POST" class="mt-4 space-y-3">
                        @csrf
                        <input type="hidden" name="subject" value="{{ $project->title }} Teklif Talebi">
                        <div>
                            <input type="text" name="name" required placeholder="Adınız Soyadınız" class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-xs text-white placeholder-gray-400 focus:outline-none focus:border-amber-400">
                        </div>
                        <div>
                            <input type="tel" name="phone" required placeholder="Telefon Numaranız" class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-xs text-white placeholder-gray-400 focus:outline-none focus:border-amber-400">
                        </div>
                        <div>
                            <textarea name="message" rows="3" placeholder="Notunuz veya talebiniz..." class="w-full px-3.5 py-2.5 bg-white/10 border border-white/20 rounded-xl text-xs text-white placeholder-gray-400 focus:outline-none focus:border-amber-400"></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 bg-theme-primary hover:bg-theme-primary-hover text-white text-xs font-black rounded-xl shadow-md transition-all">
                            Teklif Talebini Gönder
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-frontend-layout>
