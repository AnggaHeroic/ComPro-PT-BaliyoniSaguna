<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Pesan error dalam bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }

    /**
     * Coba masuk. Salah memasukkan email atau kata sandi sebanyak batas (admin.login.max_attempts)
     * mengunci percobaan berikutnya selama admin.login.lockout_seconds detik.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            $attempts = RateLimiter::hit($this->attemptsKey(), 300);

            if ($attempts >= (int) config('admin.login.max_attempts')) {
                RateLimiter::clear($this->attemptsKey());
                RateLimiter::hit($this->lockKey(), (int) config('admin.login.lockout_seconds'));

                $this->throwLockout();
            }

            throw ValidationException::withMessages([
                // Sengaja tidak membedakan email salah atau kata sandi salah, dan sisa percobaan tidak ditampilkan.
                'email' => 'Email atau kata sandi tidak sesuai.',
            ]);
        }

        RateLimiter::clear($this->attemptsKey());
    }

    /**
     * Tolak percobaan selama masa tunggu.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->lockKey(), 1)) {
            return;
        }

        event(new Lockout($this));

        $this->throwLockout();
    }

    // Lama tunggu sengaja tidak disebutkan.
    private function throwLockout(): never
    {
        throw ValidationException::withMessages([
            // Kunci 'lockout' (bukan 'email') agar halaman login menampilkannya sebagai peringatan yang hilang sendiri.
            'lockout' => 'Terlalu banyak percobaan masuk. Coba lagi dalam beberapa menit.',
        ]);
    }

    /** Dihitung per alamat IP, supaya mengganti-ganti email tidak melewati batas. */
    private function attemptsKey(): string
    {
        return 'login-attempts|'.$this->ip();
    }

    private function lockKey(): string
    {
        return 'login-lock|'.$this->ip();
    }
}
