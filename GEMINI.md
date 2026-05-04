# VotingApp: Kosgoro™ Digital Voting System

Kosgoro™ is a modern, web-based voting application designed with a **Neo-Brutalist** aesthetic. It provides a robust and transparent platform for managing elections, candidates, and live ballots.

## Project Overview

*   **Architecture:** Monolithic Laravel 12 application using **Inertia.js** to bridge the backend with a **Vue.js 3** frontend.
*   **Tech Stack:**
    *   **Backend:** PHP 8.2+, Laravel 12
    *   **Frontend:** Vue 3 (Composition API), Vite, Tailwind CSS (Custom Neo-Brutalist theme)
    *   **Database:** SQLite (default for development)
    *   **Authentication:** Laravel Breeze / Manual Auth implementation
*   **Key Features:**
    *   **Election Management:** Create, activate, and end election periods.
    *   **Candidate Management:** Manage candidates per election, including profiles and photos.
    *   **Live Ballots:** Real-time voting interface for users.
    *   **Voter Analytics:** Detailed results and participation rates.
    *   **Admin Panel:** Full control for Admins and Super Admins.

## Technical Standards & Conventions

### UI/UX Style: Neo-Brutalism
The project follows a strict Neo-Brutalist design language defined in `tailwind.config.js` and `resources/css/app.css`:
*   **Borders:** Thick black borders (`border-4` or `border-neo`).
*   **Shadows:** Solid, non-blurred shadows (`shadow-[8px_8px_0px_0px_rgba(0,0,0,1)]`).
*   **Colors:** High-contrast primary colors (Blue `#0048FF`, Yellow `#FFDE00`, Red `#FF3C3C`).
*   **Typography:** Bold, uppercase headings using "Space Grotesk" and "Work Sans".
*   **Interactions:** "Press" effect on buttons (shifting 2px-4px on click).

### Backend (Laravel)
*   **Models:** Located in `app/Models`. Use Eloquent relationships and scopes (e.g., `Election::scopeActive`).
*   **Controllers:** Located in `app/Http/Controllers`. Use Inertia to render views: `return Inertia::render('PageName', $data);`.
*   **Middleware:** Custom middleware for `admin` and `super_admin` roles in `app/Http/Middleware`.
*   **Routes:** Defined in `routes/web.php` (web interface) and `routes/auth.php` (authentication).

### Frontend (Vue.js)
*   **Pages:** Located in `resources/js/Pages`. Organized by feature (Admin, Vote, Results).
*   **Components:** Reusable UI elements in `resources/js/Components` (e.g., `SidebarLink`, `NeoToast`).
*   **Layouts:** Main application wrapper in `resources/js/Layouts/AuthenticatedLayout.vue`.
*   **Form Handling:** Use `@inertiajs/vue3`'s `useForm` hook for all data submissions.

## Building and Running

### Development
1.  **Dependencies:** `composer install` and `npm install`.
2.  **Environment:** Copy `.env.example` to `.env` and run `php artisan key:generate`.
3.  **Database:** `php artisan migrate`.
4.  **Run:** `php artisan serve` and `npm run dev`.

### Production Build
Run `npm run build` to compile assets.
*Note: If build fails with "Unexpected token" in Vue files, ensure complex object mapping in templates is moved to `computed` properties.*

## Key Files
*   `tailwind.config.js`: Defines the Neo-Brutalist theme tokens.
*   `resources/css/app.css`: Custom Neo-Brutalist utility classes and patterns.
*   `resources/js/Layouts/AuthenticatedLayout.vue`: The primary layout with collapsible sidebar logic.
*   `app/Models/Election.php`: Contains core logic for election states and participation rates.
*   `routes/web.php`: The central hub for application routing.
