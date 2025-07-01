<?php
session_start();
include '../includes/db.php';

// Check if the user is logged in as a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

// Fetch completed exams with paper titles
$exam_query = "
    SELECT exams.id AS exam_id, exams.end_time, papers.title
    FROM exams 
    JOIN papers ON exams.paper_id = papers.id 
    WHERE exams.status = 'completed'
    ORDER BY exams.end_time DESC
";

$exam_result = mysqli_query($conn, $exam_query);

include '../includes/header.php';
?>

<main>
    <h2>Previous Exam Papers</h2>
    
    <?php if (mysqli_num_rows($exam_result) > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 8px; border: 1px solid #ddd;">Exam Title</th>
                    <th style="text-align: left; padding: 8px; border: 1px solid #ddd;">End Time</th>
                    <th style="text-align: left; padding: 8px; border: 1px solid #ddd;">View Paper</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($exam = mysqli_fetch_assoc($exam_result)): ?>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($exam['title']); ?></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><?php echo htmlspecialchars($exam['end_time']); ?></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">
                            <a href="view_exam_paper.php?exam_id=<?php echo $exam['exam_id']; ?>" 
                               style="text-decoration: none; color: #007bff;">View Paper</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No previous exam papers available.</p>
    <?php endif; ?>

    <br>
    <a href="dashboard.php" style="text-decoration: none; color: #007bff;">Back to Dashboard</a>
</main>

<?php include '../includes/footer.php'; ?>
