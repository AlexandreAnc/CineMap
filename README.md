<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## CineMap — formatage du code (Laravel Pint)

Avant chaque rendu, formate le code PHP (style **PSR-12** + règles **Laravel**) :

```bash
./vendor/bin/pint
```

Équivalents : `composer run pint` ou `composer run format`.

Dans ce dépôt, Pint s’applique via le fichier `pint.json` (préréglage `laravel`).

Laravel Pint s’appuie sur [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) : il unifie les espaces, l’ordre des `use`, l’accolade, etc. pour garder le projet cohérent. Ce n’est pas le même outil qu’un linter qui signale des bugs, c’est un **formateur** (correction automatique du style).

- [Documentation officielle Pint](https://laravel.com/docs/pint)

## CineMap — serveur MCP (lecture seule, étape 9)

- API Laravel (token `MCP_READ_TOKEN`) : `GET /api/mcp/films`, `GET /api/mcp/films/{id}/locations` (cf. `config/mcp.php`).
- Serveur MCP Node (outils `list_films`, `get_locations_for_film`) : dossier `mcp/cinemap-server/` (installer avec `npm install` dans ce dossier). Voir le **README** détaillé : `mcp/cinemap-server/README.md`.

## CineMap — connexion Google (OAuth)

- Paquet : [Laravel Socialite](https://laravel.com/docs/socialite) (`composer require laravel/socialite`).
- Variables d’environnement : `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET` (voir `.env.example`). Renseigne aussi l’**URI de redirection** dans [Google Cloud Console](https://console.cloud.google.com/) → APIs & services → identifiants → client OAuth 2.0 (type *Application Web*) :  
  `http://127.0.0.1:8000/auth/google/callback` (adapter host/port si besoin, ou `GOOGLE_REDIRECT_URI` dans `.env`).
- Routes : `GET /auth/google` (redirection Google), `GET /auth/google/callback` (retour, création ou reprise de compte).

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
