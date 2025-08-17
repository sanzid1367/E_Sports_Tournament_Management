<?php
include 'db_connect.php';
include 'header.php';

// Handle form submission for adding new player
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $team_id = intval($_POST['team_id']);
    $player_name = $conn->real_escape_string($_POST['player_name']);
    $in_game_name = $conn->real_escape_string($_POST['in_game_name']);
    $role = $conn->real_escape_string($_POST['role']);
    $email = $conn->real_escape_string($_POST['email']);
    $dob = $conn->real_escape_string($_POST['dob']);
    
    $sql = "INSERT INTO players (team_id, player_name, in_game_name, role, email, date_of_birth)
            VALUES ($team_id, '$player_name', '$in_game_name', '$role', '$email', '$dob')";
    
    if ($conn->query($sql)) {
        echo '<div style="color: green; margin: 10px 0;">Player added successfully!</div>';
    } else {
        echo '<div style="color: red; margin: 10px 0;">Error adding player: ' . $conn->error . '</div>';
    }
}
?>

<h2>Players</h2>

<!-- Add Player Form -->
<div class="card">
    <h3>Add New Player</h3>
    <form method="POST">
        <div class="form-group">
            <label for="team_id">Team:</label>
            <select id="team_id" name="team_id" required>
                <?php
                $teams = $conn->query("SELECT * FROM teams ORDER BY team_name");
                while($team = $teams->fetch_assoc()) {
                    echo '<option value="' . $team['team_id'] . '">' . $team['team_name'] . '</option>';
                }
                ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="player_name">Player Name:</label>
            <input type="text" id="player_name" name="player_name" required>
        </div>
        
        <div class="form-group">
            <label for="in_game_name">In-Game Name:</label>
            <input type="text" id="in_game_name" name="in_game_name" required>
        </div>
        
        <div class="form-group">
            <label for="role">Role:</label>
            <input type="text" id="role" name="role">
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob">
        </div>
        
        <button type="submit">Add Player</button>
    </form>
</div>

<!-- Players List -->
<h3>All Players</h3>
<?php
$players = $conn->query("SELECT p.*, t.team_name, g.game_name 
                        FROM players p
                        JOIN teams t ON p.team_id = t.team_id
                        JOIN games g ON t.game_id = g.game_id
                        ORDER BY p.player_name");

if ($players->num_rows > 0) {
    echo '<table>';
    echo '<tr><th>Name</th><th>In-Game Name</th><th>Team</th><th>Game</th><th>Role</th><th>Email</th><th>Actions</th></tr>';
    while($row = $players->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['player_name'] . '</td>';
        echo '<td>' . $row['in_game_name'] . '</td>';
        echo '<td>' . $row['team_name'] . '</td>';
        echo '<td>' . $row['game_name'] . '</td>';
        echo '<td>' . $row['role'] . '</td>';
        echo '<td>' . $row['email'] . '</td>';
        echo '<td><a href="player_details.php?id=' . $row['player_id'] . '">View</a></td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p>No players found.</p>';
}

include 'footer.php';
?>