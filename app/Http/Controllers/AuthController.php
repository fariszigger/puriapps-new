<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function index()
    {
        $n1 = rand(1, 10);
        $n2 = rand(1, 10);
        session(['captcha_answer' => $n1 + $n2]);

        return view('auth.login', compact('n1', 'n2'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'captcha_answer' => 'required|numeric',
        ]);

        if ($request->captcha_answer != session('captcha_answer')) {
            // Generate new captcha on failure
            $this->generateNewCaptcha();
            
            return back()->withErrors([
                'captcha_answer' => 'Jawaban captcha salah!',
            ])->withInput($request->only('username', 'remember'));
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Update is_online status
            $user = Auth::user();
            /** @var \App\Models\User $user */
            $user->update(['is_online' => '1']);

            return redirect()->intended('/')->with('success', 'Login successful! Welcome back.');
        }

        return back()->withErrors([
            'username' => 'username atau password salah!',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            /** @var \App\Models\User $user */
            $user->update(['is_online' => '0']);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function refreshCaptcha()
    {
        $data = $this->generateNewCaptcha();
        return response()->json($data);
    }

    private function generateNewCaptcha()
    {
        $n1 = rand(1, 10);
        $n2 = rand(1, 10);
        session(['captcha_answer' => $n1 + $n2]);
        
        return ['n1' => $n1, 'n2' => $n2];
    }
}
