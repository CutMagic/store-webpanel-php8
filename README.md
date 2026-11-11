# Store WebPanel – PHP 8

Panneau web pour le plugin SourceMod **Store 1.2 Final (Drixevel)** sur Counter-Strike: Source :
administration du store (objets, catégories, joueurs, crédits, serveurs) et **espace joueur** avec
connexion Steam (crédits, inventaire, boutique, objets équipés).

> **English:** installation instructions are in [INSTALL.txt](INSTALL.txt).
> Fixed security issues (FR/EN): [SECURITY-FIXES.txt](SECURITY-FIXES.txt).

Basé sur [SourceMod-Store WebPanel-Core](https://github.com/SourceMod-Store/WebPanel-Core) 2.0.1
(Werner Maisl, AGPLv3), réécrit en partie pour le schéma du Store 1.2 et porté de Laravel 5.1 vers
**Laravel 12**. Les modifications sont marquées `[multimod]` et `[php82]` dans le code.
Liste des failles corrigées : [SECURITY-FIXES.txt](SECURITY-FIXES.txt).

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
- **Installateur web** (`/installer`) : marche sans accès SSH et vérifie à la fin que `.env` n'est pas
  lisible depuis le web.

## Prérequis

- PHP **8.2** ou plus récent (8.3, 8.4) avec les extensions `pdo_mysql`, `mbstring`, `openssl`,
  `tokenizer`, `xml`, `ctype`, `fileinfo`, `curl`.
- MySQL / MariaDB, avec la base du plugin Store 1.2 (base neuve : `sql/store-tables-vierges.sql`
  dans `plugin-sourcemod-store.zip` crée les tables vides).
- Le plugin SourceMod Store 1.2 sur le ou les serveurs de jeu (fourni dans les Releases, voir plus bas).

## Installation

### 1. Envoyer les fichiers

Télécharger `store-webpanel-php8-*.zip` dans la page **[Releases](../../releases)** (dossier `vendor`
compris, pas besoin de Composer) et l'envoyer sur l'hébergement. Trois possibilités, de la meilleure à
la moins bonne :

1. **Le (sous-)domaine pointe sur le dossier `public`** (ex. `store.monsite.fr` → `.../store/public`).
   Seul le contenu de `public` est visible depuis le web : c'est le plus sûr.
2. **Tout le dossier dans le dossier web** (ex. `public_html/store`) sur un hébergeur **Apache ou
   LiteSpeed** (la plupart des hébergeurs mutualisés) : le fichier **`.htaccess` de la racine** du
   panneau envoie tout vers `public` (l'adresse reste `monsite.fr/store`) et bloque `.env`, `storage`,
   `vendor`... Le site marche directement, sans `/public` dans l'adresse.
3. **Nginx** : les `.htaccess` sont ignorés. Faire pointer la racine du site sur `public` (possibilité 1).

> ⚠️ **Si tu modifies le `.htaccess` de la racine, garde sa partie 1 (« PROTECTION »).**
> Sans elle, ton `.env` (mots de passe MySQL et SMTP) peut être lisible par tout le monde à l'adresse
> `monsite.fr/store/.env`. Le fichier `index.php` de la racine ne sert que de secours : si la
> réécriture d'adresse ne marche plus, il renvoie les visiteurs vers `public/`, mais il ne protège
> **pas** `.env`. Ne supprime pas non plus `storage/.htaccess`.

Donner les droits d'écriture au serveur web sur le dossier du panneau (pour créer `.env` et `INSTALLED`),
sur `storage/` et sur `bootstrap/cache/`.

### 2. Installateur web (sans SSH)

Ouvrir `https://monsite.fr/store/installer` **juste après l'envoi des fichiers** (tant que l'installation
n'est pas finie, n'importe qui peut ouvrir l'installateur).

- À la première visite, le panneau crée lui-même `.env` (depuis `.env.example`) et sa clé de chiffrement unique.
- Remplir la base du panneau, la base du store et le SMTP. Les deux bases peuvent être la même
  (les préfixes `webpanel_` et `store_` les séparent).
- L'installateur crée les tables du panneau (`webpanel_*` seulement) et le premier compte admin, avec un
  mot de passe tiré au hasard écrit dans `storage/app/premier-admin.txt`.
- À la fin, il crée le fichier `INSTALLED` (l'installateur est alors fermé pour toujours) et **vérifie
  depuis le web que `.env`, `storage` et `composer.json` ne sont pas lisibles**. En cas d'alerte rouge,
  corriger tout de suite (voir l'étape 1) puis changer les mots de passe MySQL et SMTP.
  Si l'hébergeur empêche le site de s'appeler lui-même, la page donne les liens à ouvrir à la main :
  chacun doit afficher une erreur 403 ou 404.

### 3. Après l'installation

1. Se connecter sur `/login` avec le compte de `storage/app/premier-admin.txt`.
2. *Panel > Users* : mettre ta vraie adresse e-mail (le « mot de passe oublié » l'utilise) et un mot de
   passe solide. Supprimer ensuite `storage/app/premier-admin.txt`.
3. *Store > Servers* : créer chaque serveur de jeu avec le même ID que son `server_id` dans
   `configs/store/core.cfg` du Store.
4. Si le site est en https, vérifier `SESSION_SECURE_COOKIE=true` dans `.env` (l'installateur le met
   tout seul quand il est ouvert en https).

### Installation à la main (avec SSH / Composer)

```bash
git clone https://github.com/cutmagic/store-webpanel-php8.git
cd store-webpanel-php8
composer install --no-dev --optimize-autoloader
cp .env.example .env            # puis remplir base-name, user-name, xxxxxxx, localhost, APP_URL
php artisan key:generate --force
php artisan migrate --force     # tables webpanel_* + premier compte admin
echo INSTALLED > INSTALLED      # ferme l'installateur web
```

Conseillé une fois en ligne : `php artisan config:cache` et `php artisan route:cache`
(à refaire après chaque modification du `.env`).

## Mails (« mot de passe oublié » des comptes admin)

Les joueurs n'ont pas de mot de passe (connexion Steam). Pour les admins, le lien « Mot de passe
oublié ? » de `/login` envoie un **lien** (jamais le mot de passe), valable 60 minutes et une seule fois.

- SMTP de ton hébergeur dans le `.env` (`MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`,
  `MAIL_FROM_ADDRESS`). Port 465 = SSL, 587 = STARTTLS.
- `MAIL_FROM_ADDRESS` doit être une adresse du domaine de ton serveur mail, sinon les mails partent en spam.
- Le lien est construit avec `APP_URL` : il doit être l'adresse exacte du site.
- Pour tester sans envoyer : `MAIL_MAILER=log` (le mail est écrit dans `storage/logs/`).

## Sécurité

Testé par des tests d'attaque sur une copie locale d'une base réelle : injection SQL, XSS, contrôle
d'accès, CSRF, fausses réponses Steam, upload de script, mot de passe oublié, fichiers privés lisibles.
Détail des failles du WebPanel d'origine corrigées : [SECURITY-FIXES.txt](SECURITY-FIXES.txt).

Restent à ta charge : HTTPS, `APP_DEBUG=false`, installation finie juste après l'envoi des fichiers,
mots de passe admin solides, `.env` jamais partagé une fois rempli.

**Ne jamais activer** `USERPANEL_DEV_LOGIN` sur un site en ligne (connexion de test comme n'importe quel joueur).

## À savoir

- **Import (Outils)** : importer une catégorie **supprime d'abord** les catégories qui ont le même
  `require_plugin`, avec leurs objets et les exemplaires possédés par les joueurs (comportement d'origine).
- Les tables du Store 1.2 sont en MyISAM (pas de transactions) : chaque opération sur les crédits est
  une seule requête atomique, comme dans le plugin.
- Connexion « store » en latin1, comme le plugin : les pseudos s'affichent comme en jeu.

## Plugin SourceMod et packs (page Releases)

- `plugin-sourcemod-store.zip` : les plugins SourceMod du store (`.smx` et leurs sources), configs,
  traduction française complète et tables SQL vierges (`sql/store-tables-vierges.sql`).
- `pack-skin-joueur.zip` : skins de joueur (modèles, matériaux et SQL d'import).
- `pack-objets-store.zip` : trails, grenades, traceurs, couleurs d'armes... (fichiers et SQL d'import).

**Origine du store** : [Store 1.2 Final de Drixevel](https://github.com/Drixevel/store/tree/1.2-Final)
(Keith Warren, aujourd'hui [KeithGDR](https://github.com/KeithGDR) sur GitHub), suite du
[Store d'alongub](https://github.com/alongubkin/store) (licence GPLv3).
Notre version est **mise à jour pour SourceMod 1.12** :

- modules d'objets adaptés et recompilés avec SourceMod 1.12, et passés de l'ancienne extension
  SMJansson à l'extension [Jansson in Pawn](https://github.com/rejchev/sm-jansson) (**obligatoire**) ;
- cœur du store : 2 petites corrections (requête compatible MySQL `ONLY_FULL_GROUP_BY`, syntaxe `view_as`) ;
- traduction française complète, tables SQL vierges.

> [KeithGDR/sm-store](https://github.com/KeithGDR/sm-store) (2025, même auteur) est une **autre version**
> du store, avec d'autres noms de plugins et de phrases : ne pas mélanger ses fichiers avec ce pack.

## Licence

**GNU Affero General Public License v3** (voir [LICENSE](LICENSE)), comme le WebPanel-Core d'origine
de Werner Maisl. Si tu modifies ce panneau et le mets en ligne, tu dois proposer ton code modifié
aux utilisateurs du site. Laravel et les bibliothèques de `vendor/` gardent leurs propres licences (MIT pour la plupart).
