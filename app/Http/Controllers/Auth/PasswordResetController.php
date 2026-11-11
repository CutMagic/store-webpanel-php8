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
    // [multimod] texte anglais = clé de traduction (français dans lang/fr.json)
    private const SENT = 'If this address belongs to an admin account, an e-mail with a link has just been sent to it. '
        . 'Remember to check your spam folder.';

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

        return back()->with('status', __(self::SENT));
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
            'email.email' => __('Invalid e-mail address.'),
            'password.min' => __('The password must be at least 8 characters long.'),
            'password.confirmed' => __('The two passwords do not match.'),
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
            return redirect()->route('login')->with('status', __('Password changed. You can sign in.'));
        }

        // jeton faux, expiré, déjà utilisé, ou e-mail qui ne va pas avec ce lien : même message
        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __('This link is not valid (already used, expired, or a different address from the account). Please make a new request.')]);
    }
}
