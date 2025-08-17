<?php
include 'db_connect.php';
include 'header.php';

$match_id = intval($_GET['id']);

// Get match details
$match = $conn->query("SELECT m.*, 
                      t1.team_name as team1_name, t2.team_name as team2_name,
                      tr.team1_score, tr.team2_score, tr.winner_id, tr.match_duration,
                      tn.tournament_name, g.game_name
                      FROM matches m
                      JOIN teams t1 ON m.team1_id = t1.team_id
                      JOIN teams t2 ON m.team2_id = t2.team_id
                      JOIN tournaments tn ON m.tournament_id = tn.tournament_id
                      JOIN games g ON tn.game_id = g.game_id
                      LEFT JOIN match_results tr ON m.match_id = tr.match_id
                      WHERE m.match_id = $match_id")->fetch_assoc();

if (!$match) {
    echo '<div class="container"><p>Match not found.</p></div>';
    include 'footer.php';
    exit();
}
?>

<div class="card">
    <h2><?php echo $match['team1_name']; ?> vs <?php echo $match['team2_name']; ?></h2>
    <p><strong>Tournament:</strong> <?php echo $match['tournament_name']; ?></p>
    <p><strong>Game:</strong> <?php echo $match['game_name']; ?></p>
    <p><strong>Stage:</strong> <?php echo $match['stage']; ?></p>
    <p><strong>Date:</strong> <?php echo $match['match_date']; ?></p>
    
    <?php if ($match['team1_score'] !== null && $match['team2_score'] !== null): ?>
        <h3>Match Result</h3>
        <p><strong>Score:</strong> <?php echo $match['team1_score'] . ' - ' . $match['team2_score']; ?></p>
        <p><strong>Winner:</strong> 
            <?php echo ($match['winner_id'] == $match['team1_id']) ? $match['team1_name'] : $match['team2_name']; ?>
        </p>
        <p><strong>Duration:</strong> <?php echo $match['match_duration']; ?></p>
    <?php else: ?>
        <p><strong>Status:</strong> Not played yet</p>
    <?php endif; ?>
</div>

<a href="matches.php" class="button">Back to Matches</a>

<?php include 'footer.php'; ?>