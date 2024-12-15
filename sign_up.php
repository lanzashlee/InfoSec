<?php
$host = "localhost";
$port = "3307";
$username = "root";
$password = "";
$database = "user_registration";

// Create connection
$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$emailError = "";
$successMessage = "";

// Handle sign-up process
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $created_at = date("Y-m-d H:i:s");

    // Check if the email already exists
    $check_sql = "SELECT * FROM users WHERE email = '$email'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $emailError = "The email is already taken. Please use a different email.";
    } else {
        $sql = "INSERT INTO users (first_name, last_name, email, password, created_at)
                VALUES ('$first_name', '$last_name', '$email', '$password', '$created_at')";
        if ($conn->query($sql) === TRUE) {
            $successMessage = "Sign-up successful! You can now sign in.";
        } else {
            $emailError = "Error: " . $conn->error;
        }
    }
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Sign Up</h2>
        <?php if (!empty($emailError)): ?>
            <div class="popup" id="error-popup">
                <p><?php echo $emailError; ?></p>
                <button onclick="closePopup('error-popup')">Close</button>
            </div>
        <?php elseif (!empty($successMessage)): ?>
            <div class="popup" id="success-popup">
                <p><?php echo $successMessage; ?></p>
                <button onclick="closePopup('success-popup')">Close</button>
            </div>
        <?php endif; ?>
        <form id="sign-up-form" method="POST">
            <label for="first-name">First Name</label>
            <input type="text" name="first_name" id="first-name" placeholder="Enter your first name" required>

            <label for="last-name">Last Name</label>
            <input type="text" name="last_name" id="last-name" placeholder="Enter your last name" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>

            <button type="submit">Sign Up</button>
        </form>
        <a class="toggle-link" href="sign_in.php">Already have an account? Sign In</a>
    </div>

    <script>
        function closePopup(popupId) {
            document.getElementById(popupId).style.display = 'none';
        }

        // Automatically show the popup if it's present
        const errorPopup = document.getElementById('error-popup');
        const successPopup = document.getElementById('success-popup');
        if (errorPopup) errorPopup.style.display = 'block';
        if (successPopup) successPopup.style.display = 'block';
    </script>
</body>
</html>
