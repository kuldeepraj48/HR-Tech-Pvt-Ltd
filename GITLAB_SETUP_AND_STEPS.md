# GitLab Setup and Steps — Sembark URL Shortener

This document describes how to push this project to GitLab and the steps to run after cloning.

---

## Part 1: Push This Code to GitLab (First Time)

### Prerequisites

- Git installed on your machine
- A GitLab account and a new (or existing) project/repository created on GitLab
- This Laravel project ready locally in your project folder

### Step 1: Initialize Git (if not already done)

Open terminal in the project root and run:

```bash
cd /path/to/your/project
git init
```

*(Skip this step if the folder is already a Git repository — you will see a `.git` folder.)*

### Step 2: Add GitLab as Remote

Replace `YOUR_GITLAB_GROUP_OR_USER` and `REPO_NAME` with your GitLab group/username and repository name. Example: `https://gitlab.com/sembark/url-shortener.git`

```bash
git remote add origin https://gitlab.com/YOUR_GITLAB_GROUP_OR_USER/REPO_NAME.git
```

To use SSH instead:

```bash
git remote add origin git@gitlab.com:YOUR_GITLAB_GROUP_OR_USER/REPO_NAME.git
```

### Step 3: Stage and Commit All Files

```bash
git add .
git status
git commit -m "Initial commit: Sembark URL Shortener with roles, invitations, short URLs"
```

### Step 4: Push to GitLab

For default branch (e.g. `main` or `master`):

```bash
git branch -M main
git push -u origin main
```

If your GitLab project uses `master`:

```bash
git push -u origin master
```

After this, your code is on GitLab. Anyone (or CI) can follow **Part 2** to get the app running.

---

## Part 2: Steps After Cloning from GitLab (Run as Per Details)

These are the steps that should be followed **every time** someone clones this repository from GitLab (or when you clone on a new machine). Do them in order.

### Step 1: Clone the Repository

```bash
git clone https://gitlab.com/YOUR_GITLAB_GROUP_OR_USER/REPO_NAME.git
cd REPO_NAME
```

*(Use the actual clone URL from your GitLab project page.)*

### Step 2: Install PHP Dependencies

```bash
composer install
```

*(No `composer update` unless you intend to upgrade packages.)*

### Step 3: Environment File

```bash
cp .env.example .env
php artisan key:generate
```

### Step 4: Configure Database in `.env`

**Option A — SQLite:**

- Set in `.env`:
  - `DB_CONNECTION=sqlite`
  - `DB_DATABASE=/absolute/path/to/database.sqlite` (or path relative to project root, e.g. `C:/xampp/htdocs/.../url-shortener/database/database.sqlite`)
- Create the file if it does not exist:

```bash
touch database/database.sqlite
```

**Option B — MySQL:**

- Set in `.env`:
  - `DB_CONNECTION=mysql`
  - `DB_HOST=127.0.0.1`
  - `DB_PORT=3306`
  - `DB_DATABASE=url_shortener`
  - `DB_USERNAME=root`
  - `DB_PASSWORD=your_password`
- Ensure the database `url_shortener` exists in MySQL.

### Step 5: Run Migrations

```bash
php artisan migrate
```

### Step 6: Seed Database (Roles + SuperAdmin)

```bash
php artisan db:seed
```

### Step 7: Run the Application

```bash
php artisan serve
```

Open in browser: **http://localhost:8000**

**Default SuperAdmin login:**

- Email: `superadmin@example.com`
- Password: `password`

---

## Part 3: Ongoing Workflow (After First Push)

When you make changes and want to push again:

```bash
git add .
git status
git commit -m "Your descriptive message"
git push origin main
```

*(Use `master` if that is your default branch.)*

---

## Part 4: Where to Add Screenshots (Optional)

You can add a folder in the repo for documentation screenshots so that “all steps come as per details” with visuals:

1. Create a folder, e.g. `docs/screenshots/`.
2. Add screenshots with clear names, for example:
   - `01-gitlab-clone-url.png`
   - `02-composer-install.png`
   - `03-env-and-migrate.png`
   - `04-login-page.png`
   - `05-dashboard.png`
3. In this file or in README, reference them like:

   `![Clone from GitLab](docs/screenshots/01-gitlab-clone-url.png)`

4. Commit and push the `docs/` folder so they appear on GitLab.

*(Do not commit sensitive data or `.env` — only screenshots and docs.)*

---

## Quick Reference: Full Setup Order (As Per Details)

| Order | Action                    | Command / Action                          |
|-------|---------------------------|-------------------------------------------|
| 1     | Clone                     | `git clone <GitLab URL>` then `cd` into project |
| 2     | Composer install          | `composer install`                        |
| 3     | Environment               | `cp .env.example .env` and `php artisan key:generate` |
| 4     | Database config           | Edit `.env` (SQLite or MySQL)             |
| 5     | SQLite file (if used)     | `touch database/database.sqlite`         |
| 6     | Migrate                   | `php artisan migrate`                     |
| 7     | Seed                      | `php artisan db:seed`                     |
| 8     | Run app                   | `php artisan serve` → open http://localhost:8000 |

When you push this code to GitLab, these steps will be available in this file and in the README so that all steps come as per details for anyone cloning the repository.
