# University Transportation and Parking Subscription System

## Project Overview

A web-based university system developed as a graduation project to organize student parking subscriptions and bus transportation services.

The system brings together student services and administrative tools for managing transportation and parking-related activities.

## Features

* Student registration and login
* Parking subscription management
* Bus line and trip information
* Bus subscription management
* Driver portal and daily schedules
* Administrative management of students, drivers, lines, trips, and subscriptions
* Email verification and password recovery

## Technologies Used

* **Frontend:** HTML, CSS, JavaScript
* **Backend:** PHP
* **Database:** MySQL
* **Local development environment:** XAMPP

## Project Structure

* `student/` — Student pages and services
* `php/` — Authentication and application-related PHP scripts
* `driver/` — Driver portal and schedule pages
* `css/` — Stylesheets
* `trips/` — Trip management
* `subscriptions/` — Subscription management
* `students/` — Student administration
* `reports/` — Reports and statistics
* `parking/` — Parking management
* `lines/` — Bus line management
* `includes/` — Shared administration components
* `drivers/` — Driver administration
* `announcements/` — Announcement management

## Local Setup

1. Install XAMPP with Apache, PHP, and MySQL.
2. Place the project folder inside `htdocs`.
3. Create a MySQL database named `university_system`.
4. Import the project's database schema and any required sample data.
5. Configure your local database connection in `php/db.php` if that is the connection path used by your code.
6. Start Apache and MySQL in XAMPP.
7. Open the project through `http://localhost/university_system/`.

**Note:** The database export/schema and database connection configuration still need to be provided separately. The exact setup steps may need adjustment to match the project's files.

## Important Note

This repository contains the project's source code. PHP and MySQL require a server environment, so the application does not run directly as a GitHub Pages website.

## Author

**Rana Balhas**

* Portfolio: [ranabalhas.github.io](https://ranabalhas.github.io)
* GitHub: [@ranabalhas](https://github.com/ranabalhas)
