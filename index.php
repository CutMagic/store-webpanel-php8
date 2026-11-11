<?php
/*
 * Store WebPanel - index.php de SECOURS à la racine du panneau.
 *
 * Normalement on n'arrive jamais ici : le .htaccess de la racine envoie tout vers public/,
 * ou le (sous-)domaine pointe directement sur public/.
 * Si ce fichier s'exécute, c'est que la réécriture d'adresse ne marche pas (mod_rewrite absent,
 * .htaccess modifié ou ignoré, serveur Nginx...). On envoie alors le visiteur vers public/,
 * pour que le site reste utilisable, et on ne montre rien d'autre.
 *
 * Pense à vérifier que ton .env n'est pas lisible : ouvre  <adresse du site>/.env  dans ton
 * navigateur, tu dois avoir une erreur 403 ou 404. Voir le README (partie "Hébergement").
 */

$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');

header('Cache-Control: no-store');
header('Location: ' . $base . '/public/', true, 302);
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"><title>Store WebPanel</title></head>
<body><p><a href="<?= htmlspecialchars($base . '/public/', ENT_QUOTES) ?>">Continuer vers le panneau</a></p></body>
</html>
