<?php

namespace App\Support;

/**
 * [multimod] Le plugin Store coupe les pseudos à 32 octets, parfois au milieu d'un caractère
 * (emoji, symbole...). Ce texte n'est plus de l'UTF-8 valide : json_encode échoue (pages en 500)
 * et Blade affiche une case vide. On le nettoie à l'AFFICHAGE, sans rien changer dans la base.
 */
class Utf8
{
    public static function clean($s)
    {
        if (!is_string($s) || mb_check_encoding($s, 'UTF-8')) return $s;
        // remplace les octets invalides par "?" (sans avertissement PHP)
        $prev = mb_substitute_character();
        mb_substitute_character(0x3F);
        $out = mb_convert_encoding($s, 'UTF-8', 'UTF-8');
        mb_substitute_character($prev);
        return $out;
    }

    /** Nettoie les champs texte d'une liste de lignes (résultats du query builder). */
    public static function cleanRows($rows)
    {
        foreach ($rows as $row) {
            foreach ($row as $k => $v) {
                if (is_string($v)) $row->$k = self::clean($v);
            }
        }
        return $rows;
    }
}
