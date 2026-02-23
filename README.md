# task-manager-webapp-PR3-KUE
Die vorliegende Arbeit befasst sich mit der Entwicklung eines webbasierten Tools zur Aufgabenverwaltung, welches darauf abzielt, die Effizienz und Organisation von Projekten innerhalb einer Hochschule zu verbessern.

The present work deals with the development of a web-based task management tool aimed at improving the efficiency and organization of projects within a university.

# Task Management WebApp (Aufgabenverwaltung)

## Project Information
* **Author:** Nicolai Treichel
* **Matriculation Number:** 1144582
* **Assignment:** Komplexe Übung PR3-SU1

## Description
This project is a web-based task management tool developed for a university assignment. It allows users to create new tasks (Component 1) and manage, filter, or update the status of existing tasks asynchronously (Component 2). The application is built using PHP, MySQL, HTML, CSS, and Vanilla JavaScript without any external frameworks.

## File Structure
* `db.php` - Establishes the connection to the MySQL database.
* `index.php` - (Component 1) Start page containing the form to add new tasks and a list of all current tasks.
* `manage_tasks.php` - (Component 2) Management dashboard with filters and status update functionality. **Protected by a password.**
* `sidebar.php` - Reusable navigation menu included on the main pages.
* `style.css` - Global stylesheet for the layout and UI elements.
* `update_status.php` - Backend endpoint that handles asynchronous database updates via the Fetch API.
* `README.md` - Project documentation.

## Setup & Installation
1. **Environment:** Ensure you have a local web server running (e.g., XAMPP) with Apache and MySQL started.
2. **Database Setup:** * Open phpMyAdmin.
   * Create a database named `task_management`.
   * Execute the provided SQL script to create the `Aufgaben` table.
3. **Deployment:** Place the entire project folder inside your web server's root directory (e.g., `C:\xampp\htdocs\task-manager-webapp-PR3-KUE`).
4. **Configuration:** If your MySQL setup uses a password for the `root` user, update the credentials in `db.php`.

## Usage
* Access the application via `http://localhost/task-manager-webapp-PR3-KUE/index.php`.
* **Important Note:** To access the task management area (`manage_tasks.php`), use the password: **123**.

![Vorschau:](https://github.com/5nicolai/task-manager-webapp-PR3-KUE/blob/a28727fd25af2f213751db86b88899dcc6a54bbd/AufgabenErfassen.png)
![Vorschau2:](https://github.com/5nicolai/task-manager-webapp-PR3-KUE/blob/f0cb1194fc241f086e841018f994a03e69bb371b/AufgabenVerwalten.png)
