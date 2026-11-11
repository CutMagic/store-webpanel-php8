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

class StoreCategoryRequest extends Request
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
        // [multimod] longueurs des colonnes de store_categories (avant : texte trop long coupé sans prévenir)
        return [
            'priority' => 'nullable|integer|min:-1000000|max:1000000',
            'display_name' => 'required|string|max:32',
            'description' => 'nullable|string|max:128',
            'require_plugin' => 'required|string|max:32',
            'web_description' => 'nullable|string|max:65535',
            'web_color' => 'nullable|string|max:10',
            'enable_server_restriction' => 'nullable|in:0,1',
            'server_list' => 'nullable|array',
            'server_list.*' => 'integer',
        ];
    }

}
