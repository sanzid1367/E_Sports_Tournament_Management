<?php
include 'db_connect.php';
include 'header.php';

$tournament_id = intval($_GET['id']);

// Get tournament details
$tournament = $conn->query("SELECT t.*, g.game_name 
                           FROM tournaments t
                           JOIN games g ON t.game_id = g.game_id
                           WHERE t.tournament_id = $tournament_id")->fetch_assoc();

if (!$tournament) {
    echo '<div class="container"><p>Tournament not found.</p></div>';
    include 'footer.php';
    exit();
}

// Get matches for this tournament
$matches = $conn->query("SELECT m.*, t1.team_name as team1_name, t2.team_name as team2_name, 
                        tr.team1_score, tr.team2_score, tr.winner_id
                        FROM matches m
                        JOIN teams t1 ON m.team1_id = t1.team_id
                        JOIN teams t2 ON m.team2_id = t2.team_id
                        LEFT JOIN match_results tr ON m.match_id = tr.match_id
                        WHERE m.tournament_id = $tournament_id
                        ORDER BY m.match_date");
?>

<div class="card">
    <h2><?php echo $tournament['tournament_name']; ?></h2>
    <p><strong>Game:</strong> <?php echo $tournament['game_name']; ?></p>
    <p><strong>Dates:</strong> <?php echo $tournament['start_date'] . ' to ' . $tournament['end_date']; ?></p>
    <p><strong>Prize Pool:</strong> $<?php echo number_format($tournament['prize_pool']); ?></p>
    <p><strong>Location:</strong> <?php echo $tournament['location']; ?></p>
    <p><strong>Status:</strong> <?php echo ucfirst($tournament['status']); ?></p>
</div>

<h3>Matches</h3>
<?php if ($matches->num_rows > 0): ?>
    <table>
        <tr>
            <th>Match</th>
            <th>Teams</th>
            <th>Score</th>
            <th>Winner</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php while($match = $matches->fetch_assoc()): ?>
            <tr>
                <td><?php echo $match['stage']; ?></td>
                <td><?php echo $match['team1_name'] . ' vs ' . $match['team2_name']; ?></td>
                <td>
                    <?php if ($match['team1_score'] !== null && $match['team2_score'] !== null): ?>
                        <?php echo $match['team1_score'] . ' - ' . $match['team2_score']; ?>
                    <?php else: ?>
                        TBD
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($match['winner_id'] == $match['team1_id']): ?>
                        <?php echo $match['team1_name']; ?>
                    <?php elseif ($match['winner_id'] == $match['team2_id']): ?>
                        <?php echo $match['team2_name']; ?>
                    <?php else: ?>
                        TBD
                    <?php endif; ?>
                </td>
                <td><?php echo $match['match_date']; ?></td>
                <td>
                    <a href="match_details.php?id=<?php echo $match['match_id']; ?>">View</a>
                    <?php if ($match['winner_id'] === null): ?>
                        | <a href="add_result.php?match_id=<?php echo $match['match_id']; ?>">Add Result</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No matches scheduled for this tournament yet.</p>
<?php endif; ?>

<a href="tournaments.php" class="button">Back to Tournaments</a>

<?php include 'footer.php'; ?>