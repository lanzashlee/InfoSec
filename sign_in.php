<?php
// Include the database connection
include 'db.php';

// Start session for user login tracking
session_start();

// Initialize error message variable
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize user inputs
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // Plain password for checking

    // Prepare SQL statement to find the user by email
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User found, verify the password
        $user = $result->fetch_assoc();
        
        // Debugging: Log user data to check what is being retrieved
        error_log(print_r($user, true)); // Log user data to the error log

        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'];

            // Check if user is admin
            if ($user['is_admin'] == 1) {
                $_SESSION['is_admin'] = true; // Set session variable for admin
                
                // Log admin login in the admin_logins table
                $admin_login_sql = "INSERT INTO admin_logins (user_id) VALUES (" . $user['id'] . ")";
                if ($conn->query($admin_login_sql) === TRUE) {
                    // Redirect to admin dashboard
                    header("Location: home.php"); 
                } else {
                    // Handle potential error in inserting log
                    error_log("Error logging admin login: " . $conn->error);
                    header("Location: home.php"); // Redirect to home page for regular users
                }
            } else {
                header("Location: home.php"); // Redirect to home page for regular users
            }
            exit();
        } else {
            $error_message = "Invalid password. Please try again.";
        }
    } else {
        $error_message = "You don't have an account with that email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Sign In</h2>
        <?php if (!empty($error_message)): ?>
            <div class="error-popup">
                <div class="error-popup-content">
                    <span class="close-popup">&times;</span>
                    <p><?php echo $error_message; ?></p>
                </div>
            </div>
        <?php endif; ?>
        <form id="sign-in-form" action="sign_in.php" method="POST">
            <label for="sign-in-email">Email</label>
            <input type="email" name="email" id="sign-in-email" placeholder="Enter your email" required>
        
            <label for="sign-in-password">Password</label>
            <input type="password" name="password" id="sign-in-password" placeholder="Enter your password" required>
        
            <button type="submit">Sign In</button>
        </form>        
        <a class="toggle-link" href="sign_up.php">Don't have an account? Sign Up</a>
    </div>

    <script>
        // Close the error popup on clicking the close button
        document.querySelectorAll('.close-popup').forEach(button => {
            button.addEventListener('click', () => {
                button.parentElement.parentElement.style.display = 'none';
            });
        });
    </script>
</body>
</html>
