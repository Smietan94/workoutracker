# workoutracker 

## Description
Simple workout tracker web application, which is using PHP Slim framework. Its created if You want to change your notebook with small web app, that is able to save your workoutplans and track gym progress.

## Installation
1. Create `.env` file based on `.env.example`
2. Create a Docker container by running the following command in your terminal (remember! You use this command from your docker directory): _`docker-compose up -d --build`_
3. In Docker db container create workoutracker database
    - to open Docker container run _`docker exec -it workoutracker-db bash`_
    - in db container console run _`mysql -u DB_USER -p`_
    - then create new database _`CREATE DATABASE DB_NAME;`_
5. In docker workoutracker-app container:
    - to open Docker container run _`docker exec -it workoutracker-app bash`_
    - run _`composer install`_
    - perform migrations by using
    ```
    php workoutracker migrations:diff
    php workoutracker migrations:migrate
    ```
7. Exit Docker container and in terminal run
    - run _`npm install --force`_
    - run _`yarn dev`_
8. Now, open your browser and go to `localhost:8000`. You can create account and start using Workoutracker.

## Author:
[Smietan94](https://github.com/Smietan94)