<?php
include 'db_connect.php';
include 'header.php';

$player_id = intval($_GET['id']);

// Get player details
$player = $conn->query("SELECT p.*, t.team_name, g.game_name
                       FROM players p
                       JOIN teams t ON p.team_id = t.team_id
                       JOIN games g ON t.game_id = g.game_id
                       WHERE p.player_id = $player_id")->fetch_assoc();

if (!$player) {
    echo '<div class="container"><p>Player not found.</p></div>';
    include 'footer.php';
    exit();
}

// Get match history for this player
$matches = $conn->query("SELECT m.*, 
                        t1.team_name as team1_name, t2.team_name as team2_name,
                        tr.team1_score, tr.team2_score, tr.winner_id,
                        tn.tournament_name, g.game_name
                        FROM matches m
                        JOIN teams t1 ON m.team1_id = t1.team_id
                        JOIN teams t2 ON m.team2_id = t2.team_id
                        JOIN tournaments tn ON m.tournament_id = tn.tournament_id
                        JOIN games g ON tn.game_id = g.game_id
                        LEFT JOIN match_results tr ON m.match_id = tr.match_id
                        WHERE t1.team_id = {$player['team_id']} OR t2.team_id = {$player['team_id']}
                        ORDER BY m.match_date DESC");
?>

<div class="card">
    <h2><?php echo $player['player_name']; ?> (<?php echo $player['in_game_name']; ?>)</h2>
    <p><strong>Team:</strong> <?php echo $player['team_name']; ?></p>
    <p><strong>Game:</strong> <?php echo $player['game_name']; ?></p>
    <p><strong>Role:</strong> <?php echo $player['role']; ?></p>
    <p><strong>Email:</strong> <?php echo $player['email']; ?></p>
    <p><strong>Date of Birth:</strong> <?php echo $player['date_of_birth']; ?></p>
</div>

<h3>Match History</h3>
<?php if ($matches->num_rows > 0): ?>
    <table>
        <tr>
            <th>Tournament</th>
            <th>Match</th>
            <th>Teams</th>
            <th>Score</th>
            <th>Result</th>
            <th>Date</th>
        </tr>
        <?php while($match = $matches->fetch_assoc()): 
            $team_result = ($match['winner_id'] == $player['team_id']) ? 'Win' : (($match['winner_id'] === null) ? 'TBD' : 'Loss');
            $score = ($match['team1_id'] == $player['team_id']) ? 
                    $match['team1_score'] . ' - ' . $match['team2_score'] : 
                    $match['team2_score'] . ' - ' . $match['team1_score'];
        ?>
            <tr>
                <td><?php echo $match['tournament_name']; ?></td>
                <td><?php echo $match['stage']; ?></td>
                <td><?php echo $match['team1_name'] . ' vs ' . $match['team2_name']; ?></td>
                <td><?php echo ($match['team1_score'] !== null) ? $score : 'TBD'; ?></td>
                <td><?php echo $team_result; ?></td>
                <td><?php echo $match['match_date']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No match history for this player yet.</p>
<?php endif; ?>

<a href="players.php" class="button">Back to Players</a>

<?php include 'footer.php'; ?>