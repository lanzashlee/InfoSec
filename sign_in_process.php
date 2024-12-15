<?php
session_start(); // Start a session to store user information

include 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Check if the form was submitted
    // Collect and sanitize user inputs
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Prepare SQL statement to find the user
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User exists, fetch the user data
        $row = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['first_name'] = $row['first_name'];
            // Redirect to home.php
            header("Location: home.php");
            exit(); // Stop further execution
        } else {
            echo "Invalid email or password."; // Password mismatch
        }
    } else {
        echo "Invalid email or password."; // User not found
    }

    // Close the connection
    $conn->close();
}
?>
