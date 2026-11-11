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

class StoreUserRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // [multimod] auth = numéro de compte Steam, unique (avant : doublon = erreur 500). Entiers 32 bits ;
        // valeurs négatives acceptées comme le plugin (joueurs non-Steam, crédits en négatif).
        $user = $this->route('store_user');
        $id = is_object($user) ? $user->getKey() : (is_numeric($user) ? (int) $user : null);

        return [
            'auth' => ['required', 'integer', 'min:-2147483648', 'max:2147483647', Rule::unique('store.users', 'auth')->ignore($id)],
            'name' => 'required|string|max:32',
            'credits' => 'required|integer|min:-2147483648|max:2147483647',
        ];
    }

    public function messages()
    {
        return [
            'auth.unique' => __('A player with this Steam account number (auth) already exists.'),
        ];
    }

}
