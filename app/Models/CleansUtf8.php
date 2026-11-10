<?php

namespace App\Models;

use App\Support\Utf8;

/**
 * [multimod] Nettoie à la lecture les textes qui ne sont pas de l'UTF-8 valide
 * (pseudos coupés par le plugin). Voir App\Support\Utf8.
 */
trait CleansUtf8
{
    public function getAttribute($key)
    {
        return Utf8::clean(parent::getAttribute($key));
    }

    public function attributesToArray()
    {
        $a = parent::attributesToArray();
        foreach ($a as $k => $v) {
            if (is_string($v)) $a[$k] = Utf8::clean($v);
        }
        return $a;
    }
}
