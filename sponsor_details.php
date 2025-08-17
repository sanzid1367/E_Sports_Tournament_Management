<?php
include 'db_connect.php';
include 'header.php';

$sponsor_id = intval($_GET['id']);

// Get sponsor details
$sponsor = $conn->query("SELECT * FROM sponsors WHERE sponsor_id = $sponsor_id")->fetch_assoc();

if (!$sponsor) {
    echo '<div class="container"><p>Sponsor not found.</p></div>';
    include 'footer.php';
    exit();
}

// Get sponsored tournaments
$tournaments = $conn->query("SELECT t.*, g.game_name
                           FROM tournaments t
                           JOIN games g ON t.game_id = g.game_id
                           ORDER BY t.start_date DESC");
?>

<div class="card">
    <h2><?php echo $sponsor['sponsor_name']; ?></h2>
    <p><strong>Contact Email:</strong> <?php echo $sponsor['contact_email']; ?></p>
    <p><strong>Sponsorship Amount:</strong> $<?php echo number_format($sponsor['sponsorship_amount'], 2); ?></p>
</div>

<h3>Sponsored Tournaments</h3>
<?php if ($tournaments->num_rows > 0): ?>
    <table>
        <tr>
            <th>Tournament</th>
            <th>Game</th>
            <th>Dates</th>
            <th>Prize Pool</th>
            <th>Status</th>
        </tr>
        <?php while($tournament = $tournaments->fetch_assoc()): ?>
            <tr>
                <td><?php echo $tournament['tournament_name']; ?></td>
                <td><?php echo $tournament['game_name']; ?></td>
                <td><?php echo $tournament['start_date'] . ' to ' . $tournament['end_date']; ?></td>
                <td>$<?php echo number_format($tournament['prize_pool']); ?></td>
                <td><?php echo ucfirst($tournament['status']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>This sponsor hasn't sponsored any tournaments yet.</p>
<?php endif; ?>

<a href="sponsors.php" class="button">Back to Sponsors</a>

<?php include 'footer.php'; ?>