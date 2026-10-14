<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UtmTracking
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $utmParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content'];

        $hasUtm = false;
        foreach ($utmParams as $param) {
            if ($request->has($param)) {
                $hasUtm = true;
                break;
            }
        }

        if ($hasUtm) {
            $utmData = [];
            foreach ($utmParams as $param) {
                if ($request->has($param)) {
                    $utmData[$param] = $request->get($param);
                }
            }
            session(['utm_data' => $utmData]);
        }

        return $next($request);
    }
}
