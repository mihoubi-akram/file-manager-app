# File Manager

A secure file management system — upload, list, download, and delete your files.

**Stack:** Laravel 13 · Vue 3 · MySQL 8.4 · Docker

---

## Quick Start

**1. Clone and configure**
```bash
git clone <repo-url>
cd file-manager-app
cp .env.example .env
```
Open `.env` and set:
```
DB_PASSWORD=your_password
DB_ROOT_PASSWORD=your_root_password
```

> If the frontend runs on a port other than `5173`, also update `backend/.env`:
> ```
> FRONTEND_URL=http://localhost:<port>
> SANCTUM_STATEFUL_DOMAINS=localhost:<port>
> ```
> Then run: `docker compose exec backend php artisan config:clear`

**2. Start the backend**
```bash
make up
```

**3. Start the frontend**
```bash
cd frontend && npm install && npm run dev
```

**4. Open your browser**

Open the URL shown in the terminal after step 3 (e.g. `http://localhost:5173`).
Register an account and start uploading files.

---

## Architecture

The app is split into two separate projects in a monorepo:

- **Backend** — Laravel 13 REST API. Handles auth, file storage, and business logic. Files are stored in private disk (not publicly accessible) and scoped per user. Authorization is enforced via Laravel Policies. Laravel Sanctum handles session-based SPA authentication.

- **Frontend** — Vue 3 SPA with Pinia for state and Vue Router for navigation. Communicates with the backend via Axios, proxied through Vite in development.

- **Auth** — Sanctum SPA mode (cookie/session, no tokens). CSRF protection is handled automatically by Axios + Sanctum.

- **Database** — MySQL 8.4 via Docker. Files metadata is stored in the DB, Single `user_files` table with UUID primary key

---

## Commands

```bash
make up            # Start backend + database
make down          # Stop containers
make logs          # View backend logs
make migrate       # Run migrations manually
```
