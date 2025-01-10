<?php
// Start session for user tracking
session_start();

// Check if user is logged in, redirect if not
if (!isset($_SESSION['user_id'])) {
    header("Location: sign_in.php");
    exit();
}

// Get user details
$user_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <a href="index.php" class="btn">Log Out</a>
    </nav>

    <div class="dashboard-container">
        <div class="modules">
            <div class="module predictive-analytics">
                <i class="fas fa-chart-line"></i>
                <h2>Predictive Analytics Module</h2>
                <p><a href="predictive_analytics.php">Access Module</a></p>
            </div>
            <div class="module sos-alert">
                <i class="fas fa-exclamation-triangle"></i>
                <h2>SOS Alert</h2>
                <p><a href="sos_alert.php">Send Alert</a></p>
            </div>
            <div class="module weather-alerts">
                <i class="fas fa-cloud-sun-rain"></i>
                <h2>Weather Alerts Module</h2>
                <p><a href="weather_alerts.php">Check Alerts</a></p>
            </div>
            <div class="module user-profile">
                <i class="fas fa-user"></i>
                <h2>User Profile Module</h2>
                <p><a href="user_profile.php">View Profile</a></p>
            </div>
        </div>
    </div>
</body>
</html>
