<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function mount(): void
    {
        $this->form->generateCaptcha();
    }

    // === TAMBAHKAN FUNGSI INI ===
    public function refreshCaptcha(): void
    {
        $this->form->generateCaptcha();
    }
    // ============================

public function login(): void
{
    $this->validate();
    $this->form->authenticate();

    // Regenerasi sesi untuk keamanan
    session()->regenerate();

    // 🔴 HAPUS navigate: true – gunakan full redirect
    // Redirect intended tanpa navigate (default false)
    $this->redirectIntended(default: route('dashboard', absolute: false), navigate: false);
}   }; ?>

<div class="login-wrapper">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root {
            --primary-purple: #6d28d9;
            --dark-purple: #4c1d95;
            --bg-white: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        .login-wrapper {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 9999;
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            background: var(--bg-white);
            overflow: hidden;
        }

        /* PANEL KIRI (UNGU) */
        .panel-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-purple), var(--dark-purple));
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 60px;
            position: relative;
        }

        /* Pembatas Garis Lurus Minimalis */
        .panel-left::after {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 1px;
            background: rgba(255, 255, 255, 0.15);
        }

        /* Tulisan Welcome (Diperbesar & Di Atas) */
        .panel-left h2 { 
            font-size: 2.2rem; /* Diperbesar */
            font-weight: 500; 
            opacity: 0.9; 
            margin-bottom: 25px; 
            letter-spacing: 1px;
        }

        /* Logo Box Tetap Besar */
        .panel-left .logo-box {
            width: 280px; 
            height: 280px; 
            background: rgba(255, 255, 255, 0.12);
            border-radius: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 40px;
            border: 1px solid rgba(255,255,255,0.25);
            backdrop-filter: blur(10px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }

        .panel-left .logo-box img {
            width: 70%;
            filter: brightness(0) invert(1);
        }

        .panel-left h1 { 
            font-size: 3rem; 
            font-weight: 800; 
            margin-bottom: 20px; 
            letter-spacing: -1.5px; 
            line-height: 1.1; 
        }

        .panel-left p { 
            font-size: 1.15rem; 
            opacity: 0.75; 
            line-height: 1.7; 
            max-width: 480px; 
        }

        /* PANEL KANAN (PUTIH) */
        .panel-right {
            flex: 1.2;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background: white;
        }

        .form-container {
            width: 100%;
            max-width: 420px;
        }

        .form-header { margin-bottom: 45px; }
        .form-header h3 { font-size: 2.4rem; font-weight: 800; color: var(--text-main); letter-spacing: -1px; }
        .form-header p { color: var(--text-muted); margin-top: 10px; font-size: 1.1rem; }

        /* Input Style */
        .input-group { margin-bottom: 35px; }
        .input-group label { 
            display: block; 
            font-size: 0.85rem; 
            font-weight: 800; 
            color: var(--text-main);
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .form-input {
            width: 100%;
            border: none;
            border-bottom: 2px solid #f1f5f9;
            padding: 12px 0;
            font-family: inherit;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus { border-bottom-color: var(--primary-purple); }

        /* Captcha Box */
        .captcha-box {
            background: #f8fafc;
            padding: 24px;
            border-radius: 20px;
            margin-bottom: 35px;
            border: 1px solid #f1f5f9;
        }

        .flex-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; }
        .checkbox-wrapper { display: flex; align-items: center; gap: 12px; font-size: 1rem; color: var(--text-muted); cursor: pointer; }
        .checkbox-wrapper input { width: 19px; height: 19px; accent-color: var(--primary-purple); }

        /* Button */
        .btn-group { display: flex; gap: 20px; }
        .btn-primary {
            flex: 2;
            background: var(--primary-purple);
            color: white;
            border: none;
            padding: 18px 40px;
            border-radius: 16px;
            font-weight: 800;
            font-size: 1.1rem;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-primary:hover { background: var(--dark-purple); transform: translateY(-3px); }

        .btn-secondary {
            flex: 1;
            background: white;
            border: 2px solid #f1f5f9;
            color: var(--text-muted);
            padding: 18px;
            border-radius: 16px;
            font-weight: 700;
            text-decoration: none;
            text-align: center;
            transition: 0.3s;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        .error-text { color: #ef4444; font-size: 0.85rem; margin-top: 8px; font-weight: 600; }

        @media (max-width: 1024px) {
            .panel-left { display: none; }
            .panel-right { padding: 40px 20px; }
        }
    </style>

    <div class="panel-left">
        <h2>Welcome to</h2> <div class="logo-box">
            <img src="{{ asset('logo.png') }}" alt="Logo">
        </div>
        
        <h1>Supply Chain Management</h1>
        <p>Sistem manajemen logistik cerdas untuk efisiensi operasional dan pemantauan stok secara real-time.</p>
    </div>

    <div class="panel-right">
        <div class="form-container">
            <div class="form-header">
                <h3>Log In</h3>
                <p>Masukkan akun Anda untuk melanjutkan.</p>
            </div>

            <form wire:submit="login">
                <x-auth-session-status class="mb-6 text-red-600 font-bold" :status="session('status')" />

                <div class="input-group">
                                        <label class="block text-sm font-bold text-slate-700 mb-2">ID User</label>

                    <input wire:model="form.id_user" type="text" class="form-input" placeholder="Masukkan ID User Anda" required autofocus>
                    @error('form.id_user') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                {{-- Password --}}
                <div x-data="{ showPassword: false }">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                    <div class="relative flex items-center">
                        <input wire:model="form.password" 
                               :type="showPassword ? 'text' : 'password'" 
                               class="form-input w-full pr-12" 
                               placeholder="••••••••" 
                               autocomplete="current-password">
                        
                        {{-- Tombol Toggle Mata --}}
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute right-3 p-2 text-slate-400 hover:text-indigo-600 focus:outline-none transition-colors"
                                tabindex="-1">
                            
                            {{-- Icon Mata Biasa (Tampil saat password disembunyikan) --}}
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>

                            {{-- Icon Mata Dicoret (Tampil saat password terlihat) --}}
                            <svg x-show="showPassword" x-cloak style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
                </div>

              {{-- Verifikasi Math Captcha --}}
                <div class="bg-indigo-50/50 p-4 rounded-2xl border border-indigo-100">
                    <label class="block text-sm font-bold text-indigo-900 mb-3 text-center">
                        Verifikasi: {{ $form->captcha_num1 }} + {{ $form->captcha_num2 }} = ?
                    </label>
                    <div class="flex items-center gap-3">
                        <input wire:model="form.captcha_answer" type="number" 
                               class="form-input text-center font-bold text-xl flex-1" 
                               placeholder="Masukkan Jawaban">
                        
                        {{-- Tombol Refresh Kembali Ditambahkan --}}
                        <button type="button" wire:click="refreshCaptcha" 
                                class="p-3 text-slate-400 hover:text-indigo-600 transition-colors rounded-xl hover:bg-indigo-100" 
                                title="Ganti Angka">
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('form.captcha_answer')" class="mt-2 text-center" />
                </div>

                <div class="flex-row">
                <label class="checkbox-wrapper">
                    <input wire:model="form.remember" type="checkbox"> Ingat saya
                </label>
</div>

                <div class="btn-group">
                    <button type="submit" class="btn-primary">Masuk Sekarang</button>
                    <a href="/" class="btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>