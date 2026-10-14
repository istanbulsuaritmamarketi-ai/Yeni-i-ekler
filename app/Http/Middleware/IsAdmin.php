<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (auth()->user()->is_admin || in_array(auth()->user()->role, ['admin', 'superadmin']))) {
            return $next($request);
        }

        if (! auth()->check()) {
            return redirect()->guest(route('login'));
        }

        abort(403, 'Bu alana erişim yetkiniz yok.');
    }
}
