<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    // Mengubah email menjadi id_user dan menghapus validasi 'email'
    #[Validate('required|string')]
    public string $id_user = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    // --- 1. PROPERTI CAPTCHA (Wajib ada untuk tampilan) ---
    public int $captcha_num1 = 0;
    public int $captcha_num2 = 0;
    public ?string $captcha_answer = null;

    // --- 2. FUNGSI ACAK ANGKA CAPTCHA ---
    public function generateCaptcha(): void
    {
        $this->captcha_num1 = rand(1, 10);
        $this->captcha_num2 = rand(1, 10);
        $this->captcha_answer = null; // Kosongkan inputan
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // --- 3. VALIDASI CAPTCHA (Dijalankan saat klik login) ---
        $correct_answer = $this->captcha_num1 + $this->captcha_num2;
        
        if (empty($this->captcha_answer) || (int)$this->captcha_answer !== $correct_answer) {
            // Jika salah hitung, acak ulang angkanya
            $this->generateCaptcha(); 
            throw ValidationException::withMessages([
                'form.captcha_answer' => 'Jawaban salah. Silakan hitung dengan benar.',
            ]);
        }

        // --- 4. PROSES AUTHENTICATION MENGGUNAKAN ID USER ---
        if (! Auth::attempt(['id_user' => $this->id_user, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            // Acak ulang captcha karena login gagal
            $this->generateCaptcha();

            throw ValidationException::withMessages([
                'form.id_user' => trans('auth.failed'),
            ]);
        }

        // --- 5. LOGIKA BLOKIR USER (Mempertahankan kode asli Anda) ---
        if (Auth::user()->status_user !== 'Aktif') {
            Auth::guard('web')->logout();
            session()->invalidate();
            session()->regenerateToken();

            throw ValidationException::withMessages([
                'form.id_user' => 'Akun Anda berstatus Nonaktif. Silakan hubungi Admin.',
            ]);
        }
        // -----------------------------------

        RateLimiter::clear($this->throttleKey());
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.id_user' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        // Menggunakan id_user untuk key pembatasan login
        return Str::transliterate(Str::lower($this->id_user).'|'.request()->ip());
    }
}