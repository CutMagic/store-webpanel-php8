<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * [multimod] Tableaux (DataTables) : refuse toute colonne demandée par le navigateur qui n'est pas prévue.
     * Les noms étaient déjà protégés contre l'injection SQL ; ceci évite en plus les erreurs et les fuites
     * d'informations quand quelqu'un envoie des noms de colonnes fabriqués.
     */
    protected function onlyColumns(array $allowed)
    {
        foreach ((array) request()->input('columns', []) as $column) {
            foreach (['data', 'name'] as $key) {
                $value = is_array($column) ? ($column[$key] ?? '') : '';
                if ($value !== '' && $value !== null && !in_array((string) $value, $allowed, true)) {
                    abort(400, 'Colonne refusée');
                }
            }
        }
    }
}
