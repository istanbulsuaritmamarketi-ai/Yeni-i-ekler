<?php

use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\UtmTracking;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            UtmTracking::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'paytr/callback',
            'logout',
        ]);

        $middleware->alias([
            'is_admin' => IsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // 419 Page Expired (CSRF) Hatasını Yakala ve Asla Beyaz Hata Ekranı Gösterme
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419) {
                // Çıkış isteğiyse oturumu anında güvenle kapat
                if ($request->is('logout') || $request->routeIs('logout')) {
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('home')->with('success', 'Güvenli bir şekilde çıkış yapıldı.');
                }

                // Kullanıcı oturum açmış durumdaysa (çift istek/yarış durumu), doğrudan dashboard'a yönlendir
                if (Auth::check()) {
                    $user = Auth::user();
                    if ($user && ($user->is_admin || in_array($user->role, ['admin', 'superadmin']))) {
                        return redirect()->route('dashboard');
                    }
                    return redirect()->route('customer.account');
                }

                // Giriş sayfasında veya giriş isteğindeyse temizce login'e yönlendir
                if ($request->is('login') || $request->routeIs('login')) {
                    return redirect()->route('login')->with('status', 'Oturum anahtarınız tazelendi, lütfen tekrar giriş yapın.');
                }

                return redirect()->back()->with('error', 'Oturum süreniz doldu veya yenilendi. Lütfen sayfayı tazeleyip tekrar deneyin.');
            }
        });

        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            // Çıkış isteğiyse oturumu anında güvenle kapat
            if ($request->is('logout') || $request->routeIs('logout')) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('home')->with('success', 'Güvenli bir şekilde çıkış yapıldı.');
            }

            if (Auth::check()) {
                $user = Auth::user();
                if ($user && ($user->is_admin || in_array($user->role, ['admin', 'superadmin']))) {
                    return redirect()->route('dashboard');
                }
                return redirect()->route('customer.account');
            }

            if ($request->is('login') || $request->routeIs('login')) {
                return redirect()->route('login')->with('status', 'Oturum anahtarınız tazelendi, lütfen tekrar giriş yapın.');
            }

            return redirect()->back()->with('error', 'Güvenlik anahtarınız yenilendi. Lütfen tekrar deneyin.');
        });
    })->create();
