<?php
session_start();
include '../includes/db.php';

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

// Get student ID
$student_id = $_SESSION['user_id'];

// Fetch available exams with their titles from question_papers
$exam_query = "SELECT e.id, e.paper_id, e.start_time, e.end_time, qp.title 
               FROM exams e
               JOIN question_papers qp ON e.paper_id = qp.id
               WHERE e.status IN ('scheduled', 'ongoing') 
               AND e.start_time <= NOW() 
               AND e.end_time > NOW()";

$exam_result = mysqli_query($conn, $exam_query);
if (!$exam_result) {
    die("Error fetching exams: " . mysqli_error($conn));
}

// Fetch student results with exam titles
$result_query = "SELECT e.paper_id, qp.title, sa.total_score 
                 FROM student_attempts sa
                 JOIN exams e ON sa.exam_id = e.id
                 JOIN question_papers qp ON e.paper_id = qp.id
                 WHERE sa.student_id = ?";
$stmt = $conn->prepare($result_query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $student_id);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$result_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<main class="dashboard-container">
    <div class="dashboard-header">
        <h2>Welcome To Your Dashboard To Take Exam:</h2>
        <div class="user-info">
            <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="../home.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <section class="dashboard-section">
        <h3><i class="fas fa-calendar-alt"></i> Available Exams</h3>
        <?php if (mysqli_num_rows($exam_result) > 0): ?>
            <div class="table-responsive">
                <table class="exam-table">
                    <thead>
                        <tr>
                            <th>Exam Title</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($exam = mysqli_fetch_assoc($exam_result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($exam['title']); ?></td>
                            <td>
                                <?php echo date('M j, Y g:i a', strtotime($exam['start_time'])) . ' - ' . date('g:i a', strtotime($exam['end_time'])); ?>
                            </td>
                            <td>
                                <?php
                                $duration = strtotime($exam['end_time']) - strtotime($exam['start_time']);
                                echo gmdate("H:i", $duration) . " hours";
                                ?>
                            </td>
                            <td>
                                <a href="solve_exam.php?exam_id=<?php echo $exam['id']; ?>" class="btn start-exam-btn">
                                    <i class="fas fa-play"></i> Start Exam
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No active exams available at this time.
            </div>
        <?php endif; ?>
    </section>

    <section class="dashboard-section">
        <h3><i class="fas fa-chart-line"></i> Your Exam Results</h3>
        <?php if ($result_result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>Exam Title</th>
                            <th>Score</th>
                            <th>Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($result = $result_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result['title']); ?></td>
                            <td><?php echo htmlspecialchars($result['total_score']); ?></td>
                            <td>
                                <?php
                                $score = $result['total_score'];
                                if ($score >= 40) {
                                    echo '<span class="performance excellent"><i class="fas fa-star"></i> Excellent</span>';
                                } elseif ($score >= 35) {
                                    echo '<span class="performance good"><i class="fas fa-thumbs-up"></i> Good</span>';
                                } elseif ($score >= 30) {
                                    echo '<span class="performance average"><i class="fas fa-check"></i> Average</span>';
                                } else {
                                    echo '<span class="performance poor"><i class="fas fa-exclamation-triangle"></i> Needs Improvement</span>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> You haven't completed any exams yet.
            </div>
        <?php endif; ?>
    </section>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>