<?php
// Include the database connection
include 'db.php';

// Start session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: sign_in.php");
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch user information
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle profile update submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_account'])) {
        // Move the user to deleted_users table before deletion
        $insert_sql = "INSERT INTO deleted_users (id, first_name, last_name, email, password, created_at) VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("issssi", $user['id'], $user['first_name'], $user['last_name'], $user['email'], $user['password'], $user['created_at']);

        if ($insert_stmt->execute()) {
            // Now delete the user from the users table
            $delete_sql = "DELETE FROM users WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $user_id);

            if ($delete_stmt->execute()) {
                session_destroy(); // Log the user out
                header("Location: sign_in.php?account_deleted=true"); // Redirect to sign-in page
                exit();
            } else {
                $error_message = "Failed to delete account. Please try again.";
            }
        } else {
            $error_message = "Failed to move account to deleted users. Please try again.";
        }
    } else {
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

        // Update query
        $update_sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssi", $first_name, $last_name, $email, $password, $user_id);

        if ($update_stmt->execute()) {
            $success_message = "Profile updated successfully!";
            // Refresh the user data
            $user['first_name'] = $first_name;
            $user['last_name'] = $last_name;
            $user['email'] = $email;
        } else {
            $error_message = "Failed to update profile. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="user-profile-page">
    <!-- Background Container -->
    <div class="profile-wrapper">
        <!-- Back Button -->
        <div class="back-button">
            <a href="home.php" class="btn">Back to Home</a>
        </div>

        <!-- Profile Section -->
        <div class="profile-container">
            <div class="profile-card">
                <!-- Circular Profile Image -->
                <form id="profile-pic-form" method="POST" enctype="multipart/form-data">
                    <label for="profile-image-input" class="profile-avatar-container">
                        <img src="avatar.jpg" alt="User Avatar" class="profile-avatar" id="profile-avatar-preview">
                        <div class="upload-overlay">Change Photo</div>
                    </label>
                    <input type="file" id="profile-image-input" name="profile_image" accept="image/*" hidden>
                </form>
                <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                <p class="member-since">Account Created: <?php echo date("d M Y", strtotime($user['created_at'])); ?></p>
            </div>

            <!-- Edit Profile Form -->
            <div class="profile-edit">
                <h2>Edit Profile</h2>
                <?php if (isset($success_message)) : ?>
                    <p class="success-message"><?php echo $success_message; ?></p>
                <?php elseif (isset($error_message)) : ?>
                    <p class="error-message"><?php echo $error_message; ?></p>
                <?php endif; ?>
                <form method="POST">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                    <label>New Password (Leave blank to keep current)</label>
                    <input type="password" name="password" placeholder="Enter new password">

                    <button type="submit" class="update-btn">Update Info</button>
                </form>
                
                <!-- Delete Account Form -->
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                    <button type="submit" name="delete_account" class="delete-btn">Delete Account</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle image preview on file selection
    document.getElementById('profile-image-input').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('profile-avatar-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>
</body>
</html>
