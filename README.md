# Module 4 – Activity 4.1: Create and Secure a REST API

**Course:** IT 255 – Web Systems, Frameworks, and Technologies (MSIT, NEUST)
**Student:** Kristel Joy D. Salinas

A Laravel 12 REST API connected to MySQL (XAMPP, database `testdb`), documented with Swagger (L5 Swagger) and tested with Postman.

## Endpoints

| Method | URL | Access | Description |
|---|---|---|---|
| GET | `/api/users` | Public | List all users (passwords hidden) |
| POST | `/api/users` | Requires `X-API-KEY` header | Create a user (`name`, `email`, `password`) |

Swagger UI: http://127.0.0.1:8000/api/documentation

## Security measures

- **Validation:** `name` and `email` are required, `email` must be valid and unique, and `password` needs at least 8 characters. Invalid input returns `422` with JSON error messages.
- **API key:** `POST /api/users` requires an `X-API-KEY` header that matches `API_KEY` in `.env`, otherwise `401`. The key is checked with `hash_equals` and is never committed (`.env` is git-ignored).
- **Password hashing:** passwords are stored with bcrypt (`'password' => 'hashed'` cast), never in plain text.
- **Hidden fields:** `password` and `remember_token` are never returned in JSON.
- **Mass-assignment protection:** only `name`, `email` and `password` can be filled, and only validated fields are saved.
- **Rate limiting:** at most 60 requests per minute per client (`throttle:60,1`), otherwise `429`.
- **SQL injection protection:** Eloquent uses prepared statements.

## Run locally

```
composer install
copy .env.example .env
php artisan key:generate
```

Set the database in `.env` (`DB_CONNECTION=mysql`, `DB_DATABASE=testdb`, `DB_USERNAME=root`, `DB_PASSWORD=`) and add `API_KEY=your-key`, then:

```
php artisan migrate
php artisan l5-swagger:generate
php artisan serve
```

## Files changed from the Laravel skeleton

- `app/Http/Controllers/Controller.php` – OpenAPI info, server and API-key security scheme
- `app/Http/Controllers/UserController.php` – `index` and `store` with validation and Swagger docs
- `app/Http/Middleware/EnsureApiKey.php` – API key check
- `app/Models/User.php` – fillable, hidden and hashed fields, plus the Swagger `User` schema
- `routes/api.php` – routes with rate limiting and the API key middleware
