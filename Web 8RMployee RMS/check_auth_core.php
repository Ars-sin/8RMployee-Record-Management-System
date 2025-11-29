<?php
// Start the session.
session_start();

// Server-side cache control headers.
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: 0");
header("Pragma: no-cache");

// The server-side gatekeeper check.
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    session_destroy();
    header("Location: index.php?error=Access Denied. Please log in.");
    exit();
}
