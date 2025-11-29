<?php
// Start the session silently.
session_start();

// The server-side gatekeeper check.
// This version does NOT send any headers, making it safe for file exports.
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    // If the user is not logged in, we stop the script immediately.
    // We do not redirect, as that would also send a conflicting header.
    session_destroy();
    die("Access Denied. You must be logged in to download this file.");
}