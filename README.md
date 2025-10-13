# LevelUp

A focus timer application that helps you maintain healthy work habits by alternating between sitting and standing, with a built-in points system based on Cornell University research.

📖 **Points system with cycles - Thursday (Mats)**: See `POINTS_SYSTEM.md`  
📝 **For project changes and updates**: See `CHANGELOG.md`

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


## 🚀 Team Setup Guide

### Prerequisites
Make sure you have installed:
- **PHP 8.2+** 
- **Composer** (PHP package manager)
- **Node.js & NPM** 
- **XAMPP** (if you don't have MySQL/MariaDB installed separately)

### Step 1: Clone and Install
```bash
# Clone the repository
git clone https://github.com/Lara-Ghi/LevelUp.git
cd LevelUp

# Install PHP dependencies
composer install

# Install JavaScript dependencies  
npm install
```

### Step 2: Windows PowerShell Fix (Windows Users Only)
If you get PowerShell execution policy errors:
```powershell
# Run this ONCE in PowerShell as Administrator:
Set-ExecutionPolicy -Scope CurrentUser -ExecutionPolicy RemoteSigned -Force
```

### Step 3: Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate
```

### Step 4: Database Setup

#### Option A: Using XAMPP
1. **Start XAMPP** - Open XAMPP Control Panel
2. **Start Apache** - Click "Start" next to Apache
3. **Start MySQL** - Click "Start" next to MySQL  
4. **Keep XAMPP running** in the background while developing

#### Option B: Using Local Database
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=levelup
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 5: Run Database Migrations
```bash
# Create database tables
php artisan migrate

# Create test user for points system
php artisan db:seed
```

### Step 6: Start Development Servers

**You need TWO terminals running simultaneously:**

#### Terminal 1: JavaScript Development Server
```bash
# Start this FIRST and keep it running
npm run dev
```
**⚠️ IMPORTANT**: This watches for JavaScript/CSS changes and auto-refreshes. Don't close this terminal!

#### Terminal 2: PHP Server
```bash  
# Start Laravel server
php artisan serve
```

### Step 7: Open the App
Visit: **http://127.0.0.1:8000**

## 🔧 Development Workflow

### Making Changes
1. **Keep both terminals running** (`npm run dev` + `php artisan serve`)
2. **Edit files** - Your changes will auto-refresh in the browser
3. **JavaScript/CSS changes** - Instant refresh (thanks to `npm run dev`)
4. **PHP changes** - Refresh browser manually

### Troubleshooting

**❌ "Changes not showing"**
- Make sure `npm run dev` is running
- Check browser console for errors

**❌ "Database errors"**  
- Make sure XAMPP MySQL is running
- Run `php artisan migrate` again

**❌ "Points not working"**
- Make sure test user exists: `php artisan db:seed`
- Check browser console for API errors

**❌ "Server not starting"**
- Check if port 8000 is already in use
- Try `php artisan serve --port=8001`

## 📁 Key Files for Development

- **Focus Clock**: `resources/js/home-clock/focus-clock.js`
- **Styling**: `resources/css/home-clock/`  
- **Backend API**: `app/Http/Controllers/HealthCycleController.php`
- **Routes**: `routes/web.php`
- **Database**: `database/migrations/`

## 🎮 Testing the Points System

1. Start a focus cycle (click the timer)
2. Let it run through sitting → standing phases  
3. Complete a full cycle
4. Check browser console for point calculations
5. Try different timing combinations to see scoring

**Pro tip**: For testing, try 20 min sitting + 10 min standing for maximum points!
