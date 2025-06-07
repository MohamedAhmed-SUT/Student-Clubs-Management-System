<?php
include 'connect.php';
session_start();

if (isset($_POST['signIn'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); 

    $loginQuery = $conn->prepare("SELECT * FROM register WHERE email = ?");
    $loginQuery->bind_param("s", $email);
    $loginQuery->execute();
    $result = $loginQuery->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['password'];
        $role = $row['role'];

        if (password_verify($password, $storedPassword)) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $role;
            $_SESSION['email'] = $email; 

            header("Location: index.php");
            exit();
        } else {
            $error = "Incorrect Password!";
        }
    } else {
        $error = "Email Not Found!";
    }

    $loginQuery->close();
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <link rel="stylesheet" href="assets/css/Register&Login.css" />
  </head>
  <body>
    <div class="video-container">
      <video autoplay muted loop class="background-video">
        <source src="assets/images/1.mp4" type="video/mp4" />
        Your browser does not support the video tag.
      </video>
    </div>

    <div class="container" id="signIn">
      <h1 class="form-title">Sign In</h1>
      <form method="post" action="">
        <i class="fas fa-envelope"></i>
        <div class="input-group">
          <input
            type="email"
            name="email"
            id="email"
            placeholder="Email"
            required
          />
        </div>
        <i class="fas fa-lock"></i>
        <div class="input-group">
          <input
            type="password"
            name="password"
            id="password"
            placeholder="Password"
            required
          />
        </div>
        <input type="submit" class="btn" value="Sign In" name="signIn" />
      </form>
      <div class="links">
        <p>
          Don't have an account yet? <button id="signUpButton">Sign Up</button>
        </p>
      </div>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        document
          .getElementById("signUpButton")
          .addEventListener("click", function () {
            window.location.href = "Register.php"; 
          });
      });
    </script>
  </body>
</html>
