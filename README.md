# Laravel Posts Sync Application

A Laravel-based system to **fetch, store, and display posts** from an external API asynchronously.  
Designed to handle **large datasets**, **background sync**, and **resilient API calls** with **circuit breaker** and **throttling**.

---

## Features

- **Fetch Posts** from external API (JSONPlaceholder)
- **Store Posts** efficiently using `upsert()` to avoid duplicates
- **Queue-Based Sync**: Posts are synced in the background using Laravel Queues
- **Service-Oriented Architecture**: Fetching and storing logic separated into a **service** and injected via **service container**
- **Circuit Breaker** for external API calls to prevent repeated failures
- **Throttle Sync Requests**: Protects the system from spamming
- **Real-Time Sync Status**: Frontend shows spinner and polls backend for completion
- **AJAX JSON Calls**: Sync button triggers a JSON POST, page reloads after sync completes

---

## Requirements

- PHP >= 8.1
- Composer
- MySQL or MariaDB
- Node.js + npm (for frontend assets)
- Docker & Docker Compose (for Sail)

---

## Installation & Run via Sail

### 1️⃣ Clone the repository

```bash
git clone <repo-url>
cd <repo-folder>
2️⃣ Start Sail environment
./vendor/bin/sail up -d
3️⃣ Install dependencies inside Sail
./vendor/bin/sail composer install
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev

Sail uses mysql as the host inside the container

5️⃣ Run migrations
./vendor/bin/sail artisan migrate

6️⃣ Start queue worker
./vendor/bin/sail artisan queue:work


This ensures background jobs (sync posts) are processed

Routes
Method	URL	Action
GET	/posts	Show all posts
POST	/posts/sync	Trigger background sync
GET	/posts/sync/status	Get current sync status
GET	/posts/sync-remaining	