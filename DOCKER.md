# LevelUp DOCKER FIRST DAY SET-UP

```powershell

# enter the Laravel project folder
Set-Location LevelUp_App

# prepare env files
Copy-Item .env.docker.example .env.docker -Force

# launch the stack (php-fpm, nginx, mysql, simulator)
docker compose up -d

# install dependencies inside the PHP container
docker compose -f docker-compose.yml exec app composer install
docker compose -f docker-compose.yml exec app npm install

# publish environment to Laravel and generate key
docker compose -f docker-compose.yml exec app cp .env.docker .env
docker compose -f docker-compose.yml exec app php artisan key:generate

# migrate and seed the database
docker compose -f docker-compose.yml exec app php artisan migrate --seed

# do not leave the key field blank (in the env file that gets automatically edited) ==> discard the change
```

## Commands After First Day SET-UP (copy & paste)

```powershell
# start services (from LevelUp_App)
docker compose up -d

# start Vite hot reload for live changes (leave running)
docker compose -f docker-compose.yml exec app npm run dev -- --host 0.0.0.0

# Faster reload (only if assets were modified in advanced)
docker compose -f docker-compose.yml exec app npm run build

# stop everything
docker compose down

# To run and open the WIFI2BLE DESK API do the following:

# You must keep this command running in a separate POWERSHELL console
cd c:\YOUR OWN DIRECTORY\LevelUp\wifi2ble-box-simulator-main
python simulator/main.py --port 8100

# To see the API data in JSON format go to:
http://127.0.0.1:8100/api/v2/E9Y2LxT4g1hQZ7aD8nR3mWx5P0qK6pV7/desks
```
