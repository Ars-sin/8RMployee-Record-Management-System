<?php
// Function to log actions to the database.
function log_action($action, $description) {
    // Check if the session is active to get the user's name.
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $user_name = $_SESSION['user_name'] ?? 'System'; // Default to 'System' if not logged in

    // --- Database Connection ---
    // Note: This connects separately to ensure logging works even if the main script's connection is closed.
    $servername = "localhost";
    $username = "u987478351_ruth";
    $password = "Qwertyuiop143!";
    $dbname = "u987478351_8rm_admin";

    $conn_log = new mysqli($servername, $username, $password, $dbname);
    if ($conn_log->connect_error) {
        // Handle connection error if needed, but we don't want to stop the main script
        return; 
    }

    $stmt = $conn_log->prepare("INSERT INTO modification_logs (user_name, action_type, description) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $user_name, $action, $description);
        $stmt->execute();
        $stmt->close();
    }
    $conn_log->close();
}
?>