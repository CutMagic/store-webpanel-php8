<?php

namespace App\Support;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * [php82] Vérifie depuis le web que les fichiers privés du panneau ne sont PAS lisibles :
 * .env (mots de passe), storage/ (sessions, logs) et composer.json (fichiers du projet).
 * Utilisé à la fin de l'installation. Le site s'appelle lui-même avec l'adresse de la requête.
 *
 * Résultat par fichier : 'ok' (refusé), 'danger' (lisible), 'unknown' (vérification impossible,
 * par exemple si l'hébergeur bloque les requêtes du site vers lui-même).
 */
class ExposureCheck
{
    public static function rootUrl(Request $request): string
    {
        $base = rtrim($request->getSchemeAndHttpHost() . $request->getBasePath(), '/');
        // site appelé par .../public : la racine du panneau est le dossier au-dessus
        return preg_replace('#/public$#', '', $base);
    }

    public static function run(string $root): array
    {
        $root = rtrim($root, '/');
        $canaryName = 'webpanel-check-' . bin2hex(random_bytes(8)) . '.txt';
        $canaryPath = storage_path('framework/' . $canaryName);
        $canaryText = 'webpanel-check-' . bin2hex(random_bytes(16));
        $canaryWritten = @file_put_contents($canaryPath, $canaryText) !== false;

        $targets = [
            'env' => ['.env', $root . '/.env', fn ($body) => str_contains($body, 'APP_KEY=')],
            'storage' => ['storage/', $root . '/storage/framework/' . $canaryName, fn ($body) => str_contains($body, $canaryText)],
            'composer' => ['composer.json', $root . '/composer.json', fn ($body) => str_contains($body, '"require"')],
        ];
        if (!$canaryWritten) {
            unset($targets['storage']);
        }

        try {
            $responses = Http::pool(function (Pool $pool) use ($targets) {
                $out = [];
                foreach ($targets as $key => [, $url]) {
                    $out[] = $pool->as($key)->timeout(6)->withHeaders(['Cache-Control' => 'no-cache'])->get($url);
                }
                return $out;
            });
        } catch (\Throwable $e) {
            $responses = [];
        }

        $results = [];
        foreach ($targets as $key => [$label, $url, $leaks]) {
            $resp = $responses[$key] ?? null;
            if ($resp instanceof \Illuminate\Http\Client\Response) {
                $exposed = $resp->successful() && $leaks($resp->body());
                $results[] = ['label' => $label, 'url' => $url, 'status' => $exposed ? 'danger' : 'ok', 'http' => $resp->status()];
            } else {
                $results[] = ['label' => $label, 'url' => $url, 'status' => 'unknown', 'http' => null];
            }
        }

        if ($canaryWritten) {
            @unlink($canaryPath);
        }
        return $results;
    }
}
