<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::createAssetPathsUsing(function ($path, $secure = null) {
            return '/'.ltrim($path, '/');
        });

        // Share settings globally to all views (Konsol komutlarını kilitlememesi için try-catch ile korunur)
        try {
            if (!app()->runningInConsole() || app()->runningUnitTests()) {
                if (Schema::hasTable('settings')) {
                    $settings = Setting::all()->pluck('value', 'key')->toArray();

                    // Eğer telefon veya whatsapp boş veya 555 dummy ise gerçek numarayı ata
                    if (empty($settings['phone']) || str_contains($settings['phone'], '555 55 55') || str_contains($settings['phone'], '0850')) {
                        $settings['phone'] = '0501 379 82 36';
                    }
                    if (empty($settings['whatsapp']) || str_contains($settings['whatsapp'], '5555555')) {
                        $settings['whatsapp'] = '905013798236';
                    }

                    $themeKey = $settings['theme_preset'] ?? env('THEME', 'indigo');
                    $themePalette = config("theme.palettes.{$themeKey}", config('theme.palettes.indigo'));

                    View::share('siteSettings', $settings);
                    View::share('siteMode', env('SITE_MODE', 'core'));
                    View::share('currentTheme', $themePalette);
                    View::share('currentThemeKey', $themeKey);

                    if (Schema::hasTable('regions')) {
                        $turkeyLocations = \Illuminate\Support\Facades\Cache::remember('turkey_locations_all', 86400, function () {
                            return \App\Models\Region::where('is_active', true)
                                ->orderBy('city')
                                ->orderBy('name')
                                ->get(['id', 'city', 'name'])
                                ->groupBy('city')
                                ->map(fn($districts) => $districts->pluck('name', 'id')->toArray())
                                ->toArray();
                        });
                        View::share('turkeyLocations', $turkeyLocations);
                    }
                } else {
                    $themeKey = env('THEME', 'indigo');
                    $themePalette = config("theme.palettes.{$themeKey}", config('theme.palettes.indigo'));

                    View::share('siteMode', env('SITE_MODE', 'core'));
                    View::share('currentTheme', $themePalette);
                    View::share('currentThemeKey', $themeKey);
                }
            }
        } catch (\Throwable $e) {
            // Veritabanı henüz oluşmamış veya bağlantı yapılandırılmamışsa artisan komutlarını kesintiye uğratma
        }

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::define('manage-users', function ($user) {
            return in_array($user->role, ['superadmin', 'admin']);
        });
    }
}
