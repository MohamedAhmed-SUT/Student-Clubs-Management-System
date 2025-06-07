<?php
session_start();
require 'connect.php'; 


if (!isset($_SESSION['email'])) {
    header('Location: login.php'); 
    exit;
}

$email = $_SESSION['email'];


$stmt = $conn->prepare("SELECT firstName, lastName, email, password FROM register WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['updateProfile'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];

        $updateStmt = $conn->prepare("UPDATE register SET firstName = ?, lastName = ? WHERE email = ?");
        $updateStmt->bind_param("sss", $firstName, $lastName, $email);

        if ($updateStmt->execute()) {
            $success = "Profile updated successfully.";
        } else {
            $error = "Failed to update profile.";
        }
    } elseif (isset($_POST['changePassword'])) {
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        if ($currentPassword !== $user['password']) {
            $error = "The current password is incorrect.";
        } elseif ($newPassword !== $confirmPassword) {
            $error = "The new passwords do not match.";
        } else {
            $updateStmt = $conn->prepare("UPDATE register SET password = ? WHERE email = ?");
            $updateStmt->bind_param("ss", $newPassword, $email);
            if ($updateStmt->execute()) {
                $success = "Password updated successfully.";
            } else {
                $error = "Failed to update the password.";
            }
        }
    } elseif (isset($_POST['deleteAccount'])) {
        $deleteStmt = $conn->prepare("DELETE FROM register WHERE email = ?");
        $deleteStmt->bind_param("s", $email);

        if ($deleteStmt->execute()) {
            session_destroy();
            header('Location: index.php');
            exit;
        } else {
            $error = "Failed to delete account.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container light-style flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-4">Account Settings</h4>
        <div class="card overflow-hidden">
            <div class="row no-gutters row-bordered row-border-light">
                <div class="col-md-3 pt-0">
                    <div class="list-group list-group-flush account-settings-links">
                        <a class="list-group-item list-group-item-action active" data-toggle="list" href="#account-general">General</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#change-password">Change Password</a>
                        <a class="list-group-item list-group-item-action" data-toggle="list" href="#delete-account">Delete Account</a>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="tab-content">


                        <div class="tab-pane fade active show" id="account-general">
                            <div class="card-body">
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                <?php endif; ?>
                                <?php if (isset($success)): ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                                <?php endif; ?>
                                <form method="post">
                                    <div class="form-group">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" name="firstName" value="<?php echo htmlspecialchars($user['firstName']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="lastName" value="<?php echo htmlspecialchars($user['lastName']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                    </div>
                                    <div class="text-right mt-3">
                                        <button type="submit" class="btn btn-primary" name="updateProfile">Save Changes</button>
                                        <a href="logout.php" class="btn btn-danger">Logout</a>
                                        <a href="index.php" class="btn btn-info">Back</a>
                                    </div>
                                </form>
                            </div>
                        </div>


                        <div class="tab-pane fade" id="change-password">
                            <div class="card-body">
                                <form method="post">
                                    <div class="form-group">
                                        <label class="form-label">Current Password</label>
                                        <input type="password" class="form-control" name="currentPassword" placeholder="Enter current password" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" name="newPassword" placeholder="Enter new password" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" name="confirmPassword" placeholder="Confirm new password" required>
                                    </div>
                                    <div class="text-right mt-3">
                                        <button type="submit" class="btn btn-primary" name="changePassword">Change Password</button>
                                        <a href="logout.php" class="btn btn-danger">Logout</a>
                                        <a href="index.php" class="btn btn-info">Back</a>
                                    </div>
                                </form>
                            </div>
                        </div>


                        <div class="tab-pane fade" id="delete-account">
                            <div class="card-body">
                                <p>Deleting your account is permanent and cannot be undone. Are you sure?</p>
                                <form method="post">
                                    <div class="text-right mt-3">
                                        <button type="submit" class="btn btn-danger" name="deleteAccount">Delete Account</button>
                                        <a href="index.php" class="btn btn-info">Back</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>