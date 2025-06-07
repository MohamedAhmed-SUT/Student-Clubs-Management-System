<?php
include "connect.php";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $event_id = $_GET['id'];

    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];  


    $clubSql = "SELECT club_id FROM events WHERE id = ?";
    $stmt = mysqli_prepare($conn, $clubSql);
    mysqli_stmt_bind_param($stmt, "i", $event_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $event = mysqli_fetch_assoc($result);

    if ($event) {
        $club_id = $event['club_id'];


        $membershipSql = "SELECT * FROM club_memberships WHERE user_id = ? AND club_id = ?";
        $stmt = mysqli_prepare($conn, $membershipSql);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $club_id);
        mysqli_stmt_execute($stmt);
        $membershipResult = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($membershipResult) == 0) {
            header("Location: user_clubs.php?msg=You must be a member of the club hosting this event to register");
            exit();
        }
    } else {
        header("Location: admin_event.php?msg=Event not found");
        exit();
    }


    $maxCapacitySql = "SELECT max_capacity FROM events WHERE id = ?";
    $stmt = mysqli_prepare($conn, $maxCapacitySql);
    mysqli_stmt_bind_param($stmt, "i", $event_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $event = mysqli_fetch_assoc($result);

    if ($event) {
        $max_capacity = $event['max_capacity'];

        $registrationCountSql = "SELECT COUNT(*) AS registered_count FROM event_registrations WHERE event_id = ?";
        $stmt = mysqli_prepare($conn, $registrationCountSql);
        mysqli_stmt_bind_param($stmt, "i", $event_id);
        mysqli_stmt_execute($stmt);
        $registrationCountResult = mysqli_stmt_get_result($stmt);
        $registrationCount = mysqli_fetch_assoc($registrationCountResult)['registered_count'];

        if ($registrationCount >= $max_capacity) {
            echo "<p class='alert alert-warning'>The event has reached its maximum capacity.</p>";
        } else {

            $checkRegistrationSql = "SELECT * FROM event_registrations WHERE user_id = ? AND event_id = ?";
            $stmt = mysqli_prepare($conn, $checkRegistrationSql);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $event_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                echo "<p class='alert alert-warning'>You are already registered for this event.</p>";
            } else {

                $registerEventSql = "INSERT INTO event_registrations (user_id, event_id) VALUES (?, ?)";
                $stmt = mysqli_prepare($conn, $registerEventSql);
                mysqli_stmt_bind_param($stmt, "ii", $user_id, $event_id);

                if ($stmt->execute()) {
                    header("Location: user_events.php?msg=New record created successfully");
                    exit();
                } else {
                    echo "Failed: " . $stmt->error;
                }
            }
        }
    } else {
        echo "<p class='alert alert-danger'>Event not found.</p>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<p class='alert alert-danger'>Invalid event ID.</p>";
}

mysqli_close($conn);
?>
