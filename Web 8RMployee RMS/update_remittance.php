<?php
// --- Database Connection ---
$servername = "localhost";
$username = "u987478351_ruth";
$password = "Qwertyuiop143!";
$dbname = "u987478351_8rm_admin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database Connection Failed: ' . $conn->connect_error]);
    exit();
}

// Check if the required data was sent
if (isset($_POST['remittance_id']) && isset($_POST['amount'])) {
    $remittance_id = $_POST['remittance_id'];
    $new_amount = $_POST['amount'];

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE remittances SET amount = ? WHERE id = ?");
    $stmt->bind_param("di", $new_amount, $remittance_id); // "d" for double/decimal, "i" for integer

    if ($stmt->execute()) {
        // If the update was successful
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Amount updated successfully!']);
    } else {
        // If the update failed
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error updating amount: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    // If required data was not received
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid data received.']);
}

$conn->close();
?>