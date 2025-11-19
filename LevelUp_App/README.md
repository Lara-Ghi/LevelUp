# LevelUp

A focus timer application that helps you maintain healthy work habits by alternating between sitting and standing, with a built-in points system based on Cornell University research.

📖 **Points system with cycles - Thursday (Mats)**: See `POINTS_SYSTEM.md`  
📝 **For project changes and updates**: See `CHANGELOG.md`

## Setup Guide

### Prerequisites

Make sure you have installed:

- **PHP 8.2+**
- **Composer** (PHP package manager)
- **Node.js & NPM**
- **XAMPP** (if you don't have MySQL/MariaDB installed separately)
- **Docker Desktop** (optional, required if you want to run the full stack with containers)

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

#### Option A: Using XAMPP - This is my case (Manish), because I have XAMPP installed

1. **Start XAMPP** - Open XAMPP Control Panel
2. **Start Apache** - Click "Start" next to Apache
3. **Start MySQL** - Click "Start" next to MySQL  
4. **Keep XAMPP running** in the background while developing

#### Option B: Using Local Database - Maybe Lara u need to do this, but I am not sure

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=levelup
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### Option C: Using Docker (Laravel + MySQL + wifi2ble simulator)

1. Install Docker Desktop and ensure it is running.
2. Copy the Docker environment template and adjust values if needed:

   ```powershell
   cd LevelUp_App
   Copy-Item .env.docker.example .env.docker -Force
   ```

3. Build and start the stack (PHP-FPM, Nginx, MySQL, and the wifi2ble simulator API):

   ```powershell
   docker compose up --build
   ```

4. Once the containers are running, install dependencies and run migrations inside the PHP container:

   ```powershell
   docker compose exec app composer install
   docker compose exec app npm install
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --seed
   ```

5. Visit the app at [http://localhost:8080](http://localhost:8080). The simulator API is exposed at [http://localhost:8000/api/v2/<api_key>/desks](http://localhost:8000/api/v2/<api_key>/desks) from your host, while Laravel uses the internal `http://simulator:8000` address.

### Step 5: Run Database Migrations

```bash
# Create database tables
php artisan migrate

# Create test user for points system and see that it works
php artisan db:seed
```

#### Reset Database (Optional)

To reset the database with fresh migrations and seed (In case you want to reset the data and put everything to 0 "cycles, points ...". If not just do the two commands above and skip this one):

```bash
php artisan migrate:fresh --seed
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

Visit: **<http://127.0.0.1:8000>**

## 🔧 Development Workflow

### Making Changes

1. **Keep both terminals running** (`npm run dev` + `php artisan serve`)
2. **Edit files** - Your changes will auto-refresh in the browser
3. **JavaScript/CSS changes** - Instant refresh (thanks to `npm run dev`)
4. **PHP changes** - Refresh browser manually

### Troubleshooting

#### Changes not showing

- Make sure `npm run dev` is running
- Check browser console for errors

#### Database errors

- Make sure XAMPP MySQL is running
- Run `php artisan migrate` again

#### Points not working

- Make sure test user exists: `php artisan db:seed`
- Check browser console for API errors (Right-click "Inspect" and head to Console to see workflow and errors)

#### Server not starting

- Check if port 8000 is already in use
- Try `php artisan serve --port=8001`

## 🎮 Testing the Points System

1. Start a focus cycle (click the timer)
2. Let it run through sitting → standing phases  
3. Complete a full cycle
4. Check browser console for point calculations (By inspecting the website "right-click" and click the console tab)
5. Try different timing combinations to see scoring

**Pro tip**: For testing, try 20 min sitting + 10 min standing for maximum points!

## ✅ Tests

Run feature assertions (Pest feature suite):

```bash
php artisan test --testsuite=Feature
```

Run browser automation (Laravel Dusk):

```bash
php artisan dusk --ansi
```
