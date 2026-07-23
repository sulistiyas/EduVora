# AGENTS.md

## Stack

- Laravel 12 / PHP 8.2+ / PostgreSQL (SQLite :memory: for tests)
- Pest 3 for testing, Laravel Pint for formatting (default config)
- Vite 7 + Tailwind CSS v4 + Alpine.js + SweetAlert2
- Scramble for API docs (generates from `routes/web.php`)

## Commands

```bash
composer dev          # artisan serve + queue:listen + npm run dev concurrently
composer test         # clears config cache then runs `php artisan test`
composer setup        # full first-time: install, env, migrate, npm build
npm run dev           # Vite dev server
npm run build         # Vite production build
```

## Architecture

**Multi-tenant SaaS** — every data query must scope to the authenticated user's `school_id`.

```
Controller → Service → Repository → Eloquent Model
```

- **Repositories** enforce school scoping via `HasSchoolScope` trait (`getAuthSchoolId()`)
- **Services** are thin pass-throughs with business logic
- **Controllers** return JSON for AJAX (`$request->expectsJson()`) or Blade views
- **FormRequest** classes handle validation for store/update operations

### Directory layout

- `app/Models/{Domain}/` — Academic, Activity, Asset, Communication, Core, Exam, Finance, Library, Student, Teacher
- `app/Http/Controllers/{Module}/` — Academic, Auth, Class, School, Student, Teacher
- `app/Repositories/` + `app/Services/` — same domain split as models
- `app/Http/Requests/` — FormRequest classes for validation
- `app/Concerns/HasSchoolScope.php` — shared trait for school scoping
- `app/Exports/` — Maatwebsite Excel exports
- `resources/views/pages/{module}/` — Blade views mirror controller modules

### Roles & middleware

Four roles: `super-admin`, `school-admin`, `teacher`, `student`.

Custom `role` middleware alias in `bootstrap/app.php`. Usage: `middleware(['role:super-admin,school-admin'])`.

Route `/dashboard` is a universal hub that redirects based on role.

### Key model relationships

- `User` ↔ `Role` via `user_has_roles` pivot
- `User` ↔ `SchoolProfiles` via `user_has_schools` pivot
- `User` → `Student` / `Teacher` (hasOne, keyed by `user_id`)
- Primary keys use domain-specific names (`academic_year_id`, `school_id`, etc.)

## Gotchas

- **Indonesian** — error messages, comments, and variable names are in Bahasa Indonesia
- **RefreshDatabase is commented out** in `tests/Pest.php` — tests use SQLite :memory: via phpunit.xml
- **Scramble docs** from `web.php` only — no `routes/api.php` exists
- **All JSON endpoints** live in `web.php`, not `api.php`
- **Modal-based CRUD** — no separate create/edit pages, views use modals via Alpine.js
- **No CI pipeline** — no `.github/workflows/`
- **No custom Pint config** — uses Laravel Pint defaults
- **`maatwebsite/excel`** for exports (Teacher module)
- **Vite ignores** `storage/framework/views/**` in file watcher
- **Tailwind v4** uses `@tailwindcss/vite` plugin (not PostCSS)
- **Duplicate `ILIKE`** usage — PostgreSQL-specific, will crash on MySQL/MariaDB
