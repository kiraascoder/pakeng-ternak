<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSesiController extends Controller
{
    public function adminLoginView()
    {
        return view('auth.login');
    }

    public function registerView()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Silahkan Masukkan Email Anda',
            'email.email' => 'Format email yang Anda masukkan tidak valid',
            'password.required' => 'Silahkan Masukkan Password Anda',
            'password.min' => 'Password minimal terdiri dari 6 karakter',
        ]);

        $infologin = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (Auth::attempt($infologin, $request->has('remember'))) {
            $user = Auth::user();

            if ($user->role == "Peternak") {
                return redirect('/peternak/dashboard');
            } elseif ($user->role == "Penyuluh") {
                return redirect('/penyuluh/dashboard');
            } elseif ($user->role == 'Admin') {
                return redirect('/admin/dashboard');
            }
        }
        return redirect('/admin/login')->withErrors(['login' => 'Login Gagal, Email atau Password tidak sesuai!'])->withInput();
    }
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|numeric',
            'role' => 'required|string|in:Peternak,Penyuluh',
        ], [
            'nama.required' => 'Nama Wajib Diisi',
            'email.required' => 'Silahkan Masukkan Email Anda',
            'email.email' => 'Format email yang Anda masukkan tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Silahkan Masukkan Password Anda',
            'password.min' => 'Password minimal terdiri dari 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
            'phone.required' => 'Nomor telepon wajib diisi',
            'role.required' => 'Silahkan Pilih Role Anda',
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role
        ]);

        return redirect('/admin/login')->with('success', 'Registrasi berhasil! Silahkan Login Ke Akun Anda.');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }
}
