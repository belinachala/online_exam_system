<?php
session_start();
include '../includes/db.php';

// Check if staff is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
    header("Location: ../auth/login.php");
    exit();
}

// Get staff information
$staff_id = $_SESSION['user_id'];
$query = "SELECT username FROM users WHERE id = '$staff_id'";
$result = mysqli_query($conn, $query);
$staff = mysqli_fetch_assoc($result);

// Fetch exams created by the staff
$exam_query = "SELECT * FROM exams WHERE created_by = '$staff_id' ORDER BY created_at DESC";
$exam_result = mysqli_query($conn, $exam_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <div class="container">
        
        </nav>
        <h2>  Online Exam System:   </h2>
        <h2>Welcome To, <?php echo htmlspecialchars($staff['username']); ?> (Staff)</h2>
         <div class="user-info">
            <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="../auth/login.php" class="logout-btn">Logout</a>
        </div>
        <nav>
            <ul>
                <li><a href="question_paper.php">Create Paper</a></li>
                <li><a href="create_exam.php">Create Exam</a></li>
                <li><a href="add_questions.php">Add Questions</a></li>
                <li><a href="view_results.php">View Student Results</a></li>
            </ul>

        <h3>Your Created Exams</h3>
        <table>
            <tr>
                <th>Exam Title</th>
                <th>Subject</th>
                <th>Date Created</th>
                <th>Actions</th>
            </tr>
            <?php while ($exam = mysqli_fetch_assoc($exam_result)) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($exam['start_time']); ?></td>
                    <td><?php echo htmlspecialchars($exam['end_time']); ?></td>
                    <td><?php echo $exam['created_at']; ?></td>
                    <td>
                        <a href="add_questions.php?exam_id=<?php echo $exam['id']; ?>">Add Questions</a> |
                        <a href="view_results.php?exam_id=<?php echo $exam['id']; ?>">View Results</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

     <!-- Footer -->
    <footer>
        <div class="containers">
            <div class="footer-content">
                <div class="footer-logo">
                    <i class="fas fa-laptop-code"></i>
                    <span>CodeExam</span>
                </div>
                <div class="footer-socials">
                    <h4>Follow Us</h4>
                    <div class="social-icons">
                         <a href="https://web.facebook.com/photo/?fbid=644120595127708&set=a.244680168405088"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a> 
                        <a href="https://www.youtube.com/channel/UC2y1IGj-6NP-uqYZbR34biw"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
            <p>&copy; CodeExam. All rights reserved. Designed for Ethiopia Software Engineering Students.</p>
            </div>
        </div>
    </footer>


</body>
</html>
