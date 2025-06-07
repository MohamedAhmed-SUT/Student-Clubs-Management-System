<?php
include "connect.php";

session_start(); 

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$eventFilter = isset($_GET['event_status']) ? $_GET['event_status'] : ''; 

$sql = "SELECT events.*, clubs.name AS club_name 
        FROM events 
        LEFT JOIN clubs ON events.club_id = clubs.id 
        WHERE 1=1";

if ($search) {
    $sql .= " AND (events.name LIKE '%$search%' OR events.description LIKE '%$search%')";
}

if ($categoryFilter) {
    $sql .= " AND events.category = '$categoryFilter'";
}

if ($eventFilter == 'ended') {
    $sql .= " AND CONCAT(events.event_date, ' ', events.event_end_time) < NOW()";
} elseif ($eventFilter == 'upcoming') {
    $sql .= " AND CONCAT(events.event_date, ' ', events.event_end_time) > NOW()";
}

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Error: " . mysqli_error($conn));
}

$categoriesResult = mysqli_query($conn, "SELECT DISTINCT category FROM events");

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/Header&Footer.css">
    <link rel="stylesheet" href="assets/css/user_event.css">
</head>

<body>
    <div id="header-container"></div>

    <div class="container">
        <h2 class="text-center mb-4">Available Events</h2>

        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Search by name or description" value="<?php echo htmlspecialchars($search); ?>">
            <select name="category">
                <option value="">All Categories</option>
                <?php while ($category = mysqli_fetch_assoc($categoriesResult)) { ?>
                    <option value="<?php echo htmlspecialchars($category['category']); ?>" <?php echo ($categoryFilter == $category['category']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['category']); ?>
                    </option>
                <?php } ?>
            </select>
            <button type="submit" class="btn btn-primary">Search & Filter</button>
            <!-- Event Status Filter Buttons -->
            <a href="?event_status=upcoming&search=<?php echo htmlspecialchars($search); ?>&category=<?php echo htmlspecialchars($categoryFilter); ?>" class="btn btn-success">Upcoming</a>
            <a href="?event_status=ended&search=<?php echo htmlspecialchars($search); ?>&category=<?php echo htmlspecialchars($categoryFilter); ?>" class="btn btn-danger">Ended</a>
        </form>

        <div class="event-list">
            <?php 
            if (mysqli_num_rows($result) > 0) { 
                while ($row = mysqli_fetch_assoc($result)) {
                    $eventName = htmlspecialchars($row['name']);
                    $eventDescription = htmlspecialchars($row['description']);
                    $eventCategory = htmlspecialchars($row['category']);
                    $eventMaxCapacity = htmlspecialchars($row['max_capacity']);
                    $eventClubName = htmlspecialchars($row['club_name']);
                    $eventDate = $row['event_date']; 
                    $startTime = $row['event_start_time']; 
                    $endTime = $row['event_end_time']; 

                    $eventId = $row['id'];
                    $isRegisteredQuery = "SELECT * FROM event_registrations WHERE user_id = '$userId' AND event_id = '$eventId'";
                    $isRegisteredResult = mysqli_query($conn, $isRegisteredQuery);
                    $isRegistered = mysqli_num_rows($isRegisteredResult) > 0;

                    $currentDateTime = date('Y-m-d H:i:s');
                    $eventDateTime = $eventDate . ' ' . $endTime;

                    if ($currentDateTime > $eventDateTime) {
                        $eventStatus = 'Ended';
                    } else {
                        if ($isRegistered) {
                            $eventStatus = 'Already Registered';
                        } else {
                            $eventStatus = '<a href="register_event.php?id=' . urlencode($eventId) . '" class="btn btn-primary btn-register">Register</a>';
                        }
                    }
                ?>
                    <div class="event-item">
                        <h5><?php echo $eventName; ?></h5>
                        <p>Description: <?php echo $eventDescription; ?></p>
                        <small>Category: <?php echo $eventCategory; ?></small><br>
                        <small>Max Capacity: <?php echo $eventMaxCapacity; ?></small><br>
                        <small>Club: <?php echo $eventClubName; ?></small><br>
                        <small>Status: <?php echo $eventStatus; ?></small>
                        
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p class="text-center">No events found matching your criteria.</p>
            <?php } ?>
        </div>
    </div>

    <div id="footer-container"></div>

    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
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
