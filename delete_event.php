<?php
include "connect.php";


if (isset($_GET["id"])) {
    $id = $_GET["id"];


    $stmt = $conn->prepare("DELETE FROM `events` WHERE id = ?");
    $stmt->bind_param("i", $id); 

    if ($stmt->execute()) {

        header("Location: admin_event.php?msg=Data deleted successfully");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "No ID specified.";
}

$conn->close();
?>
