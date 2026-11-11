<?php namespace App\Http\Requests;

//Copyright (c) 2015 "Werner Maisl"
//
//This file is part of the Store Webpanel V2.
//
//The Store Webpanel V2 is free software: you can redistribute it and/or modify
//it under the terms of the GNU Affero General Public License as
//published by the Free Software Foundation, either version 3 of the
//License, or (at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU Affero General Public License for more details.
//
//You should have received a copy of the GNU Affero General Public License
//along with this program. If not, see <http://www.gnu.org/licenses/>.

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

// [multimod] Contrôles du formulaire d'objet, d'après la structure de store_items et le plugin :
// avant, un nom en double donnait une erreur 500, un nom trop long était coupé sans prévenir, et un
// JSON invalide, un prix négatif (qui fait GAGNER des crédits en jeu) ou une catégorie inexistante
// (objet invisible en jeu) étaient enregistrés. La description devient facultative (colonne NULL ;
// la plupart des objets n'en ont pas, ils ne pouvaient plus être modifiés).
class StoreItemRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $item = $this->route('items');
        $id = is_object($item) ? $item->getKey() : (is_numeric($item) ? (int) $item : null);

        return [
            'priority' => 'nullable|integer|min:-1000000|max:1000000',
            // le plugin met le nom dans ses requêtes SQL : pas de guillemets, \, <, >, ; ni %
            'name' => ['required', 'string', 'max:32', 'regex:/^[^\'"\\<>;%]+$/u', Rule::unique('store.items', 'name')->ignore($id)],
            'display_name' => 'required|string|max:32',
            'description' => 'nullable|string|max:128',
            'web_description' => 'nullable|string|max:65535',
            'type' => 'required|string|max:32',
            'loadout_slot' => 'required|string|max:32',
            'price' => 'required|integer|min:0|max:2147483647',
            'category_id' => ['required', 'integer', Rule::exists('store.categories', 'id')],
            'attrs' => 'nullable|json|max:65535',
            'is_buyable' => 'nullable|in:0,1',
            'is_tradeable' => 'nullable|in:0,1',
            'is_refundable' => 'nullable|in:0,1',
            'expiry_time' => 'nullable|integer|min:0|max:2147483647',
            'flags' => ['nullable', 'string', 'max:11', 'regex:/^[a-z]*$/i'],
            'enable_server_restriction' => 'nullable|in:0,1',
            'server_list' => 'nullable|array',
            'server_list.*' => 'integer',
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => __('The name may not contain quotes, backslashes, < > ; or %.'),
            'name.unique' => __('An item with this name already exists.'),
            'price.min' => __('The price cannot be negative (a negative price would give credits in game).'),
            'category_id.exists' => __('This category does not exist.'),
            'attrs.json' => __('The attributes (attrs) must be valid JSON.'),
            'flags.regex' => __('The flags must be SourceMod admin flag letters (for example: b or ab).'),
        ];
    }
}
