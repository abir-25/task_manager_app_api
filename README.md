
![Logo](https://api-task.agameeit.com/images/logo_xs.png)


# Task Manager App (Backend)

**Built with Laravel (PHP)**

This repository contains the backend API for the Task Manager App, developed using Laravel. It provides secure RESTful endpoints for managing tasks and users.


## 🚀 Features

- User authentication (JWT-based)
- CRUD operations for tasks
- Update user profile
- Database migrations
- API documentation (Postman)
- API security best practices
- Task status and position management


## ⚙️ Tech Stack

**Backend:** Laravel (PHP)

**Database:** MySQL

**Authentication:** JWT

**Cache:** Redis

**API Docs:** Postman


## 🔧 Installation & Setup

Clone the project

```bash
git clone https://github.com/abir-25/task_manager_app_api.git
```

Go to the project directory

```bash
cd task-manager-api
```

Install dependencies

```bash
composer install
```

Configure environment variable:
- Create a .env file in the root directory
- Update database credentials
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=

```

Generate application key

```bash
php artisan key:generate
```

Run database migration

```bash
php artisan migrate
```

Serve the application

```bash
php artisan serve
```
## Running Tests

To run UserApiTest file, run the following command

```bash
php artisan test --filter UserApiTest
```


## 🛠️ Deployment

- Configure the **.env** file for production.
- Use **php artisan config:cache** and **php artisan route:cache** for optimization.
- Deploy to a server with Apache, Nginx, or Laravel Forge.

## 🔗 Live Site URL
[![Task Manager App](https://api-task.agameeit.com/images/logo_xs.png)](https://api-task.agameeit.com/)



## 📄 API Collection
- The API follows RESTful standards.
- Endpoints are available at /api/v1*.
- API documentation is available via [Postman Collection](https://drive.google.com/file/d/1bdM52wQ5GWZV20598jpPa1U9MOoU1MSu/view?usp=sharing)



## 📦 Database Backup File
[Database Backup](https://drive.google.com/file/d/1n75bVxifjlfPwi4g-cLk8tnAB0-bx8so/view?usp=sharing)

