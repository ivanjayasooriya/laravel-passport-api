# Laravel Passport API

A JSON API built with Laravel 13 and Laravel Passport. It provides user registration and login, bearer-token authentication, post management, and an admin-only user listing.

## Requirements

- PHP 8.3 or later
- Composer
- Node.js and npm

The default database configuration uses SQLite.

## Setup

From the project directory, run the Composer setup script. It installs PHP and frontend dependencies, creates `.env` if needed, generates the application key, runs migrations, and builds the frontend assets.

```bash
composer run setup
```

Generate Passport's encryption keys and create the personal access client used to issue API tokens:

```bash
php artisan passport:keys
php artisan passport:client --personal --name="Personal Access Client"
```

Start the local server:

```bash
php artisan serve
```

The API is available at `http://localhost:8000/api`. Configure the database and other environment-specific settings in `.env` before running the setup script when needed.

## Authentication

Register or log in to receive a Passport access token. Include it on protected requests as a bearer token:

```http
Authorization: Bearer <access-token>
Accept: application/json
```

New accounts receive the `user` role. Admin access must be assigned through a trusted administrative process; there is no public role-management endpoint.

## API endpoints

All endpoints use the `/api` prefix. Requests and responses use JSON.

### Authentication

| Method | Endpoint | Access | Description |
| --- | --- | --- | --- |
| `POST` | `/register` | Public | Create an account and return a token |
| `POST` | `/login` | Public | Authenticate and return a token |
| `GET` | `/profile` | Authenticated | Return the current user's profile |
| `POST` | `/logout` | Authenticated | Revoke the current access token |

Registration requires `name`, a unique valid `email`, and a `password` of at least 8 characters. Login requires a valid `email` and `password`.

Example registration request:

```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "password123"
}
```

Successful registration returns HTTP `201`; login returns HTTP `200`. Both responses include the user and an access token under `data`.

### Posts

All post endpoints require authentication. Every authenticated user can list and view posts. Users can update or delete their own posts; admins can update or delete any post.

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/posts` | List posts |
| `POST` | `/posts` | Create a post |
| `GET` | `/posts/{id}` | View a post |
| `PUT`, `PATCH` | `/posts/{id}` | Update a post |
| `DELETE` | `/posts/{id}` | Delete a post |

Creating a post requires `title` (up to 255 characters) and `content`. Updates accept either or both fields.

Example:

```http
POST /api/posts
Authorization: Bearer <access-token>
Accept: application/json
Content-Type: application/json
```

```json
{
  "title": "First post",
  "content": "Hello from the API."
}
```

### Administration

| Method | Endpoint | Access | Description |
| --- | --- | --- | --- |
| `GET` | `/admin/users` | Admin | List user profiles |

Requests from unauthenticated users receive `401`; non-admin users attempting admin-only actions receive `403`. Invalid input returns `422`, and requests for posts that do not exist return `404`.

## Tests

Run the test suite with:

```bash
composer test
```

## Security

Do not commit `.env`, Passport private keys, or production credentials. Keep generated keys and access tokens private.
