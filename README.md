# storee

Storee is a simple e-commerce website featuring a cart and a complete payment system using Stipe Payments.

👉 [**Live demo**](http://phpstack-856558-2958200.cloudwaysapps.com/)

![image](https://user-images.githubusercontent.com/45925914/176816426-df54e159-2345-4eb9-bb90-4d162920ba48.png)
![image](https://user-images.githubusercontent.com/45925914/176815828-e1f9d0ed-9139-42a3-aec2-6251a6410a97.png)
![image](https://user-images.githubusercontent.com/45925914/176816243-80efc62b-687b-4c18-8067-306ff2c7809b.png)

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

