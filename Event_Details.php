<?php
include "connect.php";

function getEventStatus($eventDate, $endTime) {
    $currentDateTime = date('Y-m-d H:i:s');
    $eventDateTime = $eventDate . ' ' . $endTime;

    if ($currentDateTime > $eventDateTime) {
        return 'Ended';
    } else {
        return 'Upcoming';
    }
}

if (isset($_GET['id'])) {
    $eventId = $_GET['id'];

    $sql = "SELECT * FROM events WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }

    $stmt->bind_param("i", $eventId);

    if (!$stmt->execute()) {
        echo "Error executing query: " . $stmt->error;
        exit;
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
        $eventName = htmlspecialchars($event['name']);
        $eventDescription = htmlspecialchars($event['description']);
        $eventCategory = htmlspecialchars($event['category']);
        $eventMaxCapacity = htmlspecialchars($event['max_capacity']);
        $eventDate = htmlspecialchars($event['event_date']);           
        $startTime = htmlspecialchars($event['event_start_time']);     
        $endTime = htmlspecialchars($event['event_end_time']);         
        $eventStatus = getEventStatus($eventDate, $endTime);
    } else {
        echo "Event not found.";
        exit;
    }
} else {
    echo "Invalid event ID.";
    exit;
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #eef1f5;
            color: #444;
        }

        .event-details-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .event-details {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 700px;
            text-align: left;
            transition: box-shadow 0.3s ease;
        }

        .event-details:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .event-details h1 {
            font-size: 2rem;
            color: #2c3e50;
            border-bottom: 2px solid #2980b9;
            padding-bottom: 10px;
            margin-bottom: 30px;
            text-transform: capitalize;
        }

        .event-details p {
            font-size: 1.1rem;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .event-details p strong {
            display: inline-block;
            min-width: 150px;
            color: #2980b9;
        }

        .status {
            padding: 8px 15px;
            border-radius: 5px;
            color: #fff;
            display: inline-block;
            font-weight: bold;
        }

        .status.upcoming {
            background-color: #27ae60;
        }

        .status.ended {
            background-color: #e74c3c;
        }
    </style>
</head>
<body>

    <div class="event-details-container">
        <div class="event-details">
            <h1><?php echo $eventName; ?></h1>
            <p><strong>Description:</strong> <?php echo $eventDescription; ?></p>
            <p><strong>Category:</strong> <?php echo $eventCategory; ?></p>
            <p><strong>Max Capacity:</strong> <?php echo $eventMaxCapacity; ?></p>
            <p><strong>Date:</strong> <?php echo $eventDate; ?></p>
            <p><strong>Start Time:</strong> <?php echo $startTime; ?></p>
            <p><strong>End Time:</strong> <?php echo $endTime; ?></p>
            <p>
                <strong>Status:</strong> 
                <span class="status <?php echo strtolower($eventStatus); ?>">
                    <?php echo $eventStatus; ?>
                </span>
            </p>
        </div>
    </div>

</body>
</html>

<?php
mysqli_close($conn);
?>
