# Snowtricks

Snowtricks is a community site presenting tricks and various techniques around snowboarding 

### Required

    * PHP 7.4
    * Composer   
    * Symfony CLI
    * Docker
    * Docker-compose
    * NodeJs
    * Npm

### Launch the development environment

```bash
composer install
npm install
npm run build
docker compose up-d
symfony serve-d
```

### Add tests datas

```bash
symfony console doctrine:fixtures:load
```

### Launch unit tests

```bash
php bin/phpunit --testdox
```