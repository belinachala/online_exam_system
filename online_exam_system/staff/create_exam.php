<?php
// Start session at the very beginning
session_start();

// Include database connection
include('../includes/db.php');

// Check if user is logged in and has staff role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize variables
$success_msg = '';
$error_msg = '';
$start_time = $end_time = $paper_id = '';
$username = $_SESSION['username'] ?? 'Staff User'; // Default if username not set

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and get form data
    $start_time = isset($_POST['start_time']) ? trim($_POST['start_time']) : '';
    $end_time = isset($_POST['end_time']) ? trim($_POST['end_time']) : '';
    $paper_id = isset($_POST['paper_id']) ? (int)$_POST['paper_id'] : 0;
    
    // Basic validation
    if (empty($start_time)) {
        $error_msg = "Start time is required.";
    } elseif (empty($end_time)) {
        $error_msg = "End time is required.";
    } elseif (strtotime($end_time) <= strtotime($start_time)) {
        $error_msg = "End time must be after start time.";
    } elseif ($paper_id <= 0) {
        $error_msg = "Please select a valid question paper.";
    } else {
        // Get user ID from session
        $created_by = $_SESSION['user_id'];

        // Validate paper_id exists in question_papers table and has status 'approved'
        $check_paper = $conn->prepare("SELECT id FROM question_papers WHERE id = ? AND status = 'approved'");
        $check_paper->bind_param("i", $paper_id);
        $check_paper->execute();
        $result = $check_paper->get_result();

        if ($result->num_rows == 0) {
            $error_msg = "Error: The selected question paper is not approved or doesn't exist.";
        } else {
            // Format the datetime strings for MySQL
            $start_time_db = date('Y-m-d H:i:s', strtotime($start_time));
            $end_time_db = date('Y-m-d H:i:s', strtotime($end_time));

            // Prepare and execute the INSERT query
            $stmt = $conn->prepare("INSERT INTO exams (paper_id, start_time, end_time, created_by) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $paper_id, $start_time_db, $end_time_db, $created_by);

            if ($stmt->execute()) {
                $success_msg = "Exam created successfully!";
                // Clear form values on success
                $start_time = $end_time = $paper_id = '';
            } else {
                $error_msg = "Error creating exam: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Fetch only approved question papers
$query = "SELECT id, title FROM question_papers WHERE status = 'approved' ORDER BY title";
$papers_result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Exam</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/create_exam.css">
</head>
<body>
    <div class="container">
        <h2>Welcome To Praper Online Exam Questions For Ethiopia Software Engineering Students</h2>
        <div class="user-info">
            <span class="username"><?php echo htmlspecialchars($username); ?></span>
            <a href="../home.php" class="logout-btn">Logout</a>
        </div>
        <nav>
            <ul>
                <li><a href="question_paper.php"><i class="fas fa-file-alt"></i> Create Paper</a></li>
                <li><a href="create_exam.php"><i class="fas fa-calendar-plus"></i> Create Exam</a></li>
                <li><a href="add_questions.php"><i class="fas fa-question-circle"></i> Add Questions</a></li>
                <li><a href="view_results.php"><i class="fas fa-chart-bar"></i> View Results</a></li>
            </ul>
        </nav>

        <div class="content-container">
            <h2><i class="fas fa-calendar-plus"></i> Create Exam</h2>
            
            <?php if ($success_msg): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_msg); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_msg): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_msg); ?>
                </div>
            <?php endif; ?>

            <form action="create_exam.php" method="POST" class="exam-form">
                <div class="form-group">
                    <label for="start_time"><i class="fas fa-clock"></i> Start Time</label>
                    <input type="datetime-local" name="start_time" id="start_time" 
                           value="<?php echo htmlspecialchars($start_time); ?>" 
                           required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="end_time"><i class="fas fa-clock"></i> End Time</label>
                    <input type="datetime-local" name="end_time" id="end_time" 
                           value="<?php echo htmlspecialchars($end_time); ?>" 
                           required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="paper_id"><i class="fas fa-file-alt"></i> Select Question Paper</label>
                    <select name="paper_id" id="paper_id" required class="form-control">
                        <option value="">Select a Paper</option>
                        <?php
                        if ($papers_result) {
                            while ($row = $papers_result->fetch_assoc()) {
                                $selected = ($paper_id == $row['id']) ? 'selected' : '';
                                echo "<option value='".htmlspecialchars($row['id'])."' $selected>".htmlspecialchars($row['title'])."</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Exam
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <footer>
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-logo">
                    <i class="fas fa-laptop-code"></i>
                    <span>Praper Exam System</span>
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

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>
</html>