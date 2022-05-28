# Gap-Draught

# About

This is a fictitious library catalogue application programing interface written in PHP 8 and Symfony 5.

## Technology used

* PHP 8.0
* Symfony 5.4.8
* MySQL 5.7
* Doctrine ORM
* Composer-based build

## Setup

After cloning the repo, run:
- **composer install**
- Enter your database credentials in .env-local and rename file to .env
- **php bin/console doctrine:database:create**
- **php bin/console doctrine:schema:update --force**
- **php bin/console doctrine:fixtures:load**
- **symfony server:start**

## Testing

Head over to https://127.0.0.1:8000/api or alternative test using Postman.

## References

* [Symfony](https://symfony.com/)
* [API Platform](https://api-platform.com/)

Version: 1.0