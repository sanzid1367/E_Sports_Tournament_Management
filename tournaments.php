<?php
include 'db_connect.php';
include 'header.php';

// Handle form submission for adding new tournament
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $game_id = intval($_POST['game_id']);
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $end_date = $conn->real_escape_string($_POST['end_date']);
    $prize_pool = floatval($_POST['prize_pool']);
    $location = $conn->real_escape_string($_POST['location']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "INSERT INTO tournaments (tournament_name, game_id, start_date, end_date, prize_pool, location, status)
            VALUES ('$name', $game_id, '$start_date', '$end_date', $prize_pool, '$location', '$status')";
    
    if ($conn->query($sql)) {
        echo '<div style="color: green; margin: 10px 0;">Tournament added successfully!</div>';
    }
     else {
        echo '<div style="color: red; margin: 10px 0;">Error adding tournament: ' . $conn->error . '</div>';
    }
}
?>

<h2>Tournaments</h2>

<!-- Add Tournament Form -->
<div class="card">
    <h3>Add New Tournament</h3>
    <form method="POST">
        <div class="form-group">
            <label for="name">Tournament Name:</label>
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
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
        </div>
        
        <div class="form-group">
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>
        </div>
        
        <div class="form-group">
            <label for="prize_pool">Prize Pool ($):</label>
            <input type="number" id="prize_pool" name="prize_pool" step="0.01" required>
        </div>
        
        <div class="form-group">
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required>
        </div>
        
        <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="upcoming">Upcoming</option>
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        
        <button type="submit">Add Tournament</button>
    </form>
</div>

<!-- Tournaments List -->
<h3>All Tournaments</h3>
<?php
$tournaments = $conn->query("SELECT t.*, g.game_name FROM tournaments t JOIN games g ON t.game_id = g.game_id ORDER BY t.start_date DESC");

if ($tournaments->num_rows > 0) {
    echo '<table>';
    echo '<tr><th>Name</th><th>Game</th><th>Dates</th><th>Prize Pool</th><th>Location</th><th>Status</th><th>Actions</th></tr>';
    while($row = $tournaments->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['tournament_name'] . '</td>';
        echo '<td>' . $row['game_name'] . '</td>';
        echo '<td>' . $row['start_date'] . ' to ' . $row['end_date'] . '</td>';
        echo '<td>$' . number_format($row['prize_pool']) . '</td>';
        echo '<td>' . $row['location'] . '</td>';
        echo '<td>' . ucfirst($row['status']) . '</td>';
        echo '<td><a href="tournament_details.php?id=' . $row['tournament_id'] . '">View</a></td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p>No tournaments found.</p>';
}

include 'footer.php';
?>