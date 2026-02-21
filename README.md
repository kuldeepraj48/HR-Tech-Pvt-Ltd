# URL Shortener Application

A Laravel-based URL shortener application with multi-company support, role-based access control, and invitation system.

## Summary

You are implementing a service which will allow your users to generate short URLs.
- Your service must have multiple companies
- Each company will have multiple users
- Each user will be either an Admin or a Member (or SuperAdmin, Sales, Manager)

## Requirements

- **Framework**: PHP Laravel 10 or 11 or 12
- **Database**: SQLite or MySQL

## Features

### Authentication & Authorization

- **Roles**: SuperAdmin, Admin, Member, Sales, Manager
- **SuperAdmin Account**: Created using Database Seeder with raw SQL
- **Authentication**: Users can log-in and log-out

### Invitation System

- **SuperAdmin**: Cannot invite an Admin in a new company
- **Admin**: Cannot invite another Admin or Member in their own company
- **Invitation Acceptance**: Public route accessible without authentication at `/invitations/accept/{token}`

### URL Shortener

- **Creation Restrictions**:
  - Admin and Member **cannot** create short URLs
  - SuperAdmin **cannot** create short URLs
  - Only Sales and Manager can create short URLs

- **Viewing Restrictions**:
  - SuperAdmin **cannot** see the list of all short URLs for every company
  - Admin can only see the list of all short URLs **not created** in their own company
  - Member can only see the list of all short URLs **not created** by themselves
  - Sales and Manager can see all short URLs

- **Access Control**:
  - All short URLs are **not publicly resolvable** and require authentication
  - Short URLs redirect to the original URL when accessed by authenticated users
  - URL format: `/s/{code}`

## GitLab — Push and clone steps

**Push to GitLab (first time):**  
1. `git init` (if new) → `git remote add origin <your-gitlab-repo-url>`  
2. `git add .` → `git commit -m "Initial commit"` → `git push -u origin main`

**After cloning from GitLab (full setup order):**

| Step | Action | Command |
|------|--------|--------|
| 1 | Clone | `git clone <GitLab-URL>` then `cd` into project |
| 2 | Dependencies | `composer install` |
| 3 | Environment | `cp .env.example .env` and `php artisan key:generate` |
| 4 | Database | Edit `.env` (SQLite or MySQL), create DB if MySQL |
| 5 | SQLite file (if used) | `touch database/database.sqlite` |
| 6 | Migrate | `php artisan migrate` |
| 7 | Seed | `php artisan db:seed` |
| 8 | Run | `php artisan serve` → open http://localhost:8000 |

**Detailed guide (with screenshots placeholders):** see [GITLAB_SETUP_AND_STEPS.md](GITLAB_SETUP_AND_STEPS.md).

---

## Installation

1. **Clone the repository:**
```bash
git clone <repository-url>
cd url-shortener
```

2. **Install dependencies:**
```bash
composer install
```

3. **Copy environment file:**
```bash
cp .env.example .env
```

4. **Generate application key:**
```bash
php artisan key:generate
```

5. **Configure database in `.env`:**

For SQLite:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

For MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shortener
DB_USERNAME=root
DB_PASSWORD=
```

6. **Create database file (if using SQLite):**
```bash
touch database/database.sqlite
```

7. **Run migrations:**
```bash
php artisan migrate
```

8. **Seed the database (creates roles and SuperAdmin):**
```bash
php artisan db:seed
```

## SuperAdmin Credentials

After running the seeder, you can login with:
- **Email**: superadmin@example.com
- **Password**: password

## Running Tests

Run the test suite:
```bash
php artisan test
```

Or with PHPUnit:
```bash
vendor/bin/phpunit
```

### Test Coverage

All requirements are tested:

- ✅ Admin and Member cannot create short URLs
- ✅ SuperAdmin cannot create short URLs
- ✅ SuperAdmin cannot see the list of all short URLs
- ✅ Admin can only see short URLs not created in their own company
- ✅ Member can only see short URLs not created by themselves
- ✅ Short URLs are not publicly resolvable and redirect to the original URL
- ✅ SuperAdmin cannot invite an Admin in a new company
- ✅ Admin cannot invite another Admin or Member in their own company

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── LoginController.php
│   │   ├── ClientController.php
│   │   ├── DashboardController.php
│   │   ├── InvitationController.php
│   │   ├── ShortUrlController.php
│   │   └── TeamMemberController.php
│   └── Middleware/
│       └── CheckRole.php
├── Models/
│   ├── Company.php
│   ├── Invitation.php
│   ├── Role.php
│   ├── ShortUrl.php
│   └── User.php
database/
├── migrations/
├── factories/
└── seeders/
    ├── DatabaseSeeder.php
    ├── RolesSeeder.php
    └── SuperAdminSeeder.php
tests/
└── Feature/
    ├── AuthenticationTest.php
    ├── InvitationTest.php
    └── ShortUrlTest.php
```

## Access Control Rules

### URL Shortener

- **Admin and Member**: Cannot create short URLs
- **SuperAdmin**: Cannot create short URLs
- **SuperAdmin**: Cannot see the list of all short URLs
- **Admin**: Can only see short URLs NOT created in their own company
- **Member**: Can only see short URLs NOT created by themselves
- **Sales and Manager**: Can create and view all short URLs
- **All short URLs**: Not publicly resolvable (require authentication)

### Invitations

- **SuperAdmin**: Cannot invite an Admin in a new company (must use client invitation flow)
- **Admin**: Cannot invite another Admin or Member in their own company
- **Invitation Acceptance**: Public route at `/invitations/accept/{token}` (no authentication required)

## Usage

1. **Start the development server:**
```bash
php artisan serve
```

2. **Access the application at `http://localhost:8000`**

3. **Login with SuperAdmin credentials or create a new account via invitation**

4. **Navigate through the dashboard to manage invitations and short URLs**

## Routes

### Public Routes
- `GET /login` - Login page
- `POST /login` - Login handler
- `GET /invitations/accept/{token}` - Accept invitation (public)
- `POST /invitations/accept/{token}` - Process invitation acceptance (public)
- `GET /s/{code}` - Short URL redirect (requires authentication)

### Protected Routes (require authentication)
- `GET /dashboard` - Dashboard
- `GET /short-urls` - List short URLs
- `GET /short-urls/create` - Create short URL form
- `POST /short-urls` - Store short URL
- `GET /short-urls/download` - Download short URLs CSV
- `GET /invitations` - List invitations
- `GET /invitations/create` - Create invitation form
- `POST /invitations` - Store invitation
- `GET /clients` - List clients (SuperAdmin only)
- `GET /team-members` - List team members (Admin only)
- `POST /logout` - Logout

### Public database for testing
- `/public/shortener.sql` - sql download for testing