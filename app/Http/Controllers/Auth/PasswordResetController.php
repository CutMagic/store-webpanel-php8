<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * [php82] "Mot de passe oublié" des comptes admin du panneau.
 * - envoie un LIEN à usage unique (jamais le mot de passe), valable config('auth.passwords.users.expire') minutes ;
 * - même réponse que l'adresse existe ou non (on ne peut pas deviner les e-mails des admins) ;
 * - le mail part après l'envoi de la page (pas de différence de temps de réponse) ;
 * - le lien est construit avec APP_URL, pas avec l'adresse envoyée par le navigateur
 *   (sinon un attaquant pourrait faire pointer le lien du mail vers son propre site) ;
 * - le jeton est rangé haché dans webpanel_password_resets et n'est pas mis dans l'adresse avec l'e-mail.
 */
class PasswordResetController extends Controller
{
    private const SENT = 'Si cette adresse correspond à un compte admin, un mail avec un lien vient de lui être envoyé. '
        . 'Pense à regarder dans les spams.';

    public function showLinkRequestForm()
    {
        return view('templates.' . \Config::get('webpanel.template') . 'auth.password');
    }

    public function sendResetLink(Request $request)
    {
        $data = $request->validate(['email' => 'required|email|max:255']);
        $email = $data['email'];

        app()->terminating(function () use ($email) {
            try {
                $status = Password::sendResetLink(['email' => $email]);
                if ($status !== Password::RESET_LINK_SENT && $status !== Password::INVALID_USER) {
                    Log::info('Mot de passe oublié : demande refusée (' . $status . ')');
                }
            } catch (\Throwable $e) {
                // SMTP injoignable, mauvais mot de passe du compte mail... : visible dans storage/logs
                Log::error('Mot de passe oublié : le mail n\'a pas pu partir : ' . $e->getMessage());
            }
        });

        return back()->with('status', self::SENT);
    }

    public function showResetForm($token)
    {
        return view('templates.' . \Config::get('webpanel.template') . 'auth.reset', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'token' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255|confirmed',
        ], [
            'email.email' => 'Adresse e-mail invalide.',
            'password.min' => 'Le mot de passe doit faire au moins 8 caractères.',
            'password.confirmed' => 'Les deux mots de passe ne sont pas identiques.',
        ]);

        $userId = null;
        $status = Password::reset($data, function ($user, $password) use (&$userId) {
            $user->password = Hash::make($password);
            $user->setRememberToken(Str::random(60)); // les "se souvenir de moi" existants ne marchent plus
            $user->save();
            $userId = $user->getKey();
        });

        if ($status === Password::PASSWORD_RESET) {
            Log::info('Mot de passe oublié : mot de passe changé pour le compte admin #' . $userId);
            return redirect()->route('login')->with('status', 'Mot de passe changé. Tu peux te connecter.');
        }

        // jeton faux, expiré, déjà utilisé, ou e-mail qui ne va pas avec ce lien : même message
        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'Ce lien n\'est pas valable (déjà utilisé, expiré, ou adresse différente de celle du compte). '
                . 'Refais une demande.']);
    }
}
