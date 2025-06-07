<?php 
include 'connect.php';
session_start();

if (isset($_POST['signUp'])) {
    $firstName = trim($_POST['fName']);
    $lastName = trim($_POST['lName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = 'user';

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $checkEmailQuery = $conn->prepare("SELECT * FROM register WHERE email = ?");
    $checkEmailQuery->bind_param("s", $email);
    $checkEmailQuery->execute();
    $result = $checkEmailQuery->get_result();

    if ($result->num_rows > 0) {
        $error = "Email Address Already Exists!";
    } else {
        $insertQuery = $conn->prepare("INSERT INTO register (firstName, lastName, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $insertQuery->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $role);

        if ($insertQuery->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }

        $insertQuery->close();
    }

    $checkEmailQuery->close();
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <link rel="stylesheet" href="assets/css/Register&Login.css" />
    <style>
      body,
      html {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow: hidden;
        font-family: Arial, sans-serif;
      }

      .video-container {
        position: relative;
        width: 100%;
        height: 100vh;
        overflow: hidden;
      }

      .background-video {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -1;
      }

      .content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        text-align: center;
        z-index: 1;
      }

      .container {
        position: absolute;
        bottom: 10%;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 400px;
        background: rgba(176, 224, 230, 0.7);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        z-index: 2;
      }

      .input-group {
        margin-bottom: 15px;
      }

      .input-group i {
        margin-right: 10px;
      }

      .input-group input {
        width: calc(100% - 40px);
        padding: 10px;
        margin-left: 10px;
        border: 1px solid #324ab2;
        border-radius: 5px;
      }

      .btn {
        width: 100%;
        padding: 10px;
        background-color: #324ab2;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
      }

      .btn:hover {
        background-color: #121e57;
      }

      p {
        text-align: center;
      }

      button {
        background: none;
        border: none;
        color: #007bff;
        text-decoration: underline;
        cursor: pointer;
      }

      button:hover {
        color: #0056b3;
      }
    </style>
  </head>
  <body>
    <div class="video-container">
      <video autoplay muted loop class="background-video">
        <source src="assets/images/1.mp4" type="video/mp4" />
        Your browser does not support the video tag.
      </video>
    </div>

    <!-- Sign Up Form -->
    <div class="container" id="signup">
      <h1 class="form-title">Register</h1>
      <form method="post" action="">
        <i class="fas fa-user"></i>
        <div class="input-group">
          <input
            type="text"
            name="fName"
            id="fName"
            placeholder="First Name"
            required
          />
        </div>
        <i class="fas fa-user"></i>
        <div class="input-group">
          <input
            type="text"
            name="lName"
            id="lName"
            placeholder="Last Name"
            required
          />
        </div>
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
        <input type="submit" class="btn" value="Sign Up" name="signUp" />
      </form>
      <p style="padding-top: 40px">
        Already Have an Account?
        <button style="color: #c71585" id="signInButton">Sign In</button>
      </p>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        document
          .getElementById("signInButton")
          .addEventListener("click", function () {
            window.location.href = "login.php";
          });
      });
    </script>
  </body>
</html>
