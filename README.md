# Project Track

A project management tracking application built with Laravel 13, Vue 3, and Tailwind CSS.

## Architecture

This project follows a modern Laravel architecture, prioritizing separation of concerns and type safety:

-   **Actions**: Encapsulate business logic in `app/Actions`. Each action is typically with a `handle()` method.
-   **Value Objects (DTOs)**: Used to pass structured data between layers, found in `app/Values`. They are implemented as `readonly` classes.
-   **Eloquent API Resources**: Standardized API responses using Laravel's Resource classes.
-   **Form Requests**: Handle validation and authorization, with a `toValue()` method to transform validated data into Value Objects.
-   **Models & Enums**: Use PHP 8.1+ Enums for status and priority fields.

## Setup

To get started with the project, you can use the provided setup script:

```bash
composer setup
```

The `setup` script performs the following actions:
1.  Installs PHP dependencies (`composer install`).
2.  Creates a `.env` file from `.env.example`.
3.  Generates an application key.
4.  Runs database migrations.
5.  Installs NPM dependencies.
6.  Builds frontend assets.

For local development, you can run:

```bash
composer dev
```

## Features Implemented

-   **Full Project CRUD**: Create, read, update, and delete projects.
-   **Advanced Filtering**: Filter projects by status, priority, and date ranges.
-   **Action-Based Architecture**: Business logic is encapsulated in discrete Action classes for better maintainability.
-   **Type Safety**: Extensive use of PHP 8.4+ features including readonly classes, constructor property promotion, and Enums.
-   **Standardized API Responses**: Uses Eloquent API Resources for consistent JSON output.
-   **Automated Setup**: Streamlined environment configuration via a single `composer setup` command.

## Setup Instructions

### Prerequisites

-   **PHP 8.5+**
-   **Composer**
-   **Node.js & NPM**
-   **SQLite** (default database)

### Installation

1.  **Clone the repository**:
    ```bash
    git clone <repository-url>
    cd project-track
    ```

2.  **Run the setup script**:
    This script will install dependencies, set up the environment file, generate the app key, and run migrations.
    ```bash
    composer setup
    ```

3.  **Start the development server**:
    ```bash
    composer dev
    ```

## Linting and PHPStan

The project uses **Laravel Pint** for code style and **PHPStan** (Larastan) for static analysis.

### PHPStan

To run static analysis at the highest level (Level 10):

```bash
vendor/bin/phpstan analyse
```

### Laravel Pint

To fix code style issues:

```bash
vendor/bin/pint
```

## Testing

The project uses **Pest** for its testing suite.

To run all tests:

```bash
php artisan test
```

Or run Pest directly:

```bash
vendor/bin/pest
```
