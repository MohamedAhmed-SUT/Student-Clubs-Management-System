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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="assets/css/Header&Footer.css">
  <title>PHP CRUD Application</title>
</head>

<body>
  <div id="header-container"></div>
  <div class="container">
    <?php
    
    if (isset($_GET["msg"])) {
      $msg = htmlspecialchars($_GET["msg"]);
      echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
      ' . $msg . '
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
    }
    ?>
    <a href="add_new_club.php" class="btn btn-dark mb-3">Add New</a>

    <table class="table table-hover text-center">
      <thead class="table-dark">
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Name</th>
          <th scope="col">Description</th>
          <th scope="col">Category</th>
          <th scope="col">Contact Email</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT * FROM `clubs`";
        $result = mysqli_query($conn, $sql);

        if ($result) {
          while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <tr>
              <td><?php echo htmlspecialchars($row["id"]); ?></td>
              <td><?php echo htmlspecialchars($row["name"]); ?></td>
              <td><?php echo htmlspecialchars($row["description"]); ?></td>
              <td><?php echo htmlspecialchars($row["category"]); ?></td>
              <td><?php echo htmlspecialchars($row["contact_email"]); ?></td>
              <td>
                <a href="edit_clubs.php?id=<?php echo urlencode($row["id"]); ?>" class="link-dark">
                  <i class="fa-solid fa-pen-to-square fs-5 me-3"></i>
                </a>
                <a href="delete_clubs.php?id=<?php echo urlencode($row["id"]); ?>" class="link-dark" onclick="return confirm('Are you sure you want to delete this club?');">
                  <i class="fa-solid fa-trash fs-5"></i>
                </a>
              </td>
            </tr>
        <?php
          }
        } else {
          echo "<tr><td colspan='6'>Error: " . mysqli_error($conn) . "</td></tr>";
        }

        mysqli_close($conn);
        ?>
      </tbody>
    </table>
  </div>
  <div id="footer-container"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
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