# storee

Storee is a simple e-commerce website featuring a cart and a complete payment system using Paypal. 

## technologies

- Symfony 5
- Bootstrap 4

## Requirements

- Composer

## Prod (deployment)

1. Set .env variables in a **.env.local** file

2. Run commands:
```bash
composer install -n
```

3. Update **src/Config.php**

## Usefull commands

- Clear cache on prod: `cache:clear --env=prod --no-debug`

