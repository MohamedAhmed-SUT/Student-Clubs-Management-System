<?php
include "connect.php";


$sql = "SELECT id, name FROM clubs";
$result = $conn->query($sql);
$clubs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $clubs[] = $row;
    }
}


$event_id = isset($_GET['id']) ? $_GET['id'] : null;
$event = null;

if ($event_id) {
    $sql = "SELECT * FROM events WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
    } else {
        echo "Event not found.";
        exit;
    }
    $stmt->close();
}


if (isset($_POST["submit"])) {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $club_id = trim($_POST['club']);
    $category = trim($_POST['category']);
    $max_capacity = trim($_POST['max_capacity']);
    $date = trim($_POST['event_date']);
    $starttime = trim($_POST['event_start_time']);
    $endtime = trim($_POST['event_end_time']);


    if (empty($name) || empty($description) || empty($club_id) || empty($category) || empty($max_capacity) || empty($date) || empty($starttime) || empty($endtime)) {
        echo "All fields are required.";
    } else {

        $sql = "UPDATE events SET name = ?, description = ?, club_id = ?, category = ?, max_capacity = ?, event_date = ?, event_start_time = ? , event_end_time = ? WHERE id = ?";;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisissss", $name, $description, $club_id, $category, $max_capacity, $date, $starttime,$endtime, $event_id);
        if ($stmt->execute()) {
            header("Location: admin_event.php?msg=Record updated successfully");
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
    <title>Edit Event Information</title>
</head>

<body>
    <div id="header-container"></div>

    <div class="container">
        <div class="text-center mb-4">
            <h3>Edit Event Information</h3>
            <p class="text-muted">Click update after changing any information</p>
        </div>

        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;">
                <div class="mb-3">
                    <label class="form-label">Name:</label>
                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($event['name'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description:</label>
                    <input type="text" class="form-control" name="description" value="<?php echo htmlspecialchars($event['description'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Club:</label>
                    <select name="club" class="form-control" required>
                        <option value="">Select Club</option>
                        <?php foreach ($clubs as $club) { ?>
                            <option value="<?php echo $club['id']; ?>" <?php echo (isset($event['club_id']) && $event['club_id'] == $club['id']) ? 'selected' : ''; ?>>
                                <?php echo $club['name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category:</label>
                    <input type="text" class="form-control" name="category" value="<?php echo htmlspecialchars($event['category'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Max Capacity:</label>
                    <input type="number" class="form-control" name="max_capacity" value="<?php echo htmlspecialchars($event['max_capacity'] ?? ''); ?>" required min="0">
                </div>

                <div class="mb-3">
                    <label class="form-label">Date:</label>
                    <input type="date" class="form-control" name="event_date" placeholder="Enter max capacity" required min="0">
                </div>

                <div class="mb-3">
                    <label class="form-label">Start Time:</label>
                    <input type="time" class="form-control" name="event_start_time" placeholder="Enter max capacity" required min="0">
                </div>

                <div class="mb-3">
                    <label class="form-label">End Time:</label>
                    <input type="time" class="form-control" name="event_end_time" placeholder="Enter max capacity" required min="0">
                </div>

                <div>
                    <button type="submit" class="btn btn-success" name="submit">Update</button>
                    <a href="admin_event.php" class="btn btn-danger">Cancel</a>
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
