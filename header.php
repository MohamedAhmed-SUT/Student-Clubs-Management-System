<?php
session_start();
include("connect.php");
?>

<header>
    <nav class="navbar">
        <!-- Logo -->
        <div class="navbar-logo">
            <a href="index.php">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQxWKF5wJaytJPDm_ev610oZeLdIQWA8MLUyg&s" alt="Student Club Logo" />
            </a>
        </div>

        <!-- Navigation Links -->
        <ul class="navbar-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="admin_event.php">Events</a></li>
            <li><a href="admin_clubs.php">Clubs</a></li>
            <?php
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                echo '<li><a href="members.php">Members</a></li>';
                echo '<li><a href="participants.php">Participants</a></li>';
            } else {
                echo '<li><a href="dashboard.php">Dashboard</a></li>';
                echo '<li><a href="#footer-container">Contact</a></li>';
            }
            ?>
        </ul>

        <!-- User Options -->
        <?php
        if (isset($_SESSION['email'])) {
            $email = $_SESSION['email'];
            $stmt = $conn->prepare("SELECT firstName, lastName FROM register WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $firstName = htmlspecialchars($row['firstName']);
                $lastName = htmlspecialchars($row['lastName']);
            } else {
                $firstName = "User";
                $lastName = "";
            }
            ?>
            <div style="display: flex; align-items: center; gap: 10px; color: white;">
                <a href="profile.php" style="text-decoration: none; color: white;">
                    <p style="margin: 0;" class="btn-login">
                        Hello <?= $firstName . ' ' . $lastName; ?>
                    </p>
                </a>
                <a href="logout.php" style="color: white; text-decoration: none;" class="btn-login">Logout</a>
            </div>
        <?php
        } else {
            ?>
            <div class="navbar-user">
                <a href="login.php" class="btn-login" id="loginBtn">Login</a>
                <a href="register.php" class="btn-register" id="registerBtn">Register</a>
            </div>
        <?php
        }
        ?>
    </nav>
</header>
