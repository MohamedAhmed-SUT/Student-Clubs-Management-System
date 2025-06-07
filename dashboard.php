<?php
include "connect.php";

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location:login.php");
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT 
            event_registrations.*, 
            events.name AS event_name, 
            events.event_date, 
            events.event_start_time, 
            events.event_end_time, 
            clubs.name AS club_name
        FROM 
            event_registrations
        LEFT JOIN 
            events ON event_registrations.event_id = events.id
        LEFT JOIN 
            clubs ON events.club_id = clubs.id
        WHERE 
            event_registrations.user_id = ?";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        die("Error retrieving events: " . mysqli_error($conn));
    }
} else {
    die("Error preparing statement: " . $conn->error);
}

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/Header&Footer.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <title>Your Registered Events</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
            color: #444;
        }

        .container {
            margin-top: 50px;
        }

        .event-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .event-card h5 {
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .event-card p {
            margin: 5px 0;
        }

        .event-card .club-name {
            font-weight: bold;
            color: #2980b9;
        }

        h3 {
            margin-top: 30px;
            color: #2c3e50;
        }
    </style>
</head>
<body>

<div id="header-container"></div>

<div class="container">
    <h2 class="text-center mb-4">Your Registered Events</h2>
    <h3>Upcoming Events</h3>
    <div id="upcoming-events">
    </div>

    <h3>Ended Events</h3>
    <div id="ended-events">
    </div>
</div>

<div id="footer-container"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

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

    const events = <?php echo json_encode($events); ?>;

    function createEventCard(event) {
        return `
            <div class="event-card" data-end-time="${event.event_date} ${event.event_end_time}">
                <h5>${event.event_name}</h5>
                <p><strong>Club:</strong> <span class="club-name">${event.club_name}</span></p>
                <p><strong>Date:</strong> ${event.event_date}</p>
                <p><strong>Start Time:</strong> ${event.event_start_time}</p>
                <p><strong>End Time:</strong> ${event.event_end_time}</p>
            </div>
        `;
    }

    function renderEvents() {
        const now = new Date();
        const upcomingContainer = document.getElementById('upcoming-events');
        const endedContainer = document.getElementById('ended-events');

        upcomingContainer.innerHTML = '';
        endedContainer.innerHTML = '';

        events.forEach(event => {
            const endTime = new Date(`${event.event_date} ${event.event_end_time}`);
            const eventCard = createEventCard(event);

            if (endTime > now) {
                upcomingContainer.innerHTML += eventCard;
            } else {
                endedContainer.innerHTML += eventCard;
            }
        });
    }

    renderEvents();

    setInterval(renderEvents, 60000);
</script>
</body>
</html>
