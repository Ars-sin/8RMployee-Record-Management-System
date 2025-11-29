<?php
// Set the content type of the response to JSON
header('Content-Type: application/json');

// Include the database configuration file
require 'config.php';

// Initialize the response array
$response = array();

// Check for database connection errors
if ($conn->connect_error) {
    // If connection fails, return a JSON error message and stop execution
    $response['error'] = true;
    $response['message'] = "Connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit(); // Stop the script
}

// --- Step 1: Check if the request method is POST ---
// This is the block of code sending you the error message because you are likely using a GET request.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['error'] = true;
    $response['message'] = "Invalid request method. Only POST is accepted.";
    echo json_encode($response);
    exit(); // Stop the script
}

// --- Step 2: Check if required parameters are present ---
if (isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    
    // Use trim() to remove any accidental whitespace from user input
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // --- Step 3: Perform Input Validation ---

    // Validate username: not empty, and has a minimum length
    if (strlen($username) < 3) {
        $response['error'] = true;
        $response['message'] = "Username must be at least 3 characters long.";
    } 
    // Validate email: not empty and has a valid format
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['error'] = true;
        $response['message'] = "Invalid email format.";
    } 
    // Validate password: not empty and has a minimum length
    else if (strlen($password) < 8) {
        $response['error'] = true;
        $response['message'] = "Password must be at least 8 characters long.";
    } 
    // If all initial validations pass, proceed to database checks
    else {
        // --- Step 4: Check if username or email already exists to prevent duplicates ---
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result(); // Needed to check num_rows

        if ($stmt->num_rows > 0) {
            $response['error'] = true;
            $response['message'] = "This email or username is already registered.";
        } else {
            // --- Step 5: Hash the password and insert the new user ---
            
            // Hash the password for secure storage
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare the INSERT statement
            $stmt_insert = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("sss", $username, $email, $hashed_password);

            // Execute the statement and check for success
            if ($stmt_insert->execute()) {
                $response['error'] = false;
                $response['message'] = "User registered successfully!";
            } else {
                $response['error'] = true;
                $response['message'] = "An error occurred during registration. Please try again.";
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }

} else {
    // If required parameters are missing from the POST request
    $response['error'] = true;
    $response['message'] = "Required parameters (username, email, password) are missing!";
}

// --- Final Step: Send the JSON response and close the connection ---
echo json_encode($response);
$conn->close();
?>