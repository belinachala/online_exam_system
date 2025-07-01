<?php
session_start(); 
// Include database connection
include('../includes/db.php');
// Restrict access to staff only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../auth/login.php");
    exit();
}

$staff_id = $_SESSION['user_id'];
$message = "";

// Fetch exams created by this staff (UPDATED QUERY TO INCLUDE TITLE AND SUBJECT)
$query = "SELECT id, paper_id, created_at FROM exams WHERE created_by = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$exams = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="css/view_results.css">
</head>
<body>

    <div class="container">
        
        </nav>
        <h2>  Online Exam System:   </h2>
        <h2>Welcome To,  (Staff)</h2>
         <div class="user-info">
            <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="../home.php" class="logout-btn">Logout</a>
        </div>
        <nav>
            <ul>
                <li><a href="question_paper.php">Create Paper</a></li>
                <li><a href="create_exam.php">Create Exam</a></li>
                <li><a href="add_questions.php">Add Questions</a></li>
                <li><a href="view_results.php">View Student Results</a></li>
            </ul>

 
        <h2>View Student Results</h2>

        <?php if ($exams->num_rows === 0): ?>
            <p>No exams found.</p>
        <?php else: ?>
            <?php while ($exam = $exams->fetch_assoc()): ?>
                <div class="exam-section">
                    <!-- ADDED NULL CHECKS WITH DEFAULT VALUES -->
                    <h3>
                        <?= htmlspecialchars($exam['title'] ?? 'Untitled Exam') ?> 
                        (<?= htmlspecialchars($exam['subject'] ?? 'No Subject') ?>)
                    </h3>
                    <p><strong>Date Created:</strong> <?= $exam['created_at'] ?></p>

                    <!-- Fetch student results for this exam -->
                    <?php
                    $exam_id = $exam['id'];
                    $resultQuery = "
                        SELECT u.username, sa.total_score, sa.completed_at
                        FROM student_attempts sa
                        JOIN users u ON sa.student_id = u.id
                        WHERE sa.exam_id = ?";
                    $stmt2 = $conn->prepare($resultQuery);
                    $stmt2->bind_param("i", $exam_id);
                    $stmt2->execute();
                    $results = $stmt2->get_result();
                    ?>

                    <?php if ($results->num_rows === 0): ?>
                        <p>No results found for this exam.</p>
                    <?php else: ?>
                        <table>
                            <tr>
                                <th>Student Name</th>
                                <th>Score</th>
                                <th>Completed At</th>
                            </tr>
                            <?php while ($row = $results->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= $row['total_score'] ?></td>
                                    <td><?= $row['completed_at'] ?: 'Not Completed' ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

</body>
</html>