# API_User

## Description
This project is a technical test for AlloVoisins. It is a REST API that allows managing users.
The constraints are in the file Exercice_Technique_AlloVoisins_Back.php at the root of the project.

## Prerequisites
- PHP >=5.3.7
- Composer
- MySQL

## Technologies
- PHP 8.2
- MySQL
- CodeIgniter 3

## Installation
1. Clone the project with `git clone https://github.com/Fl0-dev/APIUser.git`.
2. Install dependencies with the command `composer install`.
3. Create a database.
4. Copy the `.envsample` file to `.env` and modify the database connection information.
5. Enter the tokens in the `.env` file to secure the API.
6. Execute the SQL scripts contained in the file `SqlQueries.sql` at the root of the project to create the tables.
7. Start the server with the command `php -S localhost:8000`.
8. To fill the database with test data, run the command `php index.php batch seed`.

## Usage
The API is accessible at `http://localhost:8000/api/`. The available routes are as follows:
- For the frontOffice:
    - `POST /users/register`: Create a user.
    - `POST /users/login`: Log in.
    - `PUT /users/{id}`: Update a user.
- For the BackOffice:
    - `POST /admin/register`: Create an administrator.
    - `GET /admin/users`: Retrieve all users.
    - `DELETE /admin/users/{id}`: Delete a user.

2 commands are available
- `php index.php batch seed`: Fills the database with test data.
- `php index.php batch deleteInactiveUsers`: Deletes users inactive for 36 months.