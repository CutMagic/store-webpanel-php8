<?php
// This file is part of the Store Webpanel V2 (GNU Affero General Public License v3, see LICENSE).
//
// [multimod] Admin > Réglages du site : page Joueurs publique (visible sans connexion Steam,
// donc par Google), nom et description du site. Permission : WebPanelPanelUsersEdit
// (comme la gestion des comptes admin), voir config/route_perms.php.

namespace App\Http\Controllers\WebPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Support\PanelSettings;
use App\Support\Utf8;
use Log;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = PanelSettings::all();
        $profilesEnabled = (bool) config('userpanel.player_profiles');
        $showCredits = (bool) config('userpanel.player_profiles_credits');
        $publicActive = PanelSettings::publicPlayers();

        return view('templates.' . config('webpanel.template') . 'webpanel.settings.index',
            compact('settings', 'profilesEnabled', 'showCredits', 'publicActive'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'public_players'   => 'nullable|in:0,1',
            'site_name'        => 'nullable|string|max:64',
            'site_description' => 'nullable|string|max:300',
        ]);
        $clean = function ($v) {
            // une seule ligne, sans caractères de contrôle
            return trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', Utf8::clean((string) $v)));
        };
        $values = [
            'public_players'   => ($data['public_players'] ?? '0') === '1',
            'site_name'        => $clean($data['site_name'] ?? ''),
            'site_description' => $clean($data['site_description'] ?? ''),
        ];

        try {
            PanelSettings::save($values);
        } catch (\RuntimeException $e) {
            return redirect()->route('webpanel.settings.index')->withInput()
                ->withErrors([__('The settings could not be saved: the folder storage/app is not writable.')]);
        }

        Log::info('[webpanel] réglages du site', ['admin' => Auth::user() ? Auth::user()->name : null] + $values);
        return redirect()->route('webpanel.settings.index')->with('message', __('Settings saved.'));
    }
}
