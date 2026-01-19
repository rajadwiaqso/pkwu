# School Website

Proyek website sekolah menggunakan Laravel 11 + Inertia.js + Vue 3 + Tailwind CSS.

## Teknologi yang Digunakan

### Backend
- **Laravel 11** - PHP Framework
- **Laravel Sanctum** - API Authentication
- **Inertia.js** - Modern monolith approach

### Frontend
- **Vue 3** - Progressive JavaScript Framework
- **Tailwind CSS** - Utility-first CSS Framework
- **Vite** - Frontend Build Tool

### Database
- **MySQL/PostgreSQL/SQLite** - Relational Database

## Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18.x
- NPM/Yarn/PNPM

## Installation

1. **Clone repository**
```bash
git clone <repository-url>
cd laravel
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install JavaScript dependencies**
```bash
npm install
```

4. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Setup database**
   - Buat database baru
   - Konfigurasi koneksi database di file `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_website
DB_USERNAME=root
DB_PASSWORD=
```

6. **Run migrations**
```bash
php artisan migrate
```

7. **Build assets**
```bash
npm run build
```

## Development

Untuk development, jalankan kedua command berikut di terminal yang berbeda:

```bash
# Terminal 1 - Laravel development server
php artisan serve

# Terminal 2 - Vite development server
npm run dev
```

Akses aplikasi di: `http://localhost:8000`

## Production Build

```bash
npm run build
```

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Controllers
│   │   └── Middleware/     # Middleware
│   └── Models/             # Eloquent Models
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/           # Database seeders
├── resources/
│   ├── css/               # CSS files
│   ├── js/
│   │   ├── Pages/         # Inertia Vue pages
│   │   └── app.js         # Vue app entry
│   └── views/             # Blade templates
├── routes/
│   ├── web.php            # Web routes
│   ├── api.php            # API routes
│   └── channels.php       # Broadcast channels
└── public/                # Public assets

```

## Available Scripts

- `npm run dev` - Start Vite development server
- `npm run build` - Build production assets
- `composer test` - Run PHP tests
- `php artisan serve` - Start Laravel development server

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
