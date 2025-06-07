<?php
include "connect.php";
session_start();
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
if (!isset($_SESSION['email']) || !isset($_SESSION['role'])) {

  header("Location:login.php");
  exit();
}

if ($_SESSION['role'] === 'user') {
  header("Location: user_clubs.php");
  exit();
}
if (isset($_POST['add_member'])) {
    $member_email = mysqli_real_escape_string($conn, $_POST['member_email']);
    $member_role = mysqli_real_escape_string($conn, $_POST['member_role']);


    $query = "INSERT INTO club_memberships (member_email, member_role) VALUES ('$member_email', '$member_role')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Member added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Management</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/Header&Footer.css">
    <style>
        .icon-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 30px;
            color: white;
            margin-bottom: 20px;
        }
        .event-icon { background-color: #007bff; }
        .member-icon { background-color: #28a745; }
        .club-icon { background-color: #ffc107; }
    </style>
</head>
<body>
<div id="header-container"></div>
<div class="container mt-5">
    <h1>Club Management</h1>
    <div class="row">
        <div class="col-md-4 text-center">
            <div class="icon-circle club-icon">
                <i class="fas fa-users"></i>
            </div>
            <h3>Club Information</h3>
            <p>Manage the club name, description, and membership details.</p>
            <a href="admin_clubs.php" class="btn btn-warning">Manage Club Information</a>
        </div>

        <div class="col-md-4 text-center">
            <div class="icon-circle member-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h3>Manage Members</h3>
            <p>remove members, and assign roles.</p>
            <a href="members.php" class="btn btn-success">Edit Members</a>
        </div>

        <div class="col-md-4 text-center">
            <div class="icon-circle event-icon">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <h3>Manage Events</h3>
            <p>Add, edit, and delete events.</p>
            <a href="admin_event.php" class="btn btn-primary">Manage Events</a>
        </div>
    </div>
</div>
<div id="footer-container"></div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
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