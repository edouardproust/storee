# storee

Storee is a simple e-commerce website featuring a cart and a complete payment system using Stipe Payments.

![image](https://user-images.githubusercontent.com/45925914/176815791-0716a2c8-1a4b-45be-b01a-31e1bab68d7a.png)
![image](https://user-images.githubusercontent.com/45925914/176815828-e1f9d0ed-9139-42a3-aec2-6251a6410a97.png)
![image](https://user-images.githubusercontent.com/45925914/176815871-2d0fc676-167f-4d88-9efd-08487c1204c6.png)


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

