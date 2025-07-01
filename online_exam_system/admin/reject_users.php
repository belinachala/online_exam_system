<?php
session_start();
include('../includes/db.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle user rejection
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // Delete the user from the database
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('User rejected successfully!'); window.location='approve_users.php';</script>";
    } else {
        echo "<script>alert('Failed to reject the user. Please try again.'); window.location='approve_users.php';</script>";
    }
} else {
    echo "<script>alert('Invalid request!'); window.location='approve_users.php';</script>";
}
?>
