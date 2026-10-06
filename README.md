# Sports Tournament Management System

A complete sports tournament administration platform built with PHP, MySQL, HTML, CSS and vanilla JavaScript. The application is designed for a college environment and supports admin and participant workflows for tournaments, teams, players, matches, venues, registrations and announcements.

## Features

- Admin dashboard with totals and quick status overview
- Tournament management with add, edit and delete actions
- Team and player management
- Venue and match management
- Registration workflow for users and teams
- Results and dynamic standings calculation
- Announcements and notifications
- Login, registration and role-based authorization
- Clean responsive user interface optimized for desktop and mobile

## Technologies

- PHP 8+
- MySQL
- PDO for secure database access
- HTML5, CSS3, JavaScript
- XAMPP / Apache / MySQL

## Folder Structure

- `index.php` – public landing page
- `login.php` – login page
- `register.php` – participant registration page
- `dashboard.php` – role-based redirect
- `logout.php` – session logout
- `config/database.php` – database configuration
- `includes/` – shared auth and layout helpers
- `admin/` – admin dashboard and management pages
- `user/` – participant dashboard and modules
- `assets/` – CSS and JavaScript assets
- `database/sports_tournament.sql` – full database import file

## XAMPP Setup

1. Install XAMPP.
2. Start Apache and MySQL.
3. Copy the project folder to `C:\xampp\htdocs\sports-tournament\`.
4. Open phpMyAdmin.
5. Import the database file from `database/sports_tournament.sql`.
6. Open `config/database.php` and confirm the credentials match your local MySQL setup.
7. Visit `http://localhost/sports-tournament/`.

## Database Configuration

The default local configuration is:

- Host: `127.0.0.1`
- Database: `sports_tournament`
- Username: `root`
- Password: empty string

If your XAMPP setup uses different credentials, update them in `config/database.php` before starting the app.

## Demo Login Credentials

Admin:
- Email: `admin@sports.edu`
- Password: `Admin@123`

Participant users:
- `aarav@sports.edu` / `User@123`
- `nisha@sports.edu` / `User2@123`
- `rohan@sports.edu` / `User3@123`

## Main Functionality

### Admin
- Manage tournaments, teams, players, venues and matches
- Review registration activity
- Publish announcements
- View standings and results
- Access all system statistics

### User
- Log in and view dashboard
- Browse tournaments
- Create teams and register for events
- View upcoming matches and notifications
- Read announcements

## Troubleshooting

- If the app shows database connection errors, verify the database has been imported and `config/database.php` matches your XAMPP login.
- If routes do not load, ensure the project is placed inside `C:\xampp\htdocs\sports-tournament`.
- If you see `You are not authorized...`, make sure you are logged in with the correct role.
- Clear browser cache if a redirect seems stale.

## Notes

This application is designed to be easy to understand and extend for a college project, while still meeting the main requirements for a structured sports tournament management system.
