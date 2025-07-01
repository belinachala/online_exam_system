<?php
session_start();
include 'db.php'; // Include the database connection

/**
 * Check if the user is logged in.
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect if user is not logged in.
 */
function checkLogin() {
    if (!isLoggedIn()) {
        header("Location: ../auth/login.php");
        exit();
    }
}

/**
 * Get the currently logged-in user's details.
 */
function getUserDetails($conn, $user_id) {
    $query = "SELECT id, username, email, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Check if the logged-in user has a specific role.
 */
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Redirect if user is not an admin.
 */
function checkAdmin() {
    if (!hasRole('admin')) {
        header("Location: ../auth/login.php");
        exit();
    }
}

/**
 * Redirect if user is not a staff member.
 */
function checkStaff() {
    if (!hasRole('staff')) {
        header("Location: ../auth/login.php");
        exit();
    }
}

/**
 * Redirect if user is not a student.
 */
function checkStudent() {
    if (!hasRole('student')) {
        header("Location: ../auth/login.php");
        exit();
    }
}

/**
 * Sanitize user input.
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a random token for security purposes.
 */
function generateToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Hash a password securely.
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify a password against a hashed password.
 */
function verifyPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}

?>
