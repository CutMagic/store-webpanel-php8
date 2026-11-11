<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

/**
 * [php82] Connexion des comptes admin du panneau (remplace l'AuthController de Laravel 5.1).
 * Pas d'inscription libre : les comptes se créent dans Panel > Users.
 * Limite : 5 essais ratés par minute et par IP + e-mail.
 */
class LoginController extends Controller
{
    public function getLogin()
    {
        return view('templates.' . \Config::get('webpanel.template') . 'auth.webpanellogin');
    }

    public function postLogin(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = 'login:' . strtolower($data['email']) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __('Too many attempts. Try again in :seconds seconds.', ['seconds' => RateLimiter::availableIn($key)])]);
        }

        if (Auth::attempt($data, $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect()->intended(route('webpanel.dashboard'));
        }

        RateLimiter::hit($key, 60);
        return back()->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => __('These credentials do not match our records.')]);
    }

    public function getLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
