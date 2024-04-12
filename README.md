## Nice random app, built with Laravel

### Prerequisites:
1. Have Docker installed.

### Setup guide:
1. Clone the repository: `git clone https://github.com/KristapsVitols/nice-random-app.git`
2. Copy .env.exmaple file: `cp .env.example .env`
3. To avoid having to install PHP/composer locally, run this command:
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```
4. Build and start up docker containers: `vendor/bin/sail up -d`
5. Connect to shell (sail): `vendor/bin/sail shell`
6. Within the shell, run migrations: `php artisan migrate`
7. Within the shell, run seeders: `php artisan db:seed`

### API endpoints:
1. **GET** http://localhost/api/clients/{clientId}/accounts
2. **GET** http://localhost/api/accounts/{accountId}/transactions (`offset` and `limit` parameters available)
3. **POST** http://localhost/api/transfer-funds
   Required parameters:
   - (int) **accountIdFrom**
   - (int) **accountIdTo**
   - (int/float) **amount**
   - (string) **currency**
