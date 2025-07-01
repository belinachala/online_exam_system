<?php
session_start();
include('../includes/db.php');

// Ensure only admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Approve or Reject question based on user input
if (isset($_GET['action'])) {
    $question_id = $_GET['question_id'];
    $action = $_GET['action'];

    if ($action == 'approve') {
        $query = "UPDATE questions SET status = 'approved' WHERE id = :question_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':question_id', $question_id);
        if ($stmt->execute()) {
            $message = "Question approved successfully!";
        } else {
            $error = "Failed to approve question.";
        }
    } elseif ($action == 'reject') {
        $query = "UPDATE questions SET status = 'rejected' WHERE id = :question_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':question_id', $question_id);
        if ($stmt->execute()) {
            $message = "Question rejected successfully!";
        } else {
            $error = "Failed to reject question.";
        }
    }
}

// Fetch pending questions
$query = "SELECT * FROM questions WHERE status = 'pending'";
$stmt = $conn->prepare($query);
$stmt->execute();
$pending_questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include('../includes/header.php'); ?>

<h2>Approve Questions</h2>
<?php if (isset($message)) echo "<p style='color: green;'>$message</p>"; ?>
<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

<table border="1">
    <tr>
        <th>Question ID</th>
        <th>Question Text</th>
        <th>Submitted By</th>
        <th>Action</th>
    </tr>
    <?php foreach ($pending_questions as $question): ?>
        <tr>
            <td><?= $question['id'] ?></td>
            <td><?= htmlspecialchars($question['question_text']) ?></td>
            <td><?= htmlspecialchars($question['submitted_by']) ?></td>
            <td>
                <a href="?action=approve&question_id=<?= $question['id'] ?>">Approve</a> |
                <a href="?action=reject&question_id=<?= $question['id'] ?>">Reject</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include('../includes/footer.php'); ?>
