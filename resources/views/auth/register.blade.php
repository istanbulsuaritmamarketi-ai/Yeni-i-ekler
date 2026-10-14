<x-guest-layout>
    <div class="space-y-6">
        
        <!-- Başlık & Karşılama -->
        <div class="text-center space-y-2">
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">
                Hesap Oluştur
            </h2>
            <p class="text-xs text-gray-500 max-w-sm mx-auto">
                {{ config('app.name', 'Yiğit Ajans Core') }} ailesine katılın, siparişlerinizi ve taleplerinizi kolayca yönetin.
            </p>
        </div>

        @if (isset($errors) && $errors->any())
            <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs font-bold space-y-1">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- 1. Ad Soyad -->
            <div>
                <label for="name" class="block text-xs font-bold text-gray-700 mb-1">
                    Adınız Soyadınız <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-user text-xs"></i>
                    </div>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="Örn: Ahmet Yılmaz"
                           class="w-full pl-9 text-xs font-bold py-2.5 px-3.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-theme-primary focus:border-theme-primary">
                </div>
            </div>

            <!-- 2. Telefon Numarası -->
            <div>
                <label for="phone" class="block text-xs font-bold text-gray-700 mb-1">
                    Telefon Numaranız <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-phone text-xs"></i>
                    </div>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
                           placeholder="05xx xxx xx xx"
                           class="w-full pl-9 text-xs font-bold py-2.5 px-3.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-theme-primary focus:border-theme-primary">
                </div>
            </div>

            <!-- 3. E-Posta -->
            <div>
                <label for="email" class="block text-xs font-bold text-gray-700 mb-1">
                    E-Posta Adresiniz (Opsiyonel)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-envelope text-xs"></i>
                    </div>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           placeholder="ornek@alanadi.com"
                           class="w-full pl-9 text-xs py-2.5 px-3.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-theme-primary focus:border-theme-primary">
                </div>
            </div>

            <!-- 4. Şifre -->
            <div>
                <label for="password" class="block text-xs font-bold text-gray-700 mb-1">
                    Şifre Belirleyin <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-lock text-xs"></i>
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                           placeholder="En az 6 karakter"
                           class="w-full pl-9 text-xs py-2.5 px-3.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-theme-primary focus:border-theme-primary">
                </div>
            </div>

            <!-- 5. Şifre Tekrarı -->
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-gray-700 mb-1">
                    Şifre Tekrarı <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-lock text-xs"></i>
                    </div>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                           placeholder="Şifrenizi tekrar yazın"
                           class="w-full pl-9 text-xs py-2.5 px-3.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-theme-primary focus:border-theme-primary">
                </div>
            </div>

            <button type="submit" 
                    class="w-full bg-theme-primary hover:bg-theme-primary-hover text-white font-black text-xs py-3.5 px-4 rounded-xl shadow-lg transition-all transform active:scale-95 flex items-center justify-center gap-2 cursor-pointer mt-2">
                <i class="fas fa-user-plus"></i> Kaydı Tamamla
            </button>
        </form>

        <!-- Giriş Yap Linki -->
        <div class="pt-4 border-t border-gray-100 text-center space-y-2">
            <p class="text-xs text-gray-500">
                Zaten bir hesabınız var mı?
            </p>
            <a href="{{ route('login') }}" 
               class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-gray-50 hover:bg-theme-light text-gray-800 hover:text-theme-primary font-black text-xs rounded-xl border border-gray-200 transition-colors">
                <i class="fas fa-sign-in-alt"></i> Giriş Yap
            </a>
        </div>

    </div>
</x-guest-layout>
