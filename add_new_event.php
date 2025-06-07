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

$name = $description = $club_id = $category = $max_capacity = $date = $starttime = $endtime = "";
$error = "";

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
        $error = "All fields are required.";
    } else {
        $startDateTime = DateTime::createFromFormat('H:i', $starttime);
        $endDateTime = DateTime::createFromFormat('H:i', $endtime);

        if ($startDateTime && $endDateTime) {
            $minimumEndTime = clone $startDateTime;
            $minimumEndTime->modify('+20 minutes');

            if ($endDateTime < $minimumEndTime) {
                $error = "The end time must be at least 20 minutes later than the start time.";
            } else {
                $sql = "INSERT INTO `events` (`name`, `description`, `club_id`, `category`, `max_capacity`, `event_date`, `event_start_time`, `event_end_time`) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("ssisssss", $name, $description, $club_id, $category, $max_capacity, $date, $starttime, $endtime);

                    if ($stmt->execute()) {
                        header("Location: admin_event.php?msg=New record created successfully");
                        exit();
                    } else {
                        $error = "Failed: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $error = "Failed to prepare statement: " . $conn->error;
                }
            }
        } else {
            $error = "Invalid time format.";
        }
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
    <title>Add New Event</title>
</head>

<body>
    <div id="header-container"></div>

    <div class="container">
        <div class="text-center mb-4">
            <h3>Add New Event</h3>
            <p class="text-muted">Complete the form below to add a new Event</p>
        </div>

        <div class="container d-flex justify-content-center">
            <form action="" method="post" style="width:50vw; min-width:300px;">
                <div class="mb-3">
                    <label class="form-label">Name:</label>
                    <input type="text" class="form-control" name="name" placeholder="Enter Event name" required value="<?php echo htmlspecialchars($name); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description:</label>
                    <input type="text" class="form-control" name="description" placeholder="Enter Event description" required value="<?php echo htmlspecialchars($description); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Club:</label>
                    <select name="club" class="form-control" required>
                        <option value="">Select Club</option>
                        <?php foreach ($clubs as $club) { ?>
                            <option value="<?php echo $club['id']; ?>" <?php echo $club_id == $club['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($club['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category:</label>
                    <input type="text" class="form-control" name="category" placeholder="Enter Event category" required value="<?php echo htmlspecialchars($category); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Max Capacity:</label>
                    <input type="number" class="form-control" name="max_capacity" placeholder="Enter max capacity" required min="0" value="<?php echo htmlspecialchars($max_capacity); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Date:</label>
                    <input type="date" class="form-control" name="event_date" required value="<?php echo htmlspecialchars($date); ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Start Time:</label>
                    <input type="time" class="form-control" name="event_start_time" required value="<?php echo htmlspecialchars($starttime); ?>">
                </div>
                    
                <div class="mb-3">
                    <label class="form-label">End Time:</label>
                    <input type="time" class="form-control" name="event_end_time" placeholder="Enter end time" required value="<?php echo htmlspecialchars($endtime); ?>">
                    <?php if (!empty($error)) : ?>
                        <div class="text-danger mt-2"><?php echo $error; ?></div>
                    <?php endif; ?>
                </div>

                <div>
                    <button type="submit" class="btn btn-success" name="submit">Save</button>
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