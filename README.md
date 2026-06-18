# Phone & Accessory Price Lookup System

A PHP/MySQL inventory and price-lookup system built for a Gambian phone and accessories retailer. Customers can search and browse current stock and pricing on the public-facing page, while an admin panel allows the store to add, edit, and delete product listings in real time.

## Features

- **Public search/display page** (`index.html`) — customers can look up phones and accessories by name, brand, or category, with live pricing and stock status.
- **Admin panel** (`admin.php`) — add new products, edit existing listings, and delete discontinued items, with instant feedback (toast notifications, confirmation modal before deletion).
- **Auto-generated product names** — full product names are built automatically from brand, series/type, and variant fields rather than typed manually, reducing inconsistent naming in the catalogue.
- **Stock status indicators** — colour-coded quantity badges (in stock, low stock, out of stock) for quick visual scanning.
- **Category-aware validation** — phones require a series (e.g. "iPhone 13"), accessories require a type (e.g. "Power Bank"), enforced both in the UI and on the server.

## Tech Stack

PHP (PDO with prepared statements), MySQL, vanilla HTML/CSS/JS — no frameworks.

## Architecture

The backend is split by responsibility rather than handled in one monolithic file: `admin_insert.php`, `admin_edit_save.php`, and `admin_delete.php` each handle one database operation, with `admin_list.php` serving product data as JSON for the frontend to render. All database writes use PDO prepared statements with named parameters to prevent SQL injection, and server-side validation (required fields, category whitelisting, numeric price checks) runs independently of the client-side checks in case JavaScript validation is bypassed.

Database credentials are kept in a separate `config.php` file, which is intentionally excluded from this repository.

## Known Limitations

The admin panel does not yet have authentication — this is a planned next step before any live deployment. As it stands, this repo is intended as a demonstration of the CRUD architecture and secure query handling rather than a finished, production-ready admin system.

## Setup

1. Import `database.sql` into a MySQL database.
2. Create a `config.php` file in the project root with your database connection details (see the `getDB()` call referenced in the admin scripts for the expected interface).
3. Serve the project root with PHP (e.g. via XAMPP/WAMP or `php -S localhost:8000`).
4. Visit `index.html` for the public search page, or `admin.php` for the admin panel.
