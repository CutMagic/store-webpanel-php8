# Store WebPanel – PHP 8

Panneau web pour le plugin SourceMod **Store 1.2 Final (Drixevel)** sur Counter-Strike: Source :
administration du store (objets, catégories, joueurs, crédits, serveurs) et **espace joueur** avec
connexion Steam (crédits, inventaire, boutique, objets équipés).

*Web panel for the SourceMod **Store 1.2 Final** plugin (Drixevel): admin panel and player area with
Steam login. Based on SourceMod-Store WebPanel-Core 2.0.1, rewritten for the Store 1.2 schema and
ported to Laravel 12 / PHP 8.2. Documentation below is in French.*

Basé sur [SourceMod-Store WebPanel-Core](https://github.com/SourceMod-Store/WebPanel-Core) 2.0.1
(Werner Maisl, AGPLv3), réécrit en partie pour le schéma du Store 1.2 et porté de Laravel 5.1 vers
**Laravel 12**. Les modifications sont marquées `[multimod]` et `[php82]` dans le code.

## Ce qu'il fait

- **Administration** : objets, catégories, joueurs et crédits, serveurs, rôles et permissions des
  comptes admin, import d'objets en JSON.
- **Espace joueur** (`/userpanel`, en français) : tableau de bord (crédits, rang, objets), inventaire
  avec revente, boutique, objets équipés par loadout (un objet par emplacement, comme le plugin).
- **Connexion joueur par Steam OpenID** : pas besoin de clé API Steam.
- **Deux bases** (ou une seule) : la base du **panneau** (tables `webpanel_*`, comptes admin, rôles,
  serveurs) et la base du **store** (tables `store_*` du plugin). Le panneau lit et modifie les données
  du store, mais ne change jamais sa structure.
- **Mot de passe oublié** pour les comptes admin : lien à usage unique envoyé par mail (SMTP).

## Prérequis

- PHP **8.2** ou plus récent (8.3, 8.4) avec les extensions `pdo_mysql`, `mbstring`, `openssl`,
  `tokenizer`, `xml`, `ctype`, `fileinfo`, `curl`.
- MySQL / MariaDB, avec la base du plugin Store 1.2.
- Le plugin SourceMod Store 1.2 sur le ou les serveurs de jeu.

## Installation

### Avec le ZIP prêt à installer (sans Composer)

1. Télécharger `store-webpanel-php8-*.zip` dans la page **[Releases](../../releases)** et le décompresser.
2. Copier `.env.example` en `.env` et le remplir : remplacer `base-name`, `user-name`, `xxxxxxx` et
   `localhost` par tes valeurs (bases, utilisateur MySQL, mots de passe, SMTP), et `APP_URL` par
   l'adresse exacte du site. Si le site est en https (conseillé), mettre `SESSION_SECURE_COOKIE=true`.
3. Générer la clé du site (**obligatoire**) : `php artisan key:generate --force`
   Sans accès SSH chez l'hébergeur, le faire sur ton PC avec PHP avant d'envoyer les fichiers.
   Chaque site doit avoir **sa** clé : ne jamais reprendre celle d'un autre.
4. Envoyer le dossier sur l'hébergement. La racine web du (sous-)domaine doit pointer sur le dossier
   `public`. Si ce n'est pas possible, vérifier que `.env` n'est pas lisible depuis le web.
5. Donner les droits d'écriture sur `storage/` et `bootstrap/cache/`.
6. Créer les tables du panneau : `php artisan migrate --force` (ne crée que les tables `webpanel_*`).
   Le premier compte admin reçoit un mot de passe tiré au hasard, écrit dans `storage/app/premier-admin.txt`.
   L'installateur web `/installer` fait la même chose depuis le navigateur.
7. Créer le fichier `INSTALLED` à la racine du panneau (contenu : `INSTALLED`).
   **Tant qu'il n'existe pas, n'importe qui peut ouvrir `/installer` et réécrire ton `.env`.**
8. Se connecter sur `/login` avec le compte de `premier-admin.txt`, puis dans *Panel > Users* : mettre ta
   vraie adresse e-mail et un mot de passe solide. Supprimer ensuite `storage/app/premier-admin.txt`.
9. *Store > Servers* : créer chaque serveur de jeu avec le même ID que son `server_id` dans
   `configs/store/core.cfg` du Store.

Conseillé une fois en ligne : `php artisan config:cache` et `php artisan route:cache`
(à refaire après chaque modification du `.env`).

### Depuis les sources (avec Composer)

```bash
git clone https://github.com/cutmagic/store-webpanel-php8.git
cd store-webpanel-php8
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate --force
```

Puis reprendre à l'étape 2 ci-dessus (remplir le `.env`), et les étapes 4 à 9.

## Mails (« mot de passe oublié » des comptes admin)

Les joueurs n'ont pas de mot de passe (connexion Steam). Pour les admins, le lien « Mot de passe
oublié ? » de `/login` envoie un **lien** (jamais le mot de passe), valable 60 minutes et une seule fois.

- Régler le SMTP de ton hébergeur dans le `.env` (`MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`,
  `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`). Port 465 = SSL, 587 = STARTTLS.
- `MAIL_FROM_ADDRESS` doit être une adresse du domaine de ton serveur mail, sinon les mails partent en spam.
- Le lien est construit avec `APP_URL` : il doit être l'adresse exacte du site.
- Pour tester sans envoyer : `MAIL_MAILER=log` (le mail est écrit dans `storage/logs/`).

## Sécurité

Testé par des tests d'attaque sur une copie locale d'une base réelle :

- **Injection SQL** : aucune (requêtes préparées ; colonnes des tableaux limitées à une liste).
- **XSS** : toutes les sorties échappées, y compris pseudos et noms d'objets.
- **Contrôle d'accès** : chaque page admin exige sa permission (refus par défaut) ; un joueur ne peut
  agir que sur ses propres objets.
- **CSRF** : jeton obligatoire sur toutes les actions.
- **Steam OpenID** : adresse de retour, serveur Steam, champs signés, anti-rejeu, revérification auprès de Steam.
- **Connexion admin** : 5 essais ratés max par minute ; pas d'inscription libre.
- **Mot de passe oublié** : lien non détournable (en-tête Host ignoré), jeton haché, même réponse que
  l'adresse existe ou non, anciennes sessions déconnectées après le changement.
- En-têtes `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`.

Failles du WebPanel d'origine corrigées : HTML d'un pseudo exécuté chez l'admin ; import permettant de
lire ou supprimer un fichier du site ; import et modifications ouverts à tout compte admin sans
permission ; connexion Steam acceptant une connexion faite pour un autre site ; mot de passe enregistré
en clair à la création ; hachage du mot de passe réenregistré comme mot de passe à l'édition d'un compte.

Restent à ta charge : HTTPS, `APP_DEBUG=false`, fichier `INSTALLED` présent dès la fin de
l'installation, mots de passe admin solides, `.env` jamais partagé une fois rempli.

**Ne jamais activer** `USERPANEL_DEV_LOGIN` sur un site en ligne (connexion de test comme n'importe quel joueur).

## À savoir

- **Import (Outils)** : importer une catégorie **supprime d'abord** les catégories qui ont le même
  `require_plugin`, avec leurs objets et les exemplaires possédés par les joueurs (comportement d'origine).
- Les tables du Store 1.2 sont en MyISAM (pas de transactions) : chaque opération sur les crédits est
  une seule requête atomique, comme dans le plugin.
- Connexion « store » en latin1, comme le plugin : les pseudos s'affichent comme en jeu.

## Licence

**GNU Affero General Public License v3** (voir [LICENSE](LICENSE)), comme le WebPanel-Core d'origine
de Werner Maisl. Si tu modifies ce panneau et le mets en ligne, tu dois proposer ton code modifié
aux utilisateurs du site. Laravel et les bibliothèques de `vendor/` gardent leurs propres licences (MIT pour la plupart).
