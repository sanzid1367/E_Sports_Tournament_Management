<?php
include 'db_connect.php';
include 'header.php';

// Handle form submission for adding new sponsor
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $amount = floatval($_POST['amount']);
    
    $sql = "INSERT INTO sponsors (sponsor_name, contact_email, sponsorship_amount)
            VALUES ('$name', '$email', $amount)";
    
    if ($conn->query($sql)) {
        echo '<div style="color: green; margin: 10px 0;">Sponsor added successfully!</div>';
    } else {
        echo '<div style="color: red; margin: 10px 0;">Error adding sponsor: ' . $conn->error . '</div>';
    }
}
?>

<h2>Sponsors</h2>

<!-- Add Sponsor Form -->
<div class="card">
    <h3>Add New Sponsor</h3>
    <form method="POST">
        <div class="form-group">
            <label for="name">Sponsor Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="email">Contact Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div class="form-group">
            <label for="amount">Sponsorship Amount ($):</label>
            <input type="number" id="amount" name="amount" step="0.01" required>
        </div>
        
        <button type="submit">Add Sponsor</button>
    </form>
</div>

<!-- Sponsors List -->
<h3>All Sponsors</h3>
<?php
$sponsors = $conn->query("SELECT * FROM sponsors ORDER BY sponsor_name");

if ($sponsors->num_rows > 0) {
    echo '<table>';
    echo '<tr><th>Name</th><th>Contact Email</th><th>Sponsorship Amount</th><th>Actions</th></tr>';
    while($row = $sponsors->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['sponsor_name'] . '</td>';
        echo '<td>' . $row['contact_email'] . '</td>';
        echo '<td>$' . number_format($row['sponsorship_amount'], 2) . '</td>';
        echo '<td><a href="sponsor_details.php?id=' . $row['sponsor_id'] . '">View</a></td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p>No sponsors found.</p>';
}

include 'footer.php';
?>