<?php
session_start();
include('../includes/db.php');

// Ensure only admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch all exam reports
$query = "SELECT exams.exam_name, exams.exam_date, COUNT(results.student_id) as total_students, 
                 SUM(CASE WHEN results.score >= exams.pass_score THEN 1 ELSE 0 END) as passed_students 
          FROM exams
          LEFT JOIN results ON exams.id = results.exam_id
          GROUP BY exams.id";
$stmt = $conn->prepare($query);
$stmt->execute();
$exam_reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('../includes/header.php');
?>

<h2>Manage Exam Reports</h2>

<table border = "1" cellpadding="10">
    <tr>
        <th>Exam Name</th>
        <th>Exam Date</th>
        <th>Total Students</th>
        <th>Passed Students</th>
        <th>View Details</th>
    </tr>

    <?php foreach ($exam_reports as $report): ?>
        <tr>
            <td><?= htmlspecialchars($report['exam_name']) ?></td>
            <td><?= htmlspecialchars($report['exam_date']) ?></td>
            <td><?= $report['total_students'] ?></td>
            <td><?= $report['passed_students'] ?></td>
            <td><a href="view_exam_report.php?exam_id=<?= $report['exam_id'] ?>">View Report</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include('../includes/footer.php'); ?>
