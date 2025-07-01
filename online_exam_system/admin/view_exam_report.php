<?php
session_start();
include '../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch exam reports
$query = "
    SELECT e.id, e.title, e.subject, e.created_at,
           COUNT(sa.student_id) AS total_students,
           COALESCE(AVG(sa.total_score), 0) AS avg_score,
           COALESCE(MAX(sa.total_score), 0) AS max_score,
           COALESCE(MIN(sa.total_score), 0) AS min_score
    FROM exams e
    LEFT JOIN student_attempts sa ON e.id = sa.exam_id
    GROUP BY e.id
    ORDER BY e.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Reports - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <!-- Header -->
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <h2>Exam Reports</h2>
        <table>
            <tr>
                <th>Exam Title</th>
                <th>Subject</th>
                <th>Date Created</th>
                <th>Total Students</th>
                <th>Average Score</th>
                <th>Highest Score</th>
                <th>Lowest Score</th>
            </tr>
            <?php while ($exam = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($exam['title']); ?></td>
                    <td><?php echo htmlspecialchars($exam['subject']); ?></td>
                    <td><?php echo $exam['created_at']; ?></td>
                    <td><?php echo $exam['total_students']; ?></td>
                    <td><?php echo number_format($exam['avg_score'], 2); ?></td>
                    <td><?php echo $exam['max_score']; ?></td>
                    <td><?php echo $exam['min_score']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

</body>
</html>
