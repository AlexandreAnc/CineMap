# TP Laravel - CineMap

Application Laravel réalisée dans le cadre du TP : gestion de films et d'emplacements, avec authentification, rôles admin, upvotes via job, commande planifiée, OAuth Google, abonnement Stripe, API JWT protégée et MCP lecture seule.

## Contexte

Le projet `CineMap` est volontairement simple :
- 2 CRUDs métier principaux : `Film` et `Location`
- 1 fonctionnalité transverse sans CRUD complet : les upvotes sur les emplacements
- interface sobre (Blade), validations serveur, flux complet fonctionnel

## Objectifs pédagogiques couverts

- authentification Laravel
- CRUDs standards
- middleware personnalisé admin
- queues + jobs
- commande Artisan personnalisée + scheduler
- formatage Laravel Pint
- connexion OAuth (Google)
- abonnement Stripe (test) + route API JSON protégée
- intégration MCP lecture seule (`list_films`, `get_locations_for_film`)

## Stack et dépendances

- projet initialisé à partir du template de base officiel Laravel (`laravel/laravel`)
- PHP `^8.3`
- Laravel `^13`
- Laravel Breeze (auth UI)
- Laravel Cashier (Stripe)
- Socialite (OAuth Google)
- `php-open-source-saver/jwt-auth` (JWT API)
- Queue `database`

## Modèle de données

### Film
- `title`
- `release_year`
- `synopsis`

### Location
- `film_id`
- `user_id`
- `name`
- `city`
- `country`
- `description`
- `photo_path` (optionnel)
- `upvotes_count` (défaut `0`)

### Votes
- table `location_votes` (`user_id`, `location_id`, `created_at`)
- contrainte d'unicité fonctionnelle : un utilisateur ne vote qu'une fois par emplacement (vote toggle)

### Utilisateur
- champs auth Laravel
- `is_admin` (booléen)
- `google_id` (OAuth)

## Fonctionnalités livrées (mapping TP)

1. **Authentification** : inscription, connexion, déconnexion, dashboard protégé.
2. **CRUD Film + CRUD Location** : création/lecture/mise à jour/suppression, rattachement location -> film + user.
3. **Middleware admin** : alias `admin`, protection des routes d'administration (films).
4. **Queues & Jobs** : vote sur location -> `SyncLocationUpvotesCount` dispatché en queue.
5. **Commande + planification** :
   - commande : `locations:prune-stale`
   - règle : supprime les locations de plus de 14 jours avec moins de 2 upvotes
   - scheduler : exécution quotidienne dans `bootstrap/app.php`.
6. **Pint** : formatage standard Laravel.
7. **OAuth Google** : redirection + callback + création/connexion utilisateur.
8. **Stripe + API JWT** :
   - abonnement premium via Checkout Stripe
   - API `/api/films/{film}/locations` protégée par `auth:api` + middleware `subscribed`.
9. **MCP lecture seule** :
   - `list_films`
   - `get_locations_for_film`
   - pont HTTP vers `/api/mcp/films` et `/api/mcp/films/{film}/locations`.

## Installation

```bash
git clone <url-du-repo>
cd my-app
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
```

Puis lancer les assets :

```bash
npm run build
```

En développement (serveur + queue + logs + vite) :

```bash
composer run dev
```

## Configuration `.env` minimale

Variables importantes :

```env
APP_URL=http://127.0.0.1:8000
QUEUE_CONNECTION=database

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=

STRIPE_KEY=
STRIPE_SECRET=
STRIPE_PREMIUM_PRICE=

MCP_READ_TOKEN=
```

Pour JWT :

```bash
php artisan jwt:secret
```

## Lancer les services nécessaires

Terminal 1 (app) :

```bash
php artisan serve
```

Terminal 2 (worker queue) :

```bash
php artisan queue:work
```

Terminal 3 (optionnel, scheduler local) :

```bash
php artisan schedule:work
```

## Tests

Lancer tous les tests :

```bash
php artisan test
```

Les tests couvrent notamment :
- accès API JWT premium/non premium
- vote et logique d'upvotes
- accès route privée par abonnement
- lecture MCP

## Etape 4 - Vérifier les jobs/upvotes

