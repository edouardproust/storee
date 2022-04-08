# storee

Storee is a simple e-commerce website featuring a cart and a complete payment system using Paypal. 

## technologies

- Symfony 5
- Bootstrap 4

## Requirements

- Composer

## Prod (deployment)

```bash
composer install -n
php bin/console make:migration -n
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
```

## Usefull commands

**Regenerate APP_SECRET:** `php bin/console regenerate-app-secret`