# Laravel Posts Sync Application

A Laravel-based application to **fetch, store, and display posts from an external API** asynchronously.  
Built to demonstrate handling of **large datasets**, **background processing**, **service-oriented architecture**, and **resilient API communication** using Laravel best practices.

---

## 🚀 Features

- Fetch posts from an external API (JSONPlaceholder)
- Store posts efficiently using `upsert()` to prevent duplication
- Background sync using **Laravel Queues** to optimize large datasets
- Clean **Service Layer** with **Service Container & Dependency Injection**
- Separate responsibilities for **fetching** and **storing** data
- **Circuit Breaker** pattern to handle unstable external APIs
- **Throttle protection** to prevent excessive sync requests
- AJAX-based sync trigger (JSON POST request only)
- Frontend spinner during sync with auto-reload after completion
- Real-time sync status polling using cache

---

## 🧱 Architecture Overview

- **Controller**
  - Handles HTTP requests only
- **Service Layer**
  - Business logic for fetching and storing posts
  - Bound via service container and injected using interfaces
- **Jobs (Queue)**
  - Heavy data processing runs asynchronously
- **Cache**
  - Tracks sync status (`idle`, `running`, `finished`)

---

## 🛠️ Requirements

- PHP >= 8.4
- Composer
- Docker & Docker Compose
- MySQL / MariaDB (via Laravel Sail)

---

## 🐳 Installation & Run (Laravel Sail)

### 1 Clone the Repository

```bash
git clone https://github.com/moinul70/laravel-scalable-data-import.git
cd laravel-scalable-data-import

### 2 Install Dependencies
```bash
composer install

### 3 Start Sail Containers
```bash
./vendor/bin/sail up -d
### 4 Generate the key
```bash
./vendor/bin/sail php artisan key:generate
### 5 Run migration
```bash
./vendor/bin/sail php artisan migrate
### 5 Run migration
```bash
./vendor/bin/sail php artisan migrate
### 6 Run queue worker
```bash
./vendor/bin/sail artisan queue:work

## After all done hit the url in any browser url:http://127.0.0.1/
