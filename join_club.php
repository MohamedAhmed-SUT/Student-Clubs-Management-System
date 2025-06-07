<?php
include "connect.php";

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $club_id = $_GET['id'];

    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];  


    $checkMembershipSql = "SELECT * FROM club_memberships WHERE user_id = ? AND club_id = ?";
    $stmt = mysqli_prepare($conn, $checkMembershipSql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $club_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        header("Location: user_clubs.php?msg=You are already a member of this club");
        exit();
    } else {

        $registerClubSql = "INSERT INTO club_memberships (user_id, club_id) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $registerClubSql);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $club_id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: user_clubs.php?msg=Successfully joined the club!");
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }

    mysqli_stmt_close($stmt);
} else {
    header("Location: user_clubs.php?msg=Invalid club ID");
    exit();
}

mysqli_close($conn);
?>