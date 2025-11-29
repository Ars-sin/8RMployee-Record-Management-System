
<?php
// Database connection info
$servername = "localhost";
$username = "u987478351_ruth";
$password = "Qwertyuiop143!";
$database = "u987478351_8rm_admin";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

