<?php
/*
 * [php82] Préparation de chaque requête web, AVANT Laravel (inclus par public/index.php).
 *
 * 1. Adresses correctes quand le site est appelé par la racine du panneau
 *    (.htaccess de la racine qui renvoie vers public/ sans changer l'adresse) :
 *    PHP voit le script /store/public/index.php alors que l'adresse est /store/... ;
 *    on dit à Laravel que le site est à /store, sinon ses liens oublieraient /store.
 *
 * 2. Première visite (fichier INSTALLED absent) : crée .env depuis .env.example et génère la
 *    clé APP_KEY si elle est vide. L'installateur /installer marche donc sans accès SSH.
 *    Une fois l'installation finie (INSTALLED présent), ce bloc ne fait plus rien.
 */

(function () {
    // ---- 1. site appelé par la racine du panneau ----
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $publicDir = rtrim(dirname($script), '/');           // ex. /store/public
    if (basename($script) === 'index.php' && preg_match('#^(.*)/public$#', $publicDir, $m)) {
        $appDir = $m[1];                                  // ex. /store  (ou '' à la racine du domaine)
        $viaPublic = strpos($uri . '/', $publicDir . '/') === 0;
        $underApp = $appDir === '' || strpos($uri . '/', $appDir . '/') === 0;
        if (!$viaPublic && $underApp) {
            $_SERVER['SCRIPT_NAME'] = $appDir . '/index.php';
            $_SERVER['PHP_SELF'] = $appDir . '/index.php';
        }
    }

    // ---- 2. première visite : .env et clé ----
    $root = dirname(__DIR__);
    if (is_file($root . '/INSTALLED')) {
        return;
    }
    $env = $root . '/.env';
    if (!is_file($env) && is_file($root . '/.env.example')) {
        @copy($root . '/.env.example', $env);
    }
    $content = is_file($env) ? @file_get_contents($env) : false;
    if ($content !== false && !preg_match('/^APP_KEY=[ \t]*("[ \t]*")?[ \t]*\r?$/m', $content)) {
        return; // la clé existe déjà
    }
    if ($content !== false) {
        $key = 'base64:' . base64_encode(random_bytes(32));
        $new = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $content, 1);
        if (@file_put_contents($env, $new, LOCK_EX) !== false) {
            return;
        }
    }

    // impossible d'écrire : on explique au lieu d'une erreur Laravel incompréhensible
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="utf-8"><title>Store WebPanel - installation</title></head><body>'
        . '<h1>Store WebPanel : installation impossible pour l\'instant</h1>'
        . '<p>Le panneau n\'a pas pu créer son fichier <code>.env</code> et sa clé de chiffrement.</p>'
        . '<p>Au choix :</p><ul>'
        . '<li>donne les droits d\'écriture au serveur web sur le dossier du panneau, puis recharge cette page ;</li>'
        . '<li>ou copie <code>.env.example</code> en <code>.env</code> et lance <code>php artisan key:generate --force</code>.</li>'
        . '</ul></body></html>';
    exit;
})();
