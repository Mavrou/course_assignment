# Course Assignment API

A REST API built with Laravel 13 for managing online courses.

## Prerequisites

- **PHP** 8.3 or higher
- **Composer** (PHP package manager)
- **Node.js** 18+ and npm (for frontend assets)
- **Git** (version control)

### Optional
- **SQLite** (included with PHP, but required if changing database)
- **MySQL** or **PostgreSQL** (if using a different database)

## Installation & Setup

### 1. Clone the Repository
```bash
git clone https://github.com/Mavrou/course_assignment.git
cd course_assignment
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Create Environment Configuration
```bash
cp .env.example .env
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

This creates a unique encryption key for your application.

### 5. Configure Database (Optional)
By default, SQLite is configured. To use a different database, edit `.env`:

**For MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=course_assignment
DB_USERNAME=root
DB_PASSWORD=
```

**For PostgreSQL:**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=course_assignment
DB_USERNAME=postgres
DB_PASSWORD=
```

### 6. Run Database Migrations
```bash
php artisan migrate
```

This creates the database tables:
- `courses` - Main course data table
- `cache` - Cache storage table (optional)
- `jobs` - Background jobs table (optional)

### 7. (Optional) Seed Sample Data
```bash
php artisan db:seed
```

This populates the database with sample course data using factories.

### 8. Install Frontend Dependencies
```bash
npm install
```

### 9. Build Frontend Assets
```bash
npm run build
```

For development with hot-reload:
```bash
npm run dev
```

## Running the Application

### Start Development Server
```bash
php artisan serve
```

The API will be available at: `http://localhost:8000`

### Run Everything Concurrently (Server + Queue + Vite)
```bash
composer run dev
```

This starts:
- Laravel development server (port 8000)
- Queue listener
- Vite development server (HMR)

### Access the API
- **Base URL**: `http://localhost:8000/api/v1`
- **Courses Endpoint**: `http://localhost:8000/api/v1/courses`