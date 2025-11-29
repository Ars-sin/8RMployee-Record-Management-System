<?php
session_start();

// Unset all session variables.
$_SESSION = array();

// --- NEW: DELETE THE CLIENT-SIDE STATUS COOKIE ---
// Setting a cookie with a time in the past is the standard way to delete it.
setcookie("auth_status", "", time() - 3600, "/");

// Delete the session cookie itself.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session data on the server.
session_destroy();

// Redirect to the login page.
header("Location: index.php?status=logged_out");
exit();
?>
