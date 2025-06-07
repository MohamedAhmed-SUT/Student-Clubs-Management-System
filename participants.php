<?php
include "connect.php";

session_start();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT events.*, clubs.name AS club_name 
        FROM events 
        LEFT JOIN clubs ON events.club_id = clubs.id";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error retrieving events: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/Header&Footer.css">
    <link rel="stylesheet" href="assets/css/participants.css">
    
</head>
<body>

<div id="header-container"></div>

<div class="event-container">
    <?php while ($event = mysqli_fetch_assoc($result)): ?>
        <div class="event">
            <h2><?php echo htmlspecialchars($event['name']); ?></h2>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
            <p><strong>Club:</strong> <?php echo htmlspecialchars($event['club_name']); ?></p>

            <?php
            $event_id = $event['id'];
            $participantSql = "SELECT CONCAT(register.firstName, ' ', register.lastName) AS participant_name 
                   FROM event_registrations 
                   LEFT JOIN register ON event_registrations.user_id = register.id 
                   WHERE event_registrations.event_id = ?";

            $stmt = mysqli_prepare($conn, $participantSql);
            mysqli_stmt_bind_param($stmt, "i", $event_id);
            mysqli_stmt_execute($stmt);
            $participantResult = mysqli_stmt_get_result($stmt);
            ?>

            <h3>Participants:</h3>
            <ul>
                <?php if (mysqli_num_rows($participantResult) > 0): ?>
                    <?php while ($participant = mysqli_fetch_assoc($participantResult)): ?>
                        <li><?php echo htmlspecialchars($participant['participant_name']); ?></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No participants registered yet.</li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endwhile; ?>
</div>

<div id="footer-container"></div>

<script>
    fetch('header.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header-container').innerHTML = data;
        });

    fetch('footer.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('footer-container').innerHTML = data;
        });
</script>

</body>
</html>

<?php
mysqli_close($conn);
?>
