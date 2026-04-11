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
            'email' => ['required', 'string'],
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

        $credentials = $this->only('email', 'password');

        // Lógica de Login Simplificado para Escritorio (Username -> Email)
        // Solo aplica si el usuario no pone @ y estamos en el entorno local de escritorio
        if (!str_contains($credentials['email'], '@')) {
            $username = $credentials['email'];
            $tenant = \App\Models\Tenant::first();
            
            // Intento 1: Autocompletar el email basado en el slug de la empresa
            if ($tenant) {
                $domain = str_replace('-', '', $tenant->slug);
                $fullEmail = $username . '@' . $domain . '.com';
                
                // Si existe un usuario con este email, lo usamos
                if (\App\Models\User::where('email', $fullEmail)->exists()) {
                    $credentials['email'] = $fullEmail;
                } else {
                    // Intento 2: Buscar el email real del usuario por su NOMBRE
                    $user = \App\Models\User::where('name', 'like', $username . '%')->first();
                    if ($user) {
                        $credentials['email'] = $user->email;
                    }
                }
            } else {
                // Si no hay tenant local (primer inicio), intentamos buscar por nombre directo
                $user = \App\Models\User::where('name', 'like', $username . '%')->first();
                if ($user) {
                    $credentials['email'] = $user->email;
                }
            }
            
            // Actualizamos el request para que el RateLimiter y Auth usen la llave correcta
            $this->merge(['email' => $credentials['email']]);
        }

        if (! Auth::attempt(array_merge($credentials, ['is_active' => true]), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
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
            'email' => trans('auth.throttle', [
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
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
