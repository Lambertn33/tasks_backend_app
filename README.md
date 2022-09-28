STEPS TO SETUP THE PROJECT
==========================

1. Unzip the project
2. run composer install
3. run cp .env.example .env
4. Create Mysql DB with the name of tasks_backend_app and add all DB info in .env
5. run php artisan key:generate
6. run php migrate:seed
7. run php artisan migrate:seed
8. run php artisan serve

the project is ready on http://127.0.0.1:8000 ready to serve for the frontend