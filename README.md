# FluxBB — Laravel + MongoDB Demo

A faithful recreation of the [FluxBB](https://fluxbb.org) forum software built with **Laravel 13** and **MongoDB**, demonstrating how to build a real-world application using [`mongodb/laravel-mongodb`](https://github.com/mongodb/laravel-mongodb) v5.

## Features

- **Forum browsing** — categories, forums, topics, posts with sticky/closed states
- **Authentication** — register, login by username or email, logout
- **Posting** — create topics, reply, edit and delete posts with BBCode support
- **User profiles** — view and edit profile, signature, post history
- **Subscriptions** — subscribe/unsubscribe to forums and topics
- **Search** — full-text search across topics and posts
- **Moderation** — close, open, stick, unstick, move and delete topics
- **Admin panel** — manage users, forums, categories, groups, bans, censoring, settings

## MongoDB features showcased

| Feature | Where |
|---------|-------|
| Eloquent models with MongoDB | All models in `app/Models/` |
| `hasMany` / `belongsTo` relations | `Topic::posts()`, `Post::topic()`, `Post::forum()` |
| Embedded documents | `Forum::last_post`, `Topic::last_post` (stored as arrays) |
| Singleton document pattern | `ForumConfig::instance()` |
| `increment()` / `decrement()` | Forum/topic/user post counters |
| Regex queries | `SearchController` (replaced by Atlas Search on atlas-local) |

## Requirements

- PHP 8.3+
- Composer
- Docker & Docker Compose (for the MongoDB Atlas Local container)

## Installation

```bash
# 1. Clone and install dependencies
git clone https://github.com/GromNaN/laravel-mongodb-demo.git fluxbb
cd fluxbb
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Start MongoDB Atlas Local (includes mongot for Atlas Search)
docker compose up -d

# 4. Seed the database
php artisan db:seed

# 5. Create Atlas Search indexes
php artisan search:create-indexes

# 6. Start the development server
php artisan serve
```

The application is then available at `http://localhost:8000`.

Default admin credentials: `admin` / `adminpass`

> **Note:** `docker compose up -d` starts [`mongodb/mongodb-atlas-local`](https://www.mongodb.com/docs/atlas/cli/current/atlas-cli-local-cloud/), a local MongoDB instance bundled with the Atlas Search engine (mongot). This enables full-text search on the `/search` route. Without it, the application falls back to regex-based search automatically.

## Running tests

```bash
php artisan test --compact
```

The test suite runs against a separate `fluxbb_testing` MongoDB database and truncates collections between each test.

## Project structure

```
app/
├── Http/
│   ├── Controllers/         # Public controllers (forum, topic, post, auth…)
│   │   └── Admin/           # Admin panel controllers
│   ├── Middleware/
│   │   └── TrackOnlineUser  # Upserts online presence per request
│   └── Requests/            # Form request validation
├── Models/
│   ├── Category.php         # Forum categories
│   ├── Forum.php            # Forums (hasMany topics)
│   ├── Topic.php            # Topics (hasMany posts)
│   ├── Post.php             # Posts (belongsTo topic, forum, user)
│   ├── User.php             # Users with group-based permissions
│   ├── Group.php            # Permission groups (admin, moderator, member, guest)
│   ├── ForumConfig.php      # Singleton config document
│   ├── Ban.php              # IP / username / email bans
│   ├── Report.php           # Reported posts
│   ├── OnlineUser.php       # Online users tracker
│   └── Censor.php           # Word censoring rules
database/
├── seeders/
│   ├── DatabaseSeeder.php
│   ├── GroupSeeder.php
│   ├── ForumConfigSeeder.php
│   ├── CategorySeeder.php
│   ├── AdminUserSeeder.php
│   └── TestDataSeeder.php   # ~30 posts of realistic test data
tests/
└── Feature/                 # 87 feature tests covering all routes
```

## License

MIT
