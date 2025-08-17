<?php
include 'db_connect.php';
include 'header.php';

// Handle form submission for adding new team
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $game_id = intval($_POST['game_id']);
    $coach = $conn->real_escape_string($_POST['coach']);
    
    $sql = "INSERT INTO teams (team_name, game_id, coach_name)
            VALUES ('$name', $game_id, '$coach')";
    
    if ($conn->query($sql)) {
        echo '<div style="color: green; margin: 10px 0;">Team added successfully!</div>';
    } else {
        echo '<div style="color: red; margin: 10px 0;">Error adding team: ' . $conn->error . '</div>';
    }
}
?>

<h2>Teams</h2>

<!-- Add Team Form -->
<div class="card">
    <h3>Add New Team</h3>
    <form method="POST">
        <div class="form-group">
            <label for="name">Team Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="game_id">Game:</label>
            <select id="game_id" name="game_id" required>
                <?php
                $games = $conn->query("SELECT * FROM games");
                while($game = $games->fetch_assoc()) {
                    echo '<option value="' . $game['game_id'] . '">' . $game['game_name'] . '</option>';
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="coach">Coach Name:</label>
            <input type="text" id="coach" name="coach" required>
        </div>
        
        <button type="submit">Add Team</button>
    </form>
</div>

<!-- Teams List -->
<h3>All Teams</h3>
<?php
$teams = $conn->query("SELECT t.*, g.game_name FROM teams t JOIN games g ON t.game_id = g.game_id ORDER BY t.team_name");

if ($teams->num_rows > 0) {
    echo '<table>';
    echo '<tr><th>Name</th><th>Game</th><th>Coach</th><th>Players</th><th>Actions</th></tr>';
    while($row = $teams->fetch_assoc()) {
        $player_count = $conn->query("SELECT COUNT(*) FROM players WHERE team_id = " . $row['team_id'])->fetch_row()[0];
        echo '<tr>';
        echo '<td>' . $row['team_name'] . '</td>';
        echo '<td>' . $row['game_name'] . '</td>';
        echo '<td>' . $row['coach_name'] . '</td>';
        echo '<td>' . $player_count . ' players</td>';
        echo '<td><a href="team_details.php?id=' . $row['team_id'] . '">View</a></td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p>No teams found.</p>';
}

include 'footer.php';
?>