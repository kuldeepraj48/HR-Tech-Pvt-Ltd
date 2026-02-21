# URL Shortener Application

A Laravel-based URL shortener application with multi-company support, role-based access control, and invitation system.

# Auther

Kuldeep Raj Full Stack Developer Team Lead.

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
- 
### Details and Repo

Login:

<img width="1792" height="544" alt="image" src="https://github.com/user-attachments/assets/a22ad7f6-8c74-41d3-9f2e-b6ba429cfa35" />

### Super Admin

Super Admin After Login:

<img width="1882" height="635" alt="image" src="https://github.com/user-attachments/assets/0eddf487-102a-49a6-914f-861ccaf3a514" />

Invite new client by Supper Admin:

<img width="1875" height="665" alt="image" src="https://github.com/user-attachments/assets/2a4f537a-bd90-4a20-b3ed-40f2cdb8a8e5" />

Short Urls Node able to access by Super Admin.

<img width="1458" height="632" alt="image" src="https://github.com/user-attachments/assets/868c1a3e-2fde-42fa-8a46-db3495cb3b1c" />

### Admin

Admin Login:

<img width="1809" height="553" alt="image" src="https://github.com/user-attachments/assets/97b83370-4919-4743-832c-613c150b7585" />

Admin Deshboard:

<img width="1899" height="577" alt="image" src="https://github.com/user-attachments/assets/af0afc7e-56a3-4766-b050-fb26bbd69dd6" />

Admin View Team Member :

<img width="1887" height="649" alt="image" src="https://github.com/user-attachments/assets/eec19e77-52b4-4f1e-be11-d3baa175a26f" />

Admin Invite new team Member :

<img width="1895" height="883" alt="image" src="https://github.com/user-attachments/assets/a6b90ce1-9b34-4317-9b08-7026d81d0709" />

Admin Short Urls view :

<img width="1910" height="644" alt="image" src="https://github.com/user-attachments/assets/2bc871df-5626-4e46-80f6-ac66a1792c6e" />

### Client

Client Login:

<img width="1881" height="660" alt="image" src="https://github.com/user-attachments/assets/890dad9b-ced6-4381-bbad-4be9768422d5" />

Generate Short Url and Download:

<img width="1854" height="614" alt="image" src="https://github.com/user-attachments/assets/aed7b420-74dd-4375-bbdc-0dfb965cd6e7" />

Generate Urls :

<img width="1906" height="617" alt="image" src="https://github.com/user-attachments/assets/222378f0-6e6c-4afa-a151-4098606fe926" />

### Database just for testing

<img width="1535" height="543" alt="image" src="https://github.com/user-attachments/assets/16788958-abb0-42de-9469-e3fdf9f33956" />

Short Ulrs Coming.

<img width="1528" height="207" alt="image" src="https://github.com/user-attachments/assets/c451a2fe-ca8c-4e72-bc27-b66692bd3224" />


### Public database for testing
- `/public/shortener.sql` - sql download for testing

### Changes Required then let me know
-  kuldeepraj48
