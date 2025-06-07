<?php
include "connect.php";

if (isset($_POST["submit"])) {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $contact_email = trim($_POST['contact_email']);

    if (empty($name) || empty($description) || empty($category) || empty($contact_email)) {
        echo "All fields are required.";
    } else {
        $sql = "INSERT INTO `clubs` (`name`, `description`, `category`, `contact_email`) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $description, $category, $contact_email);

        if ($stmt->execute()) {
            header("Location: admin_clubs.php?msg=New record created successfully");
            exit();
        } else {
            echo "Failed: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
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
    <title>Add New Club</title>
</head>

<body>
    <div id="header-container"></div>
    <div class="container">
        <div class="text-center mb-4">
            <h3>Add New Club</h3>
            <p class="text-muted">Complete the form below to add a new club</p>
        </div>

        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;">
                <div class="mb-3">
                    <label class="form-label">Name:</label>
                    <input type="text" class="form-control" name="name" placeholder="Enter club name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description:</label>
                    <input type="text" class="form-control" name="description" placeholder="Enter club description" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category:</label>
                    <input type="text" class="form-control" name="category" placeholder="Enter club category" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contact Email:</label>
                    <input type="email" class="form-control" name="contact_email" placeholder="name@example.com" required>
                </div>

                <div>
                    <button type="submit" class="btn btn-success" name="submit">Save</button>
                    <a href="admin_clubs.php" class="btn btn-danger">Cancel</a>
                </div>
            </form>
        </div>
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