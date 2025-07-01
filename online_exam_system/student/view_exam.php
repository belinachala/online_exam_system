<?php
session_start();
include '../includes/db.php';

// Check if the user is logged in as a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch student's exam results
$result_query = "SELECT exams.name AS exam_name, student_attempts.total_score, student_attempts.completed_at 
                 FROM student_attempts 
                 JOIN exams ON student_attempts.exam_id = exams.id 
                 WHERE student_attempts.student_id = $student_id 
                 ORDER BY student_attempts.completed_at DESC";

$result_result = mysqli_query($conn, $result_query);

include '../includes/header.php';
?>

<main>
    <h2>My Exam Results</h2>
    
    <?php if (mysqli_num_rows($result_result) > 0): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Exam Name</th>
                    <th>Score</th>
                    <th>Completed At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($result = mysqli_fetch_assoc($result_result)): ?>
                    <tr>
                        <td><?php echo $result['exam_name']; ?></td>
                        <td><?php echo $result['total_score']; ?></td>
                        <td><?php echo $result['completed_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No exam results available.</p>
    <?php endif; ?>

    <a href="dashboard.php">Back to Dashboard</a>
</main>

<?php include '../includes/footer.php'; ?>
