<?php
include 'db_connect.php';
include 'header.php';

$team_id = intval($_GET['id']);

// Get team details
$team = $conn->query("SELECT t.*, g.game_name 
                     FROM teams t
                     JOIN games g ON t.game_id = g.game_id
                     WHERE t.team_id = $team_id")->fetch_assoc();

if (!$team) {
    echo '<div class="container"><p>Team not found.</p></div>';
    include 'footer.php';
    exit();
}

// Get players for this team
$players = $conn->query("SELECT * FROM players WHERE team_id = $team_id ORDER BY player_name");

// Get matches for this team
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
                        WHERE m.team1_id = $team_id OR m.team2_id = $team_id
                        ORDER BY m.match_date DESC");
?>

<div class="card">
    <h2><?php echo $team['team_name']; ?></h2>
    <p><strong>Game:</strong> <?php echo $team['game_name']; ?></p>
    <p><strong>Coach:</strong> <?php echo $team['coach_name']; ?></p>
</div>

<h3>Players</h3>
<?php if ($players->num_rows > 0): ?>
    <table>
        <tr>
            <th>Name</th>
            <th>In-Game Name</th>
            <th>Role</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php while($player = $players->fetch_assoc()): ?>
            <tr>
                <td><?php echo $player['player_name']; ?></td>
                <td><?php echo $player['in_game_name']; ?></td>
                <td><?php echo $player['role']; ?></td>
                <td><?php echo $player['email']; ?></td>
                <td><a href="player_details.php?id=<?php echo $player['player_id']; ?>">View</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No players in this team yet.</p>
<?php endif; ?>

<h3>Match History</h3>
<?php if ($matches->num_rows > 0): ?>
    <table>
        <tr>
            <th>Tournament</th>
            <th>Opponent</th>
            <th>Score</th>
            <th>Result</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php while($match = $matches->fetch_assoc()): 
            $opponent = ($match['team1_id'] == $team_id) ? $match['team2_name'] : $match['team1_name'];
            $score = ($match['team1_id'] == $team_id) ? 
                    $match['team1_score'] . ' - ' . $match['team2_score'] : 
                    $match['team2_score'] . ' - ' . $match['team1_score'];
            $result = ($match['winner_id'] == $team_id) ? 'Win' : (($match['winner_id'] === null) ? 'TBD' : 'Loss');
        ?>
            <tr>
                <td><?php echo $match['tournament_name']; ?></td>
                <td><?php echo $opponent; ?></td>
                <td><?php echo ($match['team1_score'] !== null) ? $score : 'TBD'; ?></td>
                <td><?php echo $result; ?></td>
                <td><?php echo $match['match_date']; ?></td>
                <td><a href="match_details.php?id=<?php echo $match['match_id']; ?>">View</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No match history for this team yet.</p>
<?php endif; ?>

<a href="teams.php" class="button">Back to Teams</a>

<?php include 'footer.php'; ?>