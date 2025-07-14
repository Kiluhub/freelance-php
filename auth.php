<?php
// auth.php
session_start();

/**
 * Require the user to be logged in.
 * Optionally enforce allowed roles.
 *
 * @param array $allowedRoles Optional. Array of allowed roles: ['admin', 'student']
 */
function require_login(array $allowedRoles = []) {
    if (!isset($_SESSION['user_id'], $_SESSION['user_role'])) {
        die("❌ Access denied. Please log in.");
    }

    if (!empty($allowedRoles) && !in_array($_SESSION['user_role'], $allowedRoles)) {
        die("❌ Access denied. You do not have permission to access this page.");
    }
}
