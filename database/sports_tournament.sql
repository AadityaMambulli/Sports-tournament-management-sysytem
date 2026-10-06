DROP DATABASE IF EXISTS sports_tournament;
CREATE DATABASE sports_tournament;
USE sports_tournament;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    phone VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    max_players INT NOT NULL DEFAULT 11,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tournaments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sport_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    registration_deadline DATE NOT NULL,
    venue VARCHAR(150) NOT NULL,
    status ENUM('Upcoming','Registration Open','Ongoing','Completed','Cancelled') NOT NULL DEFAULT 'Upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tournament_sport FOREIGN KEY (sport_id) REFERENCES sports(id) ON DELETE RESTRICT
);

CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    captain_name VARCHAR(100) NOT NULL,
    captain_email VARCHAR(150) DEFAULT NULL,
    captain_phone VARCHAR(25) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_team_name_per_tournament (tournament_id, name),
    CONSTRAINT fk_team_tournament FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE
);

CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    phone VARCHAR(25) DEFAULT NULL,
    jersey_number INT NOT NULL,
    position VARCHAR(60) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_player_team FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
);

CREATE TABLE venues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    location VARCHAR(200) NOT NULL,
    capacity INT NOT NULL,
    facilities TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    round VARCHAR(40) NOT NULL,
    team1_id INT NOT NULL,
    team2_id INT NOT NULL,
    venue_id INT NOT NULL,
    match_date DATE NOT NULL,
    match_time TIME NOT NULL,
    score_team1 INT DEFAULT 0,
    score_team2 INT DEFAULT 0,
    winner_team_id INT DEFAULT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_match_tournament FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
    CONSTRAINT fk_match_team1 FOREIGN KEY (team1_id) REFERENCES teams(id) ON DELETE RESTRICT,
    CONSTRAINT fk_match_team2 FOREIGN KEY (team2_id) REFERENCES teams(id) ON DELETE RESTRICT,
    CONSTRAINT fk_match_venue FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE RESTRICT,
    CONSTRAINT fk_match_winner FOREIGN KEY (winner_team_id) REFERENCES teams(id) ON DELETE SET NULL
);

CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    team_id INT NOT NULL,
    registered_by INT NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_registration (tournament_id, team_id),
    CONSTRAINT fk_registration_tournament FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
    CONSTRAINT fk_registration_team FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    CONSTRAINT fk_registration_user FOREIGN KEY (registered_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_announcement_user FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    match_id INT NOT NULL,
    team_id INT NOT NULL,
    points INT NOT NULL DEFAULT 0,
    position INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_result_tournament FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
    CONSTRAINT fk_result_match FOREIGN KEY (match_id) REFERENCES matches(id) ON DELETE CASCADE,
    CONSTRAINT fk_result_team FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notification_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_tournaments_status ON tournaments(status);
CREATE INDEX idx_teams_tournament ON teams(tournament_id);
CREATE INDEX idx_matches_date ON matches(match_date);
CREATE INDEX idx_notifications_user ON notifications(user_id);

INSERT INTO sports (id, name, description, max_players, created_at) VALUES
(1, 'Cricket', 'Fast-paced team sport with strategy and power hitting.', 11, '2026-01-05 08:00:00'),
(2, 'Football', 'High-energy field sport with tactical play and endurance.', 11, '2026-01-05 08:05:00'),
(3, 'Basketball', 'Indoor court game combining speed, teamwork and shooting.', 5, '2026-01-05 08:10:00'),
(4, 'Volleyball', 'Quick reaction sport focused on serves, blocks and rallies.', 6, '2026-01-05 08:15:00'),
(5, 'Badminton', 'Precision racket sport played in singles or doubles.', 2, '2026-01-05 08:20:00'),
(6, 'Table Tennis', 'Fast indoor sport emphasizing speed and technique.', 2, '2026-01-05 08:25:00');

INSERT INTO users (id, name, email, password, role, phone, created_at) VALUES
(1, 'Admin User', 'admin@sports.edu', '$2b$10$LtybYpT2jQsUfuRQ/sbsDuvCwM1wcvREho7ptKdL8koxkFPOt3jva', 'admin', '9876543210', '2026-01-09 09:00:00'),
(2, 'Aarav Sharma', 'aarav@sports.edu', '$2b$10$E3uxYI7Wqo9wlvdNUa3SQ.SgVXBeZc8QsvUBSspMqBoq1Fr1Mjv4i', 'user', '9876501234', '2026-01-10 09:00:00'),
(3, 'Nisha Patel', 'nisha@sports.edu', '$2b$10$aO10JssmIrx.Is6JZyP4neK.dmMMaTwwzbbgoilz5DG4/xPReITY6', 'user', '9876502234', '2026-01-11 09:00:00'),
(4, 'Rohan Mehta', 'rohan@sports.edu', '$2b$10$CE1a7w3oRmQLPNEkaV8VNu9ceR2B2ZB2oHIuBDV7PYSSmoQ.V6a4y', 'user', '9876503234', '2026-01-12 09:00:00');

INSERT INTO tournaments (id, sport_id, name, description, start_date, end_date, registration_deadline, venue, status, created_at) VALUES
(1, 1, 'Inter-College Cricket League', 'A prestigious cricket championship featuring top college teams.', '2026-11-05', '2026-11-25', '2026-10-20', 'Main Sports Complex', 'Registration Open', '2026-01-20 10:00:00'),
(2, 2, 'Campus Football Cup', 'A thrilling football tournament for campus teams and student squads.', '2026-11-10', '2026-11-30', '2026-10-18', 'North Ground', 'Ongoing', '2026-01-22 10:00:00'),
(3, 3, 'Basketball Showdown', 'College basketball action with high-intensity matchups and team rivalries.', '2026-12-02', '2026-12-18', '2026-10-28', 'Indoor Arena', 'Upcoming', '2026-02-04 10:00:00'),
(4, 4, 'Volleyball Challenge', 'Regional volleyball tournament open to departmental teams.', '2026-10-30', '2026-11-12', '2026-10-12', 'Court 3', 'Completed', '2026-02-10 10:00:00');

INSERT INTO teams (id, tournament_id, name, captain_name, captain_email, captain_phone, created_at) VALUES
(1, 1, 'Blue Hawks', 'Aarav Sharma', 'aarav@college.edu', '9876501234', '2026-02-01 10:00:00'),
(2, 1, 'Royal Strikers', 'Nisha Patel', 'nisha@college.edu', '9876502234', '2026-02-01 10:20:00'),
(3, 2, 'Green United', 'Rohan Mehta', 'rohan@college.edu', '9876503234', '2026-02-02 09:30:00'),
(4, 2, 'City Falcons', 'Aarav Sharma', 'aarav@college.edu', '9876501234', '2026-02-02 10:00:00'),
(5, 4, 'Sunrise Spikers', 'Nisha Patel', 'nisha@college.edu', '9876502234', '2026-02-05 11:00:00'),
(6, 4, 'Thunder Wave', 'Rohan Mehta', 'rohan@college.edu', '9876503234', '2026-02-05 11:20:00');

INSERT INTO players (id, team_id, name, email, phone, jersey_number, position, created_at) VALUES
(1, 1, 'Ishaan Rao', 'ishaan@college.edu', '9876510001', 7, 'Batter', '2026-02-10 09:00:00'),
(2, 1, 'Karan Joshi', 'karan@college.edu', '9876510002', 18, 'Bowler', '2026-02-10 09:05:00'),
(3, 2, 'Priya Nair', 'priya@college.edu', '9876510003', 9, 'All-rounder', '2026-02-10 09:10:00'),
(4, 2, 'Yash Malhotra', 'yash@college.edu', '9876510004', 14, 'Wicketkeeper', '2026-02-10 09:15:00'),
(5, 3, 'Aditya Sen', 'aditya@college.edu', '9876510005', 10, 'Forward', '2026-02-11 09:00:00'),
(6, 3, 'Harsh Verma', 'harsh@college.edu', '9876510006', 8, 'Midfielder', '2026-02-11 09:05:00'),
(7, 5, 'Mehul Das', 'mehul@college.edu', '9876510007', 12, 'Setter', '2026-02-12 09:00:00'),
(8, 6, 'Swapnil Roy', 'swapnil@college.edu', '9876510008', 15, 'Blocker', '2026-02-12 09:05:00');

INSERT INTO venues (id, name, location, capacity, facilities, created_at) VALUES
(1, 'Main Sports Complex', 'North Campus', 1500, 'Cricket ground, sheds, floodlights, digital scoreboard', '2026-01-15 12:00:00'),
(2, 'North Ground', 'North Campus', 800, 'Football turf, running track, dugouts', '2026-01-15 12:10:00'),
(3, 'Indoor Arena', 'East Block', 600, 'Synthetic court, lighting, changing rooms', '2026-01-15 12:20:00'),
(4, 'Court 3', 'Sports Complex', 250, 'Volleyball court, benches, floodlights', '2026-01-15 12:30:00');

INSERT INTO matches (id, tournament_id, round, team1_id, team2_id, venue_id, match_date, match_time, score_team1, score_team2, winner_team_id, status, created_at) VALUES
(1, 1, 'League', 1, 2, 1, '2026-11-08', '18:00:00', 154, 148, 1, 'Completed', '2026-02-14 08:00:00'),
(2, 1, 'League', 1, 2, 1, '2026-11-15', '18:00:00', 162, 170, 2, 'Completed', '2026-02-14 08:10:00'),
(3, 2, 'Semi Final', 3, 4, 2, '2026-11-18', '17:30:00', 2, 1, 3, 'Completed', '2026-02-18 08:00:00'),
(4, 4, 'Final', 5, 6, 4, '2026-11-10', '19:00:00', 3, 1, 5, 'Completed', '2026-02-20 08:00:00'),
(5, 1, 'Final', 1, 2, 1, '2026-11-24', '19:00:00', 0, 0, NULL, 'Scheduled', '2026-02-20 08:10:00');

INSERT INTO registrations (id, tournament_id, team_id, registered_by, status, created_at) VALUES
(1, 1, 1, 2, 'Approved', '2026-02-08 09:00:00'),
(2, 1, 2, 3, 'Approved', '2026-02-08 09:10:00'),
(3, 2, 3, 2, 'Approved', '2026-02-09 09:00:00'),
(4, 2, 4, 4, 'Approved', '2026-02-09 09:10:00');

INSERT INTO announcements (id, title, content, created_by, created_at) VALUES
(1, 'Registration Deadline Extended', 'The registration deadline for the cricket league has been extended by five days. Teams may finalise their rosters by 2026-10-25.', 1, '2026-02-20 10:00:00'),
(2, 'Venue Update', 'All football semi-final fixtures will be held at the North Ground with compulsory team warm-up slots.', 1, '2026-02-21 09:00:00'),
(3, 'Player Verification', 'All team captains should verify rosters before the start of the next match window.', 1, '2026-02-22 08:30:00');

INSERT INTO results (id, tournament_id, match_id, team_id, points, position, created_at) VALUES
(1, 1, 1, 1, 3, 1, '2026-02-22 12:00:00'),
(2, 1, 2, 2, 3, 2, '2026-02-22 12:10:00'),
(3, 2, 3, 3, 3, 1, '2026-02-22 12:20:00');

INSERT INTO notifications (id, user_id, title, message, is_read, created_at) VALUES
(1, 2, 'Tournament Update', 'Your team Blue Hawks has a new fixture scheduled for 2026-11-24.', 0, '2026-02-23 12:00:00'),
(2, 2, 'Registration Approved', 'Your registration for the Inter-College Cricket League has been approved.', 0, '2026-02-24 11:00:00'),
(3, 3, 'Match Reminder', 'Your team Royal Strikers should confirm the final squad before the upcoming match.', 0, '2026-02-25 10:30:00'),
(4, 4, 'Fixture Posted', 'The Campus Football Cup semi-final has been announced with venue details.', 1, '2026-02-26 09:00:00');
