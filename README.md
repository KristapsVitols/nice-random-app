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
