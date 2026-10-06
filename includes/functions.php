<?php

function baseUrl(): string
{
    return '/sports-tournament';
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function db(): PDO
{
    require_once __DIR__ . '/../config/database.php';
    return getDb();
}

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function setFlash(string $type, string $message): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function getFlash(): ?array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function renderFlash(): void
{
    $flash = getFlash();
    if ($flash === null) {
        return;
    }

    $classMap = [
        'success' => 'alert-success',
        'error' => 'alert-error',
        'info' => 'alert-info',
    ];

    $class = $classMap[$flash['type']] ?? 'alert-info';
    echo '<div class="alert ' . $class . '">' . e($flash['message']) . '</div>';
}

function formatDate(string $value): string
{
    if ($value === null || $value === '' || $value === '0000-00-00') {
        return 'N/A';
    }

    $date = new DateTime($value);
    return $date->format('d M Y');
}

function formatDateTime(string $value): string
{
    if ($value === null || $value === '' || $value === '0000-00-00 00:00:00') {
        return 'N/A';
    }

    $date = new DateTime($value);
    return $date->format('d M Y, h:i A');
}

function isValidId($value): bool
{
    return is_numeric($value) && (int)$value > 0;
}

function getUserById(int $id): ?array
{
    $stmt = db()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function getSportName(int $sportId): string
{
    $stmt = db()->prepare('SELECT name FROM sports WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $sportId]);
    $result = $stmt->fetchColumn();
    return $result ?: 'Unknown Sport';
}

function getTournamentName(int $tournamentId): string
{
    $stmt = db()->prepare('SELECT name FROM tournaments WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $tournamentId]);
    $result = $stmt->fetchColumn();
    return $result ?: 'Unknown Tournament';
}

function getTeamName(int $teamId): string
{
    $stmt = db()->prepare('SELECT name FROM teams WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $teamId]);
    $result = $stmt->fetchColumn();
    return $result ?: 'Unknown Team';
}

function getVenueName(int $venueId): string
{
    $stmt = db()->prepare('SELECT name FROM venues WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $venueId]);
    $result = $stmt->fetchColumn();
    return $result ?: 'Unknown Venue';
}

function statusBadgeClass(string $status): string
{
    $map = [
        'Upcoming' => 'status-upcoming',
        'Registration Open' => 'status-registration',
        'Ongoing' => 'status-ongoing',
        'Completed' => 'status-completed',
        'Cancelled' => 'status-cancelled',
    ];

    return $map[$status] ?? 'status-default';
}

function getTournamentStandings(int $tournamentId): array
{
    $pdo = db();

    $teams = $pdo->prepare('SELECT id, name FROM teams WHERE tournament_id = :tournament_id');
    $teams->execute([':tournament_id' => $tournamentId]);
    $teamRows = $teams->fetchAll();

    $standings = [];
    foreach ($teamRows as $team) {
        $standings[$team['id']] = [
            'id' => (int)$team['id'],
            'team' => $team['name'],
            'played' => 0,
            'won' => 0,
            'lost' => 0,
            'drawn' => 0,
            'points' => 0,
            'goal_for' => 0,
            'goal_against' => 0,
        ];
    }

    $matches = $pdo->prepare('SELECT * FROM matches WHERE tournament_id = :tournament_id AND status = :status');
    $matches->execute([
        ':tournament_id' => $tournamentId,
        ':status' => 'Completed',
    ]);
    $matchRows = $matches->fetchAll();

    foreach ($matchRows as $match) {
        $team1 = (int)$match['team1_id'];
        $team2 = (int)$match['team2_id'];
        $score1 = (int)($match['score_team1'] ?? 0);
        $score2 = (int)($match['score_team2'] ?? 0);

        if (!isset($standings[$team1])) {
            continue;
        }
        if (!isset($standings[$team2])) {
            continue;
        }

        $standings[$team1]['played'] += 1;
        $standings[$team2]['played'] += 1;
        $standings[$team1]['goal_for'] += $score1;
        $standings[$team1]['goal_against'] += $score2;
        $standings[$team2]['goal_for'] += $score2;
        $standings[$team2]['goal_against'] += $score1;

        if ($score1 > $score2) {
            $standings[$team1]['won'] += 1;
            $standings[$team1]['points'] += 3;
            $standings[$team2]['lost'] += 1;
        } elseif ($score2 > $score1) {
            $standings[$team2]['won'] += 1;
            $standings[$team2]['points'] += 3;
            $standings[$team1]['lost'] += 1;
        } else {
            $standings[$team1]['drawn'] += 1;
            $standings[$team2]['drawn'] += 1;
            $standings[$team1]['points'] += 1;
            $standings[$team2]['points'] += 1;
        }
    }

    usort($standings, function ($a, $b) {
        if ($a['points'] !== $b['points']) {
            return $b['points'] <=> $a['points'];
        }
        if ($a['won'] !== $b['won']) {
            return $b['won'] <=> $a['won'];
        }
        $goalDiffA = $a['goal_for'] - $a['goal_against'];
        $goalDiffB = $b['goal_for'] - $b['goal_against'];
        if ($goalDiffA !== $goalDiffB) {
            return $goalDiffB <=> $goalDiffA;
        }
        return strcmp($a['team'], $b['team']);
    });

    $position = 1;
    foreach ($standings as &$standing) {
        $standing['position'] = $position;
        $standing['goal_diff'] = $standing['goal_for'] - $standing['goal_against'];
        $position++;
    }

    return $standings;
}

function getRecentAnnouncements(int $limit = 5): array
{
    $stmt = db()->prepare('SELECT a.*, u.name as author_name FROM announcements a LEFT JOIN users u ON u.id = a.created_by ORDER BY a.created_at DESC LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getUpcomingMatches(int $limit = 5): array
{
    $stmt = db()->prepare('SELECT m.*, t1.name as team1_name, t2.name as team2_name, v.name as venue_name FROM matches m LEFT JOIN teams t1 ON t1.id = m.team1_id LEFT JOIN teams t2 ON t2.id = m.team2_id LEFT JOIN venues v ON v.id = m.venue_id WHERE m.match_date >= CURDATE() ORDER BY m.match_date ASC, m.match_time ASC LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function userIsLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function currentUser(): ?array
{
    if (!userIsLoggedIn()) {
        return null;
    }
    return getUserById((int)$_SESSION['user_id']);
}

function csrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrf(string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function requireLogin(): void
{
    if (!userIsLoggedIn()) {
        setFlash('error', 'Please log in to access that page.');
        redirect(baseUrl() . '/login.php');
    }
}

function requireAdmin(): void
{
    requireLogin();
    if ((string)$_SESSION['role'] !== 'admin') {
        setFlash('error', 'You are not authorized to perform this action.');
        redirect(baseUrl() . '/dashboard.php');
    }
}
