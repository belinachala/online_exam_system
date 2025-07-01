<?php
session_start();
// Include database connection
include('../includes/db.php');
// Restrict access to staff only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../auth/login.php");
    exit();
}

// Function to sanitize input
function sanitizeInput($connection, $data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $connection->real_escape_string($data);
}

$staff_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch existing subjects from database
$subjects = [];
$sql = "SELECT id, name FROM subjects";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $existing_subject = isset($_POST['existing_subject']) ? $_POST['existing_subject'] : '';
    $subject_name = isset($_POST['subject_name']) ? sanitizeInput($conn, $_POST['subject_name']) : '';
    $title = sanitizeInput($conn, $_POST['title']);

    // Determine subject ID
    $subject_id = null;
    
    if ($existing_subject === 'new') {
        // Insert new subject if it doesn't exist
        if (!empty($subject_name)) {
            $check_sql = "SELECT id FROM subjects WHERE name = '$subject_name'";
            $check_result = $conn->query($check_sql);
            
            if ($check_result->num_rows > 0) {
                $row = $check_result->fetch_assoc();
                $subject_id = $row['id'];
            } else {
                $insert_sql = "INSERT INTO subjects (name) VALUES ('$subject_name')";
                if ($conn->query($insert_sql) === TRUE) {
                    $subject_id = $conn->insert_id;
                } else {
                    $error = "Error creating subject: " . $conn->error;
                }
            }
        } else {
            $error = "Please enter a subject name";
        }
    } elseif (!empty($existing_subject) && $existing_subject !== 'new') {
        $subject_id = $existing_subject;
    } else {
        $error = "Please select or enter a subject";
    }

    // Create question paper if no errors
    if ($subject_id && empty($error)) {
        $sql = "INSERT INTO question_papers (staff_id, subject_id, title, status) 
                VALUES ('$staff_id', '$subject_id', '$title', 'pending')";

        if ($conn->query($sql) === TRUE) {
            $success = "Question paper created successfully! Waiting for admin approval.";
            // Clear form values
            $title = '';
        } else {
            $error = "Error creating question paper: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Question Paper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/question_paper.css">
</head>
<body>
        <div class="container">
        <h2> Welcome To Praper Online Exam  Questions For Ethiopia Software Engineering Students:      </h2>
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
        </nav>

    <div class="container mt-5">
        <h2>Create New Question Paper</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Subject</label>
                <div class="subject-container">
                    <select class="form-control" id="existing_subject" name="existing_subject" required>
                        <option value="">Select Existing Subject</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo $subject['id']; ?>"
                                <?php echo (isset($_POST['existing_subject']) && $_POST['existing_subject'] == $subject['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($subject['name']); ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="new"
                            <?php echo (isset($_POST['existing_subject']) && $_POST['existing_subject'] == 'new') ? 'selected' : ''; ?>>
                            + Add New Subject
                        </option>
                    </select>
                    <input type="text" class="form-control" id="subject_name" name="subject_name" 
                           placeholder="Enter New Subject Name" 
                           value="<?php echo isset($_POST['subject_name']) ? htmlspecialchars($_POST['subject_name']) : ''; ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label">Paper Title</label>
                <input type="text" class="form-control" id="title" name="title" required
                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">Submit for Approval</button>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subjectSelect = document.getElementById('existing_subject');
            const subjectNameField = document.getElementById('subject_name');
            
            // Initialize display based on current selection
            if (subjectSelect.value === 'new') {
                subjectNameField.style.display = 'block';
                subjectNameField.required = true;
            }
            
            // Handle change event
            subjectSelect.addEventListener('change', function() {
                if (this.value === 'new') {
                    subjectNameField.style.display = 'block';
                    subjectNameField.required = true;
                    subjectNameField.value = '';
                } else {
                    subjectNameField.style.display = 'none';
                    subjectNameField.required = false;
                }
            });
        });
    </script>
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