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

### Create your directory for installing project

```bash
mkdir P6_ProjectSnowtrick_KF
-
cd P6_ProjectSnowtrick_KF
```

### Clone the project Snowtricks

```bash
git clone https://github.com/killiadmin/snowtricks.git
```

### Setup the database

```bash
mkdir database
```

### Launch the development environment

```bash
cd snowtricks
-
docker compose up -d
--
composer install or composer update (if composer exist)
---
npm install
----
nqpm run build
-----
symfony serve -d
```

### Launch the migrations

```bash
symfony d:m:m
```

### Add fakes datas

```bash
symfony console doctrine:fixtures:load
```

### Open Maildev for catch mails

```bash
symfony open:local:webmail
```

### Launch unit tests

```bash
php bin/phpunit --testdox
```
