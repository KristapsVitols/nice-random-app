## Nice random app (list client accounts, list account transactions, transfer funds between client accounts), built with Laravel

### Prerequisites:
1. Have Docker Desktop installed.

### Setup guide:
1. Clone the repository: `git clone https://github.com/KristapsVitols/nice-random-app.git`
2. Go into the project folder: `cd nice-random-app`
3. Copy .env.example file: `cp .env.example .env`
4. To avoid having to install PHP/composer locally, run this command:
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

**!!IMPORTANT!!**
This app uses https://exchangerate.host/ API for currency conversion. It has free version, so just get an API key and add it in the .env file to the `CURRENCY_EXCHANGE_API_KEY` variable

### Running tests:
Within sail shell, run `vendor/bin/phpunit`
