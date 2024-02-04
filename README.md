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

### Launch the development environment

```bash
composer install
-
npm install
--
npm run build
---
docker compose up -d
----
symfony serve -d
```

### Add tests datas

```bash
symfony console doctrine:fixtures:load
```

### Launch unit tests

```bash
php bin/phpunit --testdox
```
