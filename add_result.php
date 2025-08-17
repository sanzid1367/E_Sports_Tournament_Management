<?php
include 'header.php';
include 'db_connect.php';

if (isset($_GET['match_id'])) {
    $match_id = $_GET['match_id'];
    
    // Get match details
    $query = "SELECT m.match_id, m.match_date, m.stage,
                     t1.team_id as team1_id, t2.team_id as team2_id,
                     t1.team_name as team1_name, t2.team_name as team2_name,
                     tr.tournament_name, g.game_name,
                     mr.team1_score, mr.team2_score, mr.match_duration, mr.winner_id
              FROM matches m
              LEFT JOIN teams t1 ON m.team1_id = t1.team_id
              LEFT JOIN teams t2 ON m.team2_id = t2.team_id
              LEFT JOIN tournaments tr ON m.tournament_id = tr.tournament_id
              LEFT JOIN games g ON tr.game_id = g.game_id
              LEFT JOIN match_results mr ON m.match_id = mr.match_id
              WHERE m.match_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $match_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $match = $result->fetch_assoc();
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $team1_score = intval($_POST['team1_score']);
            $team2_score = intval($_POST['team2_score']);
            $winner_id = $team1_score > $team2_score ? $match['team1_id'] : 
                        ($team2_score > $team1_score ? $match['team2_id'] : NULL);
            $duration = $conn->real_escape_string($_POST['match_duration']);
            
            // Check if result exists and update, or insert new result
            $check_query = "SELECT result_id FROM match_results WHERE match_id = ?";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("i", $match_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                // Update existing result
                $update_sql = "UPDATE match_results 
                             SET team1_score = ?, team2_score = ?, winner_id = ?, match_duration = ?
                             WHERE match_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("iiisi", $team1_score, $team2_score, $winner_id, $duration, $match_id);
                
                if ($update_stmt->execute()) {
                    echo '<div class="alert alert-success">Match result updated successfully!</div>';
                } else {
                    echo '<div class="alert alert-danger">Error updating match result: ' . $conn->error . '</div>';
                }
                $update_stmt->close();
            } else {
                // Insert new result
                $insert_sql = "INSERT INTO match_results (match_id, team1_score, team2_score, winner_id, match_duration)
                             VALUES (?, ?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("iiisi", $match_id, $team1_score, $team2_score, $winner_id, $duration);
                
                if ($insert_stmt->execute()) {
                    echo '<div class="alert alert-success">Match result added successfully!</div>';
                } else {
                    echo '<div class="alert alert-danger">Error adding match result: ' . $conn->error . '</div>';
                }
                $insert_stmt->close();
            }
            
            // Refresh match data after update
            $stmt->execute();
            $result = $stmt->get_result();
            $match = $result->fetch_assoc();
        }
?>
        <div class="container mt-4">
            <h2>Update Match Result</h2>
            <div class="card">
                <div class="card-header">
                    <h3><?php echo htmlspecialchars($match['tournament_name']); ?> - <?php echo htmlspecialchars($match['game_name']); ?></h3>
                </div>
                <div class="card-body">
                    <div class="match-info mb-4">
                        <p><strong>Stage:</strong> <?php echo htmlspecialchars($match['stage']); ?></p>
                        <p><strong>Date:</strong> <?php echo date('F d, Y H:i', strtotime($match['match_date'])); ?></p>
                        <p><strong>Teams:</strong> <?php echo htmlspecialchars($match['team1_name']); ?> vs <?php echo htmlspecialchars($match['team2_name']); ?></p>
                    </div>

                    <form method="POST" class="result-form">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="team1_score"><?php echo htmlspecialchars($match['team1_name']); ?> Score:</label>
                                    <input type="number" class="form-control" id="team1_score" name="team1_score" 
                                           value="<?php echo $match['team1_score'] ?? 0; ?>" required min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="team2_score"><?php echo htmlspecialchars($match['team2_name']); ?> Score:</label>
                                    <input type="number" class="form-control" id="team2_score" name="team2_score" 
                                           value="<?php echo $match['team2_score'] ?? 0; ?>" required min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="match_duration">Match Duration (HH:MM:SS):</label>
                                    <input type="text" class="form-control" id="match_duration" name="match_duration" 
                                           value="<?php echo $match['match_duration'] ?? '00:00:00'; ?>" 
                                           pattern="([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]" 
                                           title="Please enter time in HH:MM:SS format" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">Update Result</button>
                            <a href="match_result_details.php?match_id=<?php echo $match_id; ?>" class="btn btn-secondary">View Details</a>
                            <a href="matches.php" class="btn btn-link">Back to Matches</a>
                        </div>
                    </form>
                </div>
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