1. Connectez-vous.
2. Ouvrez une fiche d'emplacement et votez.
3. Vérifiez que la table `location_votes` est mise à jour.
4. Vérifiez que `upvotes_count` est recalculé par le job `SyncLocationUpvotesCount`.
5. Assurez-vous que `php artisan queue:work` tourne pendant le test.

## Etape 5 - Commande Artisan + scheduler

Commande manuelle :

```bash
php artisan locations:prune-stale
```

Simulation scheduler :

```bash
php artisan schedule:run
```

Règle métier appliquée :
- `created_at < now() - 14 jours`
- `upvotes_count < 2`

## Etape 6 - Formatage Laravel Pint

Commande attendue avant rendu :

```bash
./vendor/bin/pint
```

Alias possibles :

```bash
composer run pint
composer run format
```

## Etape 7 - OAuth Google

Routes :
- `GET /auth/google`
- `GET /auth/google/callback`

Configuration côté Google Cloud Console :
- créer un OAuth Client Web
- URI de redirection autorisée :
  - `http://127.0.0.1:8000/auth/google/callback`

## Etape 8 - Stripe + JWT API

### 1) Souscription Stripe (mode test)

Configurer :
- `STRIPE_KEY`
- `STRIPE_SECRET`
- `STRIPE_PREMIUM_PRICE` (id du prix Stripe, ex. `price_...`)

Parcours :
- page `/subscribe`
- checkout Stripe
- retour `/subscribe/success`

Carte de test possible :
- `4242 4242 4242 4242`

### 2) Générer un token JWT

Endpoint login API :

```text
POST /api/auth/login
```

Payload :

```json
{
  "email": "user@example.com",
  "password": "password"
}
```

### 3) Appeler l'API protégée

```bash
curl -H "Authorization: Bearer <JWT_TOKEN>" \
  http://127.0.0.1:8000/api/films/1/locations
```

Conditions d'accès :
- JWT valide obligatoire
- abonnement actif obligatoire

## Etape 9 - MCP (lecture seule)

### API Laravel exposée au MCP

- `GET /api/mcp/films`
- `GET /api/mcp/films/{film}/locations`
- protection par token `MCP_READ_TOKEN` (middleware `mcp.read`)

### Serveur MCP Node

Dossier : `mcp/cinemap-server`

Installation :

```bash
cd mcp/cinemap-server
npm install
```

Lancement :

```bash
node index.js
```

Variables d'environnement attendues par le serveur MCP :

```bash
export CINEMAP_MCP_BASE_URL=http://127.0.0.1:8000
export CINEMAP_MCP_READ_TOKEN=<meme_valeur_que_MCP_READ_TOKEN>
```

Outils MCP disponibles :
- `list_films`
- `get_locations_for_film` (argument : `film_id`)

## Mise en production

Le TP est également accessible publiquement en production :
- [https://cinemap.aanc.fr](https://cinemap.aanc.fr)

Choix de déploiement :
- mise en production via un pipeline GitHub Actions simple ;
- serveur web sous Nginx ;
- domaine déployé avec certificat TLS géré via Certbot.

## Routes principales

Web :
- `/dashboard`
- `/films` (+ administration sur create/edit/delete via middleware `admin`)
- `/locations`
- `/subscribe`
- `/prive` (protégée abonnement)

API :
- `POST /api/auth/login`
- `GET /api/films/{film}/locations` (JWT + abonnement)
- `GET /api/mcp/films` (token MCP)
- `GET /api/mcp/films/{film}/locations` (token MCP)

## Contraintes générales respectées

- interface HTML simple (Blade)
- peu de JS, logique principalement serveur
- validations serveur sur formulaires
- migrations dédiées et claires
- pas de CRUD complet pour les votes
- application exécutable de bout en bout

## Checklist de rendu

- [x] code source complet
- [x] migrations
- [x] modèles / contrôleurs / middleware / jobs / commandes / routes
- [x] README d'installation et d'exécution
- [x] instructions worker queue
- [x] instructions scheduler
- [x] instructions OAuth
- [x] instructions Stripe
- [x] instructions JWT
- [x] instructions MCP
