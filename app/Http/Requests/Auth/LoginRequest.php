<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['sometimes', 'string'],
            'email' => ['sometimes', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginInput = trim($this->input('login') ?? $this->input('email') ?? '');
        $password = $this->input('password');

        if (empty($loginInput)) {
            throw ValidationException::withMessages([
                'login' => 'Lütfen telefon numaranızı veya e-posta adresinizi giriniz.',
            ]);
        }

        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);
        $authenticated = false;

        if ($isEmail) {
            $authenticated = Auth::attempt(['email' => $loginInput, 'password' => $password], $this->boolean('remember'));
        } else {
            // Cep telefonu ile giriş
            $cleanPhone = preg_replace('/[^0-9]/', '', $loginInput);
            if (str_starts_with($cleanPhone, '90') && strlen($cleanPhone) === 12) {
                $cleanPhone = '0'.substr($cleanPhone, 2);
            } elseif (! str_starts_with($cleanPhone, '0') && strlen($cleanPhone) === 10) {
                $cleanPhone = '0'.$cleanPhone;
            }

            // 1. Doğrudan phone kolonu ile dene
            $authenticated = Auth::attempt(['phone' => $cleanPhone, 'password' => $password], $this->boolean('remember'));

            // 2. Olmazsa fallback email ile dene
            if (! $authenticated) {
                $fallbackEmail = $cleanPhone.'@musteri.istanbulsuaritmamarketi.com';
                $authenticated = Auth::attempt(['email' => $fallbackEmail, 'password' => $password], $this->boolean('remember'));
            }
        }

        if (! $authenticated) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => 'Telefon numarası / e-posta veya şifre hatalı.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $loginInput = $this->input('login') ?? $this->input('email') ?? '';

        return Str::transliterate(Str::lower($loginInput).'|'.$this->ip());
    }
}
