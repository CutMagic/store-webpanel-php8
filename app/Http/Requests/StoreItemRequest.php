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

class StoreItemRequest extends Request
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
        return [
            'priority' => 'numeric',
            'name' => 'required', //TODO: Check for no whitespaces
            'display_name' => 'required',
            'description' => 'required',
            'web_description' => 'nullable', // [php82] règle sans nom refusée par Laravel 12
            'type' => 'required',
            'loadout_slot' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'attrs' => 'nullable', // [php82] TODO: Perfom JSON Validation on the Attrs field
            'is_buyable' => 'nullable', // [php82] règle sans nom refusée par Laravel 12
            'is_tradeable' => 'nullable', // [php82] règle sans nom refusée par Laravel 12
            'is_refundable' => 'nullable', // [php82] règle sans nom refusée par Laravel 12
            'expiry_time' => 'nullable', // [php82] règle sans nom refusée par Laravel 12
            'flags' => 'nullable', // [php82] règle sans nom refusée par Laravel 12
        ];
    }

}
