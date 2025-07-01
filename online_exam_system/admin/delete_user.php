<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

// Prevent self-deletion
if ($user_id && $user_id != $_SESSION['user_id']) {
    try {
        $pdo->beginTransaction();
        
        // Delete related records first if needed
        // $pdo->prepare("DELETE FROM results WHERE student_id = ?")->execute([$user_id]);
        
        // Then delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        $pdo->commit();
        $_SESSION['success'] = 'User deleted successfully';
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Failed to delete user: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'You cannot delete yourself';
}

header('Location: manage_users.php');
exit();
?>