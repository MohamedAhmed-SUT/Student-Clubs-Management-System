<?php
include "connect.php";

session_start();

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT 
            club_memberships.id AS membership_id,
            club_memberships.club_id,
            club_memberships.member_role,
            register.firstName AS member_name,
            clubs.name AS club_name
        FROM 
            club_memberships
        LEFT JOIN 
            clubs ON club_memberships.club_id = clubs.id
        LEFT JOIN 
            register ON club_memberships.user_id = register.id";

if ($searchQuery) {
    $sql .= " WHERE clubs.name LIKE ?";
}

$stmt = $conn->prepare($sql);
if ($searchQuery) {
    $searchTerm = "%" . $searchQuery . "%";  
    $stmt->bind_param("s", $searchTerm);
}
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error retrieving data: " . $conn->error);
}

$clubs = [];
while ($row = $result->fetch_assoc()) {
    $clubs[$row['club_name']][] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['membership_id'], $_POST['new_role'])) {
    $membership_id = $_POST['membership_id'];
    $new_role = $_POST['new_role'];

    $stmt = $conn->prepare("SELECT club_id FROM club_memberships WHERE id = ?");
    $stmt->bind_param("i", $membership_id);
    $stmt->execute();
    $stmt->bind_result($club_id);
    $stmt->fetch();
    $stmt->close();

    if ($club_id) {
        if ($new_role !== 'Member') {
            $checkStmt = $conn->prepare("SELECT id FROM club_memberships WHERE club_id = ? AND member_role = ?");
            $checkStmt->bind_param("is", $club_id, $new_role);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                $_SESSION['message'] = "Error: The role \"$new_role\" is already assigned to another member in this club.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            }
            $checkStmt->close();
        }

        $updateSql = "UPDATE club_memberships SET member_role = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);

        if ($updateStmt) {
            $updateStmt->bind_param("si", $new_role, $membership_id);
            if ($updateStmt->execute()) {
                $_SESSION['message'] = "Role updated successfully!";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $_SESSION['message'] = "Error updating role.";
            }
            $updateStmt->close();
        } else {
            $_SESSION['message'] = "Error preparing statement: " . $conn->error;
        }
    } else {
        $_SESSION['message'] = "Error: Invalid membership ID.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_membership_id'])) {
    $delete_membership_id = $_POST['delete_membership_id'];

    $deleteSql = "DELETE FROM club_memberships WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);

    if ($deleteStmt) {
        $deleteStmt->bind_param("i", $delete_membership_id);
        if ($deleteStmt->execute()) {
            $_SESSION['message'] = "Member removed successfully!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION['message'] = "Error deleting member.";
        }
        $deleteStmt->close();
    } else {
        $_SESSION['message'] = "Error preparing statement: " . $conn->error;
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/Header&Footer.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <title>Manage Club Members</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f6f9;
            color: #444;
        }

        .container {
            margin-top: 50px;
        }

        .club-section {
            margin-bottom: 30px;
        }

        .club-section h3 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .form-select {
            display: inline-block;
            width: auto;
        }

        .search-bar {
            width: 250px;
            margin-right: 15px;
            border-radius: 25px;
            padding: 10px;
            border: 1px solid #ccc;
        }

        button[type="submit"] {
            background: linear-gradient(135deg, #6c5ce7, #00b894);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        button[type="submit"]:hover {
            background: linear-gradient(135deg, #00b894, #6c5ce7);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        button[type="submit"]:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(100, 150, 255, 0.6);
        }
    </style>
</head>
<body>

<div id="header-container"></div>

<div class="container">
    <h2 class="text-center mb-4">Manage Club Members</h2>

    <?php if (!empty($clubs)): ?>
        <div class="row mb-3">
    <div class="col d-flex justify-content-center align-items-center">
        <form method="GET" class="form-inline d-flex">
            <input type="text" name="search" class="form-control search-bar me-2" placeholder="Search clubs..." 
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</div>
        <?php foreach ($clubs as $club_name => $members): ?>
            <div class="club-section">
                <h3><?php echo htmlspecialchars($club_name); ?></h3>
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Member Name</th>
                            <th>Role</th>
                            <th>Update Role</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['member_name']); ?></td>
                                <td><?php echo htmlspecialchars($member['member_role']); ?></td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="membership_id" value="<?php echo $member['membership_id']; ?>">
                                        <select name="new_role" class="form-select">
                                            <option value="Member" <?php echo $member['member_role'] === 'Member' ? 'selected' : ''; ?>>Member</option>
                                            <option value="President" <?php echo $member['member_role'] === 'President' ? 'selected' : ''; ?>>President</option>
                                            <option value="Vice President" <?php echo $member['member_role'] === 'Vice President' ? 'selected' : ''; ?>>Vice President</option>
                                            <option value="Secretary" <?php echo $member['member_role'] === 'Secretary' ? 'selected' : ''; ?>>Secretary</option>
                                            <option value="Treasurer" <?php echo $member['member_role'] === 'Treasurer' ? 'selected' : ''; ?>>Treasurer</option>
                                            <option value="Membership Chair" <?php echo $member['member_role'] === 'Membership Chair' ? 'selected' : ''; ?>>Membership Chair</option>
                                            <option value="Communications Chair" <?php echo $member['member_role'] === 'Communications Chair' ? 'selected' : ''; ?>>Communications Chair</option>
                                            <option value="Program Chair" <?php echo $member['member_role'] === 'Program Chair' ? 'selected' : ''; ?>>Program Chair</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_membership_id" value="<?php echo $member['membership_id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center">No clubs or members found.</p>
    <?php endif; ?>
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

