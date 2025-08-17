<?php
include 'header.php';
include 'db_connect.php';

if (isset($_GET['match_id'])) {
    $match_id = $_GET['match_id'];
    
    // Query to get match details with team names and tournament info
    $query = "SELECT m.match_id, m.match_date, m.stage,
                     t1.team_name as team1_name, t2.team_name as team2_name,
                     tr.tournament_name, g.game_name,
                     mr.team1_score, mr.team2_score, mr.match_duration,
                     winner.team_name as winner_name
              FROM matches m
              LEFT JOIN teams t1 ON m.team1_id = t1.team_id
              LEFT JOIN teams t2 ON m.team2_id = t2.team_id
              LEFT JOIN tournaments tr ON m.tournament_id = tr.tournament_id
              LEFT JOIN games g ON tr.game_id = g.game_id
              LEFT JOIN match_results mr ON m.match_id = mr.match_id
              LEFT JOIN teams winner ON mr.winner_id = winner.team_id
              WHERE m.match_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $match_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $match = $result->fetch_assoc();
?>
        <div class="container mt-4">
            <h2>Match Result Details</h2>
            <div class="card">
                <div class="card-header">
                    <h3><?php echo htmlspecialchars($match['tournament_name']); ?> - <?php echo htmlspecialchars($match['game_name']); ?></h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Match Information</h4>
                            <p><strong>Stage:</strong> <?php echo htmlspecialchars($match['stage']); ?></p>
                            <p><strong>Date:</strong> <?php echo date('F d, Y H:i', strtotime($match['match_date'])); ?></p>
                            <p><strong>Duration:</strong> <?php echo $match['match_duration'] ? $match['match_duration'] : 'Not available'; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h4>Teams & Scores</h4>
                            <div class="match-score-display">
                                <p>
                                    <strong><?php echo htmlspecialchars($match['team1_name']); ?></strong>
                                    <?php echo isset($match['team1_score']) ? $match['team1_score'] : '-'; ?>
                                    vs
                                    <?php echo isset($match['team2_score']) ? $match['team2_score'] : '-'; ?>
                                    <strong><?php echo htmlspecialchars($match['team2_name']); ?></strong>
                                </p>
                            </div>
                            <?php if ($match['winner_name']): ?>
                            <p><strong>Winner:</strong> <?php echo htmlspecialchars($match['winner_name']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <a href="matches.php" class="btn btn-primary">Back to Matches</a>
            </div>
        </div>
<?php
    } else {
        echo '<div class="container mt-4"><div class="alert alert-danger">Match not found.</div></div>';
    }
    $stmt->close();
} else {
    echo '<div class="container mt-4"><div class="alert alert-danger">No match ID specified.</div></div>';
}

include 'footer.php';
?>
