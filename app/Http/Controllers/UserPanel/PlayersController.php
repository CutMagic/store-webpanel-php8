<?php
// This file is part of the Store Webpanel V2 (GNU Affero General Public License v3, see LICENSE).
//
// [multimod] Page "Joueurs" de l'espace joueur : classement, recherche (pseudo ou SteamID) et profil
// des autres joueurs (SteamID, crédits, objets possédés et équipés), en LECTURE SEULE.
// Réglages dans .env : USERPANEL_PLAYER_PROFILES (page entière) et USERPANEL_PLAYER_PROFILES_CREDITS
// (crédits et rang des autres joueurs). Réservé aux joueurs connectés (middleware storeuserauth).

namespace App\Http\Controllers\UserPanel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StoreUser;
use App\Models\StoreItem;
use App\Models\StoreLoadout;
use App\Support\SteamIds;
use App\Support\Utf8;
use DB;

class PlayersController extends Controller
{
    const PER_PAGE = 25;

    private function checkEnabled()
    {
        if (!config('userpanel.player_profiles')) abort(404);
    }

    private static function showCredits()
    {
        return (bool) config('userpanel.player_profiles_credits');
    }

    private static function view($name, $data)
    {
        return view('templates.' . config('userpanel.template') . 'userpanel.players.' . $name, $data);
    }

    /** Rang d'un joueur au classement des crédits (1 = le plus riche), comme le tableau de bord. */
    private static function rank($credits)
    {
        return StoreUser::where('credits', '>', $credits)->count() + 1;
    }

    /** Classement des joueurs, avec recherche par pseudo ou SteamID. */
    public function getIndex(Request $request)
    {
        $this->checkEnabled();
        $me = StoreUser::findOrFail($request->session()->get('store_user_id'));
        $showCredits = self::showCredits();

        $q = $request->query('q', '');
        $q = is_string($q) ? trim(mb_substr(Utf8::clean($q), 0, 64)) : '';

        $query = StoreUser::query();
        if ($q !== '') {
            $account = SteamIds::parseToAccountId($q);
            // SteamID complet (STEAM_0:..., [U:1:...], 7656119..., adresse du profil) : on va directement au profil
            if ($account !== null && SteamIds::isExplicit($q)) {
                $found = StoreUser::where('auth', $account)->first();
                if ($found) return redirect()->route('userpanel.players.show', ['id' => $found->id]);
            }
            $like = '%' . addcslashes($q, '\\%_') . '%';
            $query->where(function ($w) use ($like, $account) {
                $w->where('name', 'like', $like);
                if ($account !== null) $w->orWhere('auth', $account);
            });
        }
        if ($showCredits) {
            $query->orderBy('credits', 'desc')->orderBy('id');
        } else {
            $query->orderBy('name')->orderBy('id'); // sans crédits : pas de classement par richesse
        }

        $players = $query->paginate(self::PER_PAGE, ['id', 'auth', 'name', 'credits'])->withQueryString();

        $ids = $players->pluck('id')->all();
        $itemCounts = empty($ids) ? [] : DB::connection('store')->table('users_items')
            ->whereIn('user_id', $ids)->groupBy('user_id')
            ->select('user_id', DB::raw('COUNT(*) as n'))->pluck('n', 'user_id')->all();

        $rows = [];
        foreach ($players as $p) {
            $rows[] = (object) [
                'id' => $p->id, 'auth' => $p->auth, 'name' => $p->name, 'credits' => $p->credits,
                'rank' => $showCredits ? self::rank($p->credits) : null,
                'items' => (int) ($itemCounts[$p->id] ?? 0),
                'steam' => SteamIds::fromAccountId($p->auth),
                'is_me' => $p->id == $me->id,
            ];
        }
        $myPage = $showCredits ? (int) ceil(self::rank($me->credits) / self::PER_PAGE) : null;

        return self::view('index', compact('players', 'rows', 'q', 'showCredits', 'me', 'myPage'));
    }

    /** Profil d'un joueur : SteamID, crédits, objets équipés et possédés. */
    public function getShow(Request $request, $id)
    {
        $this->checkEnabled();
        $me = StoreUser::findOrFail($request->session()->get('store_user_id'));
        $player = StoreUser::findOrFail((int) $id);
        $showCredits = self::showCredits();
        $st = DB::connection('store');

        // Objets possédés, regroupés par objet (requête valable avec ONLY_FULL_GROUP_BY)
        $counts = $st->table('users_items')->where('user_id', $player->id)
            ->groupBy('item_id')->get(['item_id', DB::raw('COUNT(*) as quantity')]);
        $qty = [];
        foreach ($counts as $c) $qty[$c->item_id] = (int) $c->quantity;
        $owned = [];
        if (!empty($qty)) {
            foreach (StoreItem::with('category')->whereIn('id', array_keys($qty))->get() as $it) {
                $owned[] = (object) [
                    'display_name' => $it->display_name, 'description' => $it->description, 'type' => $it->type,
                    'category' => $it->category ? $it->category->display_name : '', 'quantity' => $qty[$it->id],
                ];
            }
            usort($owned, function ($x, $y) { return strcasecmp($x->category . $x->display_name, $y->category . $y->display_name); });
        }

        // Objets équipés, par loadout (A, B, C...)
        $eqRows = $st->table('users_items_loadouts')
            ->join('users_items', 'users_items.id', '=', 'users_items_loadouts.useritem_id')
            ->where('users_items.user_id', $player->id)
            ->get(['users_items_loadouts.loadout_id', 'users_items.item_id']);
        $eqItemIds = $eqRows->pluck('item_id')->unique()->all();
        $eqItems = empty($eqItemIds) ? collect() : StoreItem::whereIn('id', $eqItemIds)->get()->keyBy('id');
        $equipped = [];
        foreach (StoreLoadout::orderBy('id')->get() as $l) {
            $list = [];
            foreach ($eqRows as $r) {
                if ($r->loadout_id == $l->id && isset($eqItems[$r->item_id])) {
                    $it = $eqItems[$r->item_id];
                    $list[$it->id] = (object) ['display_name' => $it->display_name, 'type' => $it->type, 'slot' => $it->loadout_slot];
                }
            }
            uasort($list, function ($x, $y) { return strcasecmp($x->slot . $x->display_name, $y->slot . $y->display_name); });
            $equipped[] = (object) ['name' => $l->display_name, 'items' => array_values($list)];
        }
        $equippedCount = $eqRows->count();

        $steam = SteamIds::fromAccountId($player->auth);
        $rank = $showCredits ? self::rank($player->credits) : null;
        $itemCount = array_sum($qty);
        $isMe = $player->id == $me->id;

        return self::view('show', compact('player', 'steam', 'rank', 'owned', 'equipped', 'equippedCount', 'itemCount', 'showCredits', 'isMe'));
    }
}
