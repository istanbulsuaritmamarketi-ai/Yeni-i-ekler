<x-guest-layout>
    <div class="space-y-6">
        
        <!-- Başlık & Karşılama -->
        <div class="text-center space-y-2">
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">
                Giriş Yap
            </h2>
            <p class="text-xs text-gray-500 max-w-sm mx-auto">
                Yönetim veya müşteri panelinize erişmek için lütfen giriş yapın.
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        @if (isset($errors) && $errors->any())
            <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs font-bold space-y-1">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" 
              x-data="{ submitting: false }" 
              @submit="if(submitting) { $event.preventDefault(); return false; } submitting = true;" 
              class="space-y-4">
            @csrf

            <!-- Telefon veya E-posta -->
            <div>
                <label for="login" class="block text-xs font-bold text-gray-700 mb-1">
                    Telefon Numarası veya E-Posta
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-user-circle text-xs"></i>
                    </div>
                    <input id="login" type="text" name="login" value="{{ old('login', old('email')) }}" required autofocus
                           placeholder="05xx xxx xx xx veya e-posta"
                           class="w-full pl-9 text-xs font-bold py-2.5 px-3.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-theme-primary focus:border-theme-primary">
                </div>
            </div>

            <!-- Şifre -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-bold text-gray-700">
                        Şifreniz
                    </label>
                    @if (Route::has('password.request'))
                        <a class="text-[11px] font-medium text-theme-primary hover:underline" href="{{ route('password.request') }}">
                            Şifremi unuttum?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-lock text-xs"></i>
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           placeholder="Şifrenizi girin"
                           class="w-full pl-9 text-xs py-2.5 px-3.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-theme-primary focus:border-theme-primary">
                </div>
            </div>

            <!-- Beni Hatırla -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" checked class="rounded-md border-gray-300 text-theme-primary shadow-xs focus:ring-theme-primary" name="remember">
                    <span class="ms-2 text-xs text-gray-600 font-medium">Beni hatırla (Oturumu açık tut)</span>
                </label>
            </div>

            <button type="submit" 
                    :disabled="submitting"
                    :class="submitting ? 'opacity-70 cursor-not-allowed' : 'hover:bg-theme-primary-hover active:scale-95'"
                    class="w-full bg-theme-primary text-white font-black text-xs py-3.5 px-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer">
                <template x-if="!submitting">
                    <span class="flex items-center gap-2"><i class="fas fa-sign-in-alt"></i> Giriş Yap</span>
                </template>
                <template x-if="submitting">
                    <span class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Giriş yapılıyor...</span>
                </template>
            </button>

            @if(app()->isLocal())
                <!-- Geliştirici / Yerel Hızlı Doldurma Kutusu -->
                <div class="mt-4 p-3.5 bg-blue-50 border border-blue-200 rounded-xl text-xs space-y-2.5 text-left shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-blue-950 flex items-center gap-1.5">
                            <i class="fas fa-shield-alt text-blue-600"></i> Yönetici Giriş Bilgileri
                        </span>
                        <a href="{{ route('craftsman') }}" 
                           class="inline-flex items-center gap-1 text-[11px] bg-blue-600 hover:bg-blue-700 text-white px-2.5 py-1 rounded-lg font-bold transition shadow-2xs">
                            <i class="fas fa-wrench"></i> Şifresiz Usta Ekranı
                        </a>
                    </div>
                    <div class="text-[11px] text-blue-900 bg-white/80 p-2.5 rounded-lg border border-blue-100 flex items-center justify-between gap-2">
                        <div class="font-mono space-y-0.5">
                            <div>E-posta: <strong class="text-blue-950">admin@tazecicekler.com</strong></div>
                            <div>Şifre: <strong class="text-blue-950">12345678</strong></div>
                        </div>
                        <button type="button" 
                                @click="document.getElementById('login').value = 'admin@tazecicekler.com'; document.getElementById('password').value = '12345678'; $el.closest('form').submit();"
                                class="text-[11px] bg-slate-900 text-white px-3 py-1.5 rounded-lg font-bold hover:bg-black transition whitespace-nowrap">
                            Hemen Giriş Yap
                        </button>
                    </div>
                </div>
            @endif
        </form>

        <!-- Kayıt Ol Linki -->
        @if(Route::has('register'))
        <div class="pt-4 border-t border-gray-100 text-center space-y-2">
            <p class="text-xs text-gray-500">
                Henüz bir hesabınız yok mu?
            </p>
            <a href="{{ route('register') }}" 
               class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-gray-50 hover:bg-theme-light text-gray-800 hover:text-theme-primary font-black text-xs rounded-xl border border-gray-200 transition-colors">
                <i class="fas fa-user-plus"></i> Hesap Oluştur
            </a>
        </div>
        @endif

    </div>
</x-guest-layout>
