# Catalogue des formations
The training catalog is a website for offering and administering training courses.

## Installation


Create a new project and copy the repositories into it.

```bash
composer create-project symfony/skeleton:"6.3.*" my_project_directory
```

Use [composer](https://getcomposer.org/) to install the project.

```bash
composer install
```
configure the .env, add "DATABASE_URL" and "MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0"
## Load the database with Doctrine

```bash
php bin/console d:d:c #doctrine database create
php bin/console make:migration
php bin/console d:m:m  #doctrine migrations migratenop
php bin/console d:f:l  #doctrine faker load

```
Now you can access your database; each user has 'password' as the password. The first user has the 'admin' role
