<?php
include 'db_connect.php';
include 'header.php';

// Handle form submission for adding new match
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tournament_id = intval($_POST['tournament_id']);
    $team1_id = intval($_POST['team1_id']);
    $team2_id = intval($_POST['team2_id']);
    $match_date = $conn->real_escape_string($_POST['match_date']);
    $stage = $conn->real_escape_string($_POST['stage']);
    
    $sql = "INSERT INTO matches (tournament_id, team1_id, team2_id, match_date, stage)
            VALUES ($tournament_id, $team1_id, $team2_id, '$match_date', '$stage')";
    
    if ($conn->query($sql)) {
        echo '<div style="color: green; margin: 10px 0;">Match added successfully!</div>';
    } else {
        echo '<div style="color: red; margin: 10px 0;">Error adding match: ' . $conn->error . '</div>';
    }
}
?>

<h2>Matches</h2>

<!-- Add Match Form -->
<div class="card">
    <h3>Add New Match</h3>
    <form method="POST">
        <div class="form-group">
            <label for="tournament_id">Tournament:</label>
            <select id="tournament_id" name="tournament_id" required>
                <?php
                $tournaments = $conn->query("SELECT * FROM tournaments ORDER BY start_date DESC");
                while($tournament = $tournaments->fetch_assoc()) {
                    echo '<option value="' . $tournament['tournament_id'] . '">' . $tournament['tournament_name'] . '</option>';
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="team1_id">Team 1:</label>
            <select id="team1_id" name="team1_id" required>
                <?php
                $teams = $conn->query("SELECT * FROM teams ORDER BY team_name");
                while($team = $teams->fetch_assoc()) {
                    echo '<option value="' . $team['team_id'] . '">' . $team['team_name'] . '</option>';
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="team2_id">Team 2:</label>
            <select id="team2_id" name="team2_id" required>
                <?php
                $teams = $conn->query("SELECT * FROM teams ORDER BY team_name");
                while($team = $teams->fetch_assoc()) {
                    echo '<option value="' . $team['team_id'] . '">' . $team['team_name'] . '</option>';
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="match_date">Match Date & Time:</label>
            <input type="datetime-local" id="match_date" name="match_date" required>
        </div>
        
        <div class="form-group">
            <label for="stage">Stage:</label>
            <input type="text" id="stage" name="stage" required>
        </div>
        
        <button type="submit">Add Match</button>
    </form>
</div>

<!-- Matches List -->
<h3>All Matches</h3>
<?php
$matches = $conn->query("SELECT m.*, t1.team_name as team1_name, t2.team_name as team2_name, 
                        tr.team1_score, tr.team2_score, tr.winner_id, tn.tournament_name, g.game_name
                        FROM matches m
                        JOIN teams t1 ON m.team1_id = t1.team_id
                        JOIN teams t2 ON m.team2_id = t2.team_id
                        JOIN tournaments tn ON m.tournament_id = tn.tournament_id
                        JOIN games g ON tn.game_id = g.game_id
                        LEFT JOIN match_results tr ON m.match_id = tr.match_id
                        ORDER BY m.match_date DESC");

if ($matches->num_rows > 0) {
    echo '<table>';
    echo '<tr><th>Tournament</th><th>Game</th><th>Match</th><th>Teams</th><th>Score</th><th>Winner</th><th>Date</th><th>Actions</th></tr>';
    while($row = $matches->fetch_assoc()) {
        $winner = "TBD";
        if ($row['winner_id'] == $row['team1_id']) {
            $winner = $row['team1_name'];
        } elseif ($row['winner_id'] == $row['team2_id']) {
            $winner = $row['team2_name'];
        }
        
        $score = "TBD";
        if ($row['team1_score'] !== null && $row['team2_score'] !== null) {
            $score = $row['team1_score'] . " - " . $row['team2_score'];
        }
        
        echo '<tr>';
        echo '<td>' . $row['tournament_name'] . '</td>';
        echo '<td>' . $row['game_name'] . '</td>';
        echo '<td>' . $row['stage'] . '</td>';
        echo '<td>' . $row['team1_name'] . ' vs ' . $row['team2_name'] . '</td>';
        echo '<td>' . $score . '</td>';
        echo '<td>' . $winner . '</td>';
        echo '<td>' . date('F d, Y H:i', strtotime($row['match_date'])) . '</td>';
        echo '<td>
              <a href="match_result_details.php?match_id=' . $row['match_id'] . '">View Results</a> | 
              <a href="add_result.php?match_id=' . $row['match_id'] . '">Add Result</a>
              </td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p>No matches found.</p>';
}

include 'footer.php';
?>