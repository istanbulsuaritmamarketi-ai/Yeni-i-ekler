@if(!empty($siteSettings['logo']))
    <img src="{{ asset('storage/' . $siteSettings['logo']) }}" alt="{{ config('app.name') }}" {{ $attributes->merge(['class' => 'h-14 w-auto object-contain']) }}>
@else
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3 text-theme-primary']) }}>
        <div class="w-12 h-12 bg-theme-light text-theme-primary rounded-2xl flex items-center justify-center shadow-inner border border-gray-100 shrink-0 text-xl">
            <i class="fas {{ $siteMode === 'corporate' ? 'fa-building' : ($currentThemeKey === 'rose' ? 'fa-spa' : 'fa-crown') }}"></i>
        </div>
        <div class="flex flex-col text-left">
            <span class="text-xl font-black tracking-tight text-gray-900 whitespace-nowrap">{{ config('app.name', 'Yiğit Ajans Core') }}</span>
            <span class="text-[11px] text-theme-primary font-bold tracking-wider uppercase">
                {{ $siteMode === 'corporate' ? 'Kurumsal & Proje Yönetimi' : 'Müşteri & Alışveriş Portalı' }}
            </span>
        </div>
    </div>
@endif
