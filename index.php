<?php
include 'db_connect.php';
include 'header.php';

// Get counts for dashboard
$games_count = $conn->query("SELECT COUNT(*) FROM games")->fetch_row()[0];
$tournaments_count = $conn->query("SELECT COUNT(*) FROM tournaments")->fetch_row()[0];
$teams_count = $conn->query("SELECT COUNT(*) FROM teams")->fetch_row()[0];
$players_count = $conn->query("SELECT COUNT(*) FROM players")->fetch_row()[0];
?>

<h2>Dashboard</h2>

<div class="stats">
    <div class="stat-card">
        <h3><?php echo $games_count; ?></h3>
        <p>Games</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $tournaments_count; ?></h3>
        <p>Tournaments</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $teams_count; ?></h3>
        <p>Teams</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $players_count; ?></h3>
        <p>Players</p>
    </div>
</div>

<h3>Upcoming Tournaments</h3>
<?php
$upcoming = $conn->query("SELECT t.*, g.game_name FROM tournaments t 
                         JOIN games g ON t.game_id = g.game_id 
                         WHERE t.status = 'upcoming' 
                         ORDER BY t.start_date LIMIT 3");

if ($upcoming->num_rows > 0) {
    echo '<div class="card-container">';
    while($row = $upcoming->fetch_assoc()) {
        echo '<div class="card">';
        echo '<h3>' . $row['tournament_name'] . '</h3>';
        echo '<p><strong>Game:</strong> ' . $row['game_name'] . '</p>';
        echo '<p><strong>Dates:</strong> ' . $row['start_date'] . ' to ' . $row['end_date'] . '</p>';
        echo '<p><strong>Prize Pool:</strong> $' . number_format($row['prize_pool']) . '</p>';
        echo '<p><strong>Location:</strong> ' . $row['location'] . '</p>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo '<p>No upcoming tournaments found.</p>';
}
?>

<h3>Recent Match Results</h3>
<?php
$matches = $conn->query("SELECT m.*, t1.team_name as team1_name, t2.team_name as team2_name, 
                        tr.team1_score, tr.team2_score, tr.winner_id, g.game_name
                        FROM matches m
                        JOIN teams t1 ON m.team1_id = t1.team_id
                        JOIN teams t2 ON m.team2_id = t2.team_id
                        JOIN tournaments t ON m.tournament_id = t.tournament_id
                        JOIN games g ON t.game_id = g.game_id
                        LEFT JOIN match_results tr ON m.match_id = tr.match_id
                        WHERE tr.winner_id IS NOT NULL
                        ORDER BY m.match_date DESC LIMIT 5");

if ($matches->num_rows > 0) {
    echo '<table>';
    echo '<tr><th>Match</th><th>Teams</th><th>Score</th><th>Winner</th><th>Date</th><th>Tournament</th></tr>';
    while($row = $matches->fetch_assoc()) {
        $winner = ($row['winner_id'] == $row['team1_id']) ? $row['team1_name'] : $row['team2_name'];
        echo '<tr>';
        echo '<td>' . $row['stage'] . '</td>';
        echo '<td>' . $row['team1_name'] . ' vs ' . $row['team2_name'] . '</td>';
        echo '<td>' . $row['team1_score'] . ' - ' . $row['team2_score'] . '</td>';
        echo '<td>' . $winner . '</td>';
        echo '<td>' . $row['match_date'] . '</td>';
        echo '<td>' . $row['game_name'] . ' - ' . $conn->query("SELECT tournament_name FROM tournaments WHERE tournament_id = " . $row['tournament_id'])->fetch_row()[0] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p>No recent match results found.</p>';
}

include 'footer.php';
?>