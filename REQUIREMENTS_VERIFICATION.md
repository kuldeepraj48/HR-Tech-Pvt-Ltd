# Requirements Verification

Use this document to verify that all project requirements are implemented and working.

---

## 1. Prerequisites

- PHP and Composer installed
- Database (MySQL or SQLite) configured in `.env`
- Application key generated: `php artisan key:generate`
- Migrations run: `php artisan migrate`
- Seeder run: `php artisan db:seed` (creates roles and SuperAdmin user)

---

## 2. Run Automated Tests

**Step 1.** Open a terminal in the project root.

**Step 2.** Run:

```bash
php artisan test
```

**Step 3.** Confirm all tests pass (e.g. 12 tests, 32+ assertions).

**Step 4.** If any test fails, fix the failing test or the code it covers, then run the tests again.

---

## 3. Requirements Checklist

### Invitation rules

| # | Requirement | Where to check |
|---|-------------|----------------|
| 1 | SuperAdmin cannot invite an Admin in a new company via the regular invitation form | `InvitationController::store()` – SuperAdmin must use “Invite New Client” (name + email), not Admin role |
| 2 | Admin cannot invite another Admin or Member in their own company | `InvitationController::store()` – only Sales/Manager can be invited by Admin |

### URL shortener rules

| # | Requirement | Where to check |
|---|-------------|----------------|
| 3 | Admin and Member cannot create short URLs | `ShortUrlController::create()` and `store()` – 403 for Admin/Member |
| 4 | SuperAdmin cannot create short URLs | `ShortUrlController::create()` and `store()` – 403 for SuperAdmin |
| 5 | SuperAdmin cannot see the list of all short URLs | `ShortUrlController::index()` and `download()` – 403 for SuperAdmin |
| 6 | Admin sees only short URLs not created in their own company | `ShortUrlController::index()` – filter `company_id != user's company_id` |
| 7 | Member sees only short URLs not created by themselves | `ShortUrlController::index()` – filter `user_id != user's id` |
| 8 | Short URLs are not publicly resolvable (require login) | `ShortUrlController::redirect()` – 403 if not authenticated |

---

## 4. Manual Verification Steps

### 4.1 Start the app

**Step 1.** Run: `php artisan serve`  
**Step 2.** Open: http://localhost:8000  
**Step 3.** Log in as SuperAdmin (e.g. superadmin@example.com / password).

### 4.2 Invitation rules

**Step 1.** As SuperAdmin, go to **Invite New Client** (Clients → Invite).  
- Enter client name and email → should succeed (creates company and invites Admin).

**Step 2.** As SuperAdmin, go to **Invitations** → **Create Invitation**.  
- If you try to invite with role **Admin** via this form → should show an error (SuperAdmin cannot invite Admin in new company this way).

**Step 3.** Log in as an **Admin** (e.g. after accepting an invitation).  
**Step 4.** Go to **Invite New Team Member**.  
- Invite with role **Admin** or **Member** → should show an error.  
- Invite with role **Sales** or **Manager** → should succeed.

### 4.3 URL shortener – who can create

**Step 1.** Log in as **Admin** or **Member**.  
**Step 2.** Open `/short-urls/create` or try to POST to create a short URL.  
- Result: **403** (Admin and Member cannot create short URLs).

**Step 3.** Log in as **SuperAdmin**.  
**Step 4.** Open `/short-urls/create`.  
- Result: **403** (SuperAdmin cannot create short URLs).

**Step 5.** Log in as **Sales** or **Manager**.  
**Step 6.** Open `/short-urls/create`, enter a URL, and submit.  
- Result: Short URL is created.

### 4.4 URL shortener – who can see list

**Step 1.** Log in as **SuperAdmin**.  
**Step 2.** Open `/short-urls`.  
- Result: **403** (SuperAdmin cannot see the list of all short URLs).

**Step 2.** Log in as **Admin** (Company A).  
- Ensure some short URLs exist in Company A and some in Company B.  
- Open `/short-urls`.  
- Result: Only short URLs **not** from Company A are listed.

**Step 3.** Log in as **Member**.  
- Ensure the Member has not created any short URLs; other users in the same company have.  
- Open `/short-urls`.  
- Result: Only short URLs created by **other** users are listed (not the Member’s own).

### 4.5 Short URLs require authentication

**Step 1.** Log out.  
**Step 2.** Open a short URL in the browser, e.g. `http://localhost:8000/s/XXXXXXXX`.  
- Result: **403** (not publicly resolvable).

**Step 3.** Log in again.  
**Step 4.** Open the same short URL.  
- Result: Redirect to the original long URL (and hit count increments).

---

## 5. If Something Fails

**Step 1.** Run tests again: `php artisan test`  
**Step 2.** Clear caches: `php artisan cache:clear` and `php artisan config:clear`  
**Step 3.** Check roles in the database: `SELECT * FROM roles;`  
**Step 4.** Check user–role–company: `SELECT * FROM user_company_role;`  
**Step 5.** Confirm each user has the correct `company_id` in the `users` table.

---

## 6. Main Files

- **Roles and company context:** `app/Models/User.php` (`hasRole()`)  
- **Invitation rules:** `app/Http/Controllers/InvitationController.php`  
- **Short URL rules:** `app/Http/Controllers/ShortUrlController.php`  
- **Routes:** `routes/web.php`

---

## 7. Quick Test Command

```bash
php artisan test
```

All requirement-related behaviour is covered by the test suite. If all tests pass, the requirements above are satisfied.
