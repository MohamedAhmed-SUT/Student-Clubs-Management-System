<?php
include "connect.php";

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

$sql = "SELECT * FROM `clubs` WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "Club not found.";
    exit();
}

if (isset($_POST["submit"])) {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $contact_email = trim($_POST['contact_email']);

    $update_sql = "UPDATE `clubs` SET `name` = ?, `description` = ?, `category` = ?, `contact_email` = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $name, $description, $category, $contact_email, $id);

    if ($update_stmt->execute()) {
        header("Location: admin_clubs.php?msg=Data updated successfully");
        exit();
    } else {
        echo "Failed: " . $update_stmt->error;
    }

    $update_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/Header&Footer.css">
    <title>Edit Club Information</title>
</head>

<body>
    <div id="header-container"></div>

    <div class="container">
        <div class="text-center mb-4">
            <h3>Edit Club Information</h3>
            <p class="text-muted">Click update after changing any information</p>
        </div>

        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;">
                <div class="mb-3">
                    <label class="form-label">Name:</label>
                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description:</label>
                    <input type="text" class="form-control" name="description" value="<?php echo htmlspecialchars($row['description']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category:</label>
                    <input type="text" class="form-control" name="category" value="<?php echo htmlspecialchars($row['category']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Contact Email:</label>
                    <input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars($row['contact_email']); ?>" required>
                </div>

                <div>
                    <button type="submit" class="btn btn-success" name="submit">Update</button>
                    <a href="admin_clubs.php" class="btn btn-danger">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <div id="footer-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
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