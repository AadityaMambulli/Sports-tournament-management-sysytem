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
- Optional 30-day remembered email on the login form (never stores a password or logs in automatically)
- Participant profile page with validated JPG, PNG and WebP photo uploads
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
- `user/` – participant dashboard, profile and modules
- `assets/` – CSS and JavaScript assets
- `uploads/profile-photos/` – participant profile photos with Apache directory protections
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
- Manage a profile photo from **Profile**; uploads are limited to JPG, PNG and WebP images up to 2 MB
- Read announcements

## Testing the added features

### Remembered email

1. Open `http://localhost/sports-tournament/login.php` and sign in with a demo participant account.
2. Leave **Remember my email on this device for 30 days** unchecked. The login form should not retain a persistent identifier cookie.
3. Log out, sign in again with the checkbox selected, then inspect the browser's cookies for `remembered_email`. It should contain only the account email and have an expiry about 30 days away; no password is stored.
4. Log out and revisit the login page. The email should be prefilled, but you must enter your password to authenticate. Logout removes the remembered-email cookie.

### Profile photo

1. Sign in as a participant, choose **Profile** in the navigation, and upload a JPG, PNG or WebP image smaller than 2 MB.
2. Confirm that the image displays on the profile page and that the corresponding `profile-<your-user-id>.<extension>` file appears in `uploads/profile-photos/`.
3. Upload another valid image, including one with a different allowed extension, to confirm it replaces the prior photo.
4. Try a non-image file renamed with an allowed extension and an image larger than 2 MB. Both should be rejected with an error message and the existing profile photo should remain unchanged.
5. Confirm that the profile route redirects visitors who are not logged in and does not let one participant choose another account's file.

No database migration is required: each photo uses a server-generated filename based on the authenticated participant's user ID.

## Troubleshooting

- If the app shows database connection errors, verify the database has been imported and `config/database.php` matches your XAMPP login.
- If routes do not load, ensure the project is placed inside `C:\xampp\htdocs\sports-tournament`.
- If you see `You are not authorized...`, make sure you are logged in with the correct role.
- Clear browser cache if a redirect seems stale.

## Notes

This application is designed to be easy to understand and extend for a college project, while still meeting the main requirements for a structured sports tournament management system.
