<?php 
include "connect.php";
session_start();

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$userId = $_SESSION['user_id'] ?? null;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';

$sql = "SELECT * FROM clubs WHERE 1=1";

if ($search) {
    $sql .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
}

if ($categoryFilter) {
    $sql .= " AND category = '$categoryFilter'";
}

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error: " . mysqli_error($conn));
}

$categoriesResult = mysqli_query($conn, "SELECT DISTINCT category FROM clubs");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/Header&Footer.css">
    <link rel="stylesheet" href="assets/css/user_clubs.css">

</head>

<body>
    <div id="header-container"></div>

    <div class="container">
        <h2 class="text-center mb-4">Explore Our Clubs</h2>

        <!-- Search and Filter Bar -->
        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Search by name or description" value="<?php echo htmlspecialchars($search); ?>">
            <select name="category">
                <option value="">All Categories</option>
                <?php while ($category = mysqli_fetch_assoc($categoriesResult)) { ?>
                    <option value="<?php echo htmlspecialchars($category['category']); ?>" <?php echo ($categoryFilter == $category['category']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['category']); ?>
                    </option>
                <?php } ?>
            </select>
            <button type="submit" class="btn btn-primary">Search & Filter</button>
        </form>

        <!-- Club Cards -->
        <div class="club-container">
            <?php if (mysqli_num_rows($result) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { 
                    $clubName = htmlspecialchars($row['name']);
                    $clubDescription = htmlspecialchars($row['description']);
                    $clubCategory = htmlspecialchars($row['category']);
                    $contactEmail = htmlspecialchars($row['contact_email']);
                    $clubId = $row['id'];

                    $isRegisteredQuery = "SELECT * FROM club_memberships WHERE user_id = '$userId' AND club_id = '$clubId'";
                    $isRegisteredResult = mysqli_query($conn, $isRegisteredQuery);
                    $isRegistered = mysqli_num_rows($isRegisteredResult) > 0;
                ?>
                    <div class="club-card">
                        <h5><?php echo $clubName; ?></h5>
                        <p><?php echo $clubDescription; ?></p>
                        <small><strong>Category:</strong> <?php echo $clubCategory; ?></small>
                        <small><strong>Contact Email:</strong> <?php echo $contactEmail; ?></small>

                        <?php if ($isRegistered): ?>
                            <span class="btn-joined">You already joined</span>
                        <?php else: ?>
                            <a href="join_club.php?id=<?php echo urlencode($row['id']); ?>" class="btn-join">Join Club</a>
                        <?php endif; ?>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p class="text-center">No clubs found matching your criteria.</p>
            <?php } ?>
        </div>
    </div>

    <div id="footer-container"></div>

    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
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

<?php
mysqli_close($conn);
?>
