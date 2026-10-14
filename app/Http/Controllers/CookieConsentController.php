<?php

namespace App\Http\Controllers;

use App\Models\CookieConsent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CookieConsentController extends Controller
{
    /**
     * KVKK & Çerez Açık Rıza Kaydı (Proof of Consent Logger)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'consent_token' => 'required|string|max:64',
            'consent_type' => 'required|in:all,custom,rejected',
            'necessary' => 'boolean',
            'analytics' => 'boolean',
            'marketing' => 'boolean',
        ]);

        $consent = CookieConsent::create([
            'consent_token' => $validated['consent_token'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'consent_type' => $validated['consent_type'],
            'necessary' => true,
            'analytics' => $validated['analytics'] ?? ($validated['consent_type'] === 'all'),
            'marketing' => $validated['marketing'] ?? ($validated['consent_type'] === 'all'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'KVKK çerez açık rıza kaydı başarıyla kaydedildi.',
            'id' => $consent->id,
        ]);
    }
}
