<?php
session_start();
header('Content-Type: application/json');

// --- Database Connection ---
$servername = "localhost";
$username = "u987478351_ruth";
$password = "Qwertyuiop143!";
$dbname = "u987478351_8rm_admin";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

// --- Fetch & Verify Password ---
$sql = "SELECT setting_value FROM settings WHERE setting_key = 'admin_password_hash'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$hashed_password_from_db = $row['setting_value'];
$submitted_password = $_POST['pin_pass'] ?? '';

if (password_verify($submitted_password, $hashed_password_from_db)) {
    // SUCCESS
    session_regenerate_id(true);
    $_SESSION['is_logged_in'] = true;
    $_SESSION['username'] = 'Admin';

    // --- NEW: CREATE THE CLIENT-SIDE STATUS COOKIE ---
    // This cookie is accessible by JavaScript.
    // The "0" means it will expire when the browser is closed.
    // The "/" means it's available on the entire site.
    setcookie("auth_status", "loggedin", 0, "/");

    echo json_encode(['success' => true]);
    exit();
} else {
    // FAILURE
    echo json_encode(['success' => false, 'message' => 'Invalid PIN. Please try again.']);
    exit();
}
?>