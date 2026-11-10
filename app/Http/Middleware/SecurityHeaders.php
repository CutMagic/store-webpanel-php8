<?php

namespace App\Http\Middleware;

use Closure;

/**
 * [multimod] En-têtes de sécurité sur toutes les pages :
 * - X-Frame-Options : le panneau ne peut pas être affiché dans un cadre sur un autre site (anti "clickjacking"
 *   sur les boutons Acheter / Vendre / Supprimer) ;
 * - X-Content-Type-Options : le navigateur ne devine pas le type d'un fichier ;
 * - Referrer-Policy : l'adresse complète (avec paramètres) n'est pas envoyée aux autres sites.
 */
class SecurityHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'same-origin');
        return $response;
    }
}
