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

// Fetch all approved question papers created by this staff
$query = "SELECT * FROM question_papers WHERE staff_id = ? AND status = 'approved'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$papers = $result->fetch_all(MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $paper_id = $_POST['paper_id'];
    $question_texts = $_POST['question_text'];
    $options_a = $_POST['option_a'];
    $options_b = $_POST['option_b'];
    $options_c = $_POST['option_c'];
    $options_d = $_POST['option_d'];
    $correct_options = $_POST['correct_option'];

    if (empty($paper_id)) {
        $message = "Please select a question paper.";
    } else {
        // Check paper ownership
        $check_stmt = $conn->prepare("SELECT id FROM question_papers WHERE id = ? AND staff_id = ?");
        $check_stmt->bind_param("ii", $paper_id, $staff_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
            $message = "Invalid question paper selected.";
        } else {
            $query = "INSERT INTO questions (paper_id, question_text, option_a, option_b, option_c, option_d, correct_option) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);

            for ($i = 0; $i < count($question_texts); $i++) {
                if (
                    !empty($question_texts[$i]) && !empty($options_a[$i]) &&
                    !empty($options_b[$i]) && !empty($options_c[$i]) &&
                    !empty($options_d[$i]) && !empty($correct_options[$i])
                ) {
                    $stmt->bind_param("issssss", $paper_id, $question_texts[$i], $options_a[$i], $options_b[$i], $options_c[$i], $options_d[$i], $correct_options[$i]);
                    $stmt->execute();
                }
            }

            $message = "All questions added successfully!";
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="css/add_question.css">
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



<div class="container">
    <h2><i class="fas fa-question-circle"></i> Add Multiple Choise Questions</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= strpos($message, 'successfully') !== false ? 'success' : 'danger' ?>">
            <i class="<?= strpos($message, 'successfully') !== false ? 'fas fa-check-circle' : 'fas fa-exclamation-circle' ?>"></i>
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="question-form">
        <div class="form-group">
            <label for="paper_id"><i class="fas fa-file-alt"></i> Select Question Paper:</label>
            <select name="paper_id" class="form-control" required>
                <option value="">Select Paper</option>
                <?php foreach ($papers as $paper): ?>
                    <option value="<?= $paper['id'] ?>"><?= htmlspecialchars($paper['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div id="questions-wrapper">
            <div class="question-block">
                <div class="question-header">
                    <span>Question 1</span>
                    <button type="button" class="btn btn-remove" onclick="removeQuestion(this)"><i class="fas fa-times"></i></button>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-question"></i> Question Text:</label>
                    <textarea name="question_text[]" class="form-control" required></textarea>
                </div>

                <div class="options-grid">
                    <div class="form-group">
                        <label><span class="option-letter">A</span> Option A:</label>
                        <input type="text" name="option_a[]" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label><span class="option-letter">B</span> Option B:</label>
                        <input type="text" name="option_b[]" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label><span class="option-letter">C</span> Option C:</label>
                        <input type="text" name="option_c[]" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label><span class="option-letter">D</span> Option D:</label>
                        <input type="text" name="option_d[]" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-check-circle"></i> Correct Option:</label>
                    <select name="correct_option[]" class="form-control" required>
                        <option value="">Select Answer</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="btn btn-add" onclick="addQuestion()">
                <i class="fas fa-plus"></i> Add Another Question
            </button>
            <div>
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-paper-plane"></i> Submit All Questions
                </button>
                <a href="dashboard.php" class="btn btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </form>
</div>

<script>
function addQuestion() {
    const wrapper = document.getElementById('questions-wrapper');
    const questionCount = wrapper.children.length + 1;
    const newBlock = wrapper.firstElementChild.cloneNode(true);

    // Update question number
    newBlock.querySelector('.question-header span').textContent = `Question #${questionCount}`;
    
    // Clear all inputs
    newBlock.querySelectorAll('input, textarea, select').forEach(el => {
        if (el.tagName === 'SELECT') {
            el.selectedIndex = 0;
        } else {
            el.value = '';
        }
    });

    wrapper.appendChild(newBlock);
    
    // Scroll to the new question
    newBlock.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function removeQuestion(button) {
    const questionBlock = button.closest('.question-block');
    if (document.querySelectorAll('.question-block').length > 1) {
        questionBlock.remove();
        // Renumber remaining questions
        document.querySelectorAll('.question-block .question-header span').forEach((el, index) => {
            el.textContent = `Question #${index + 1}`;
        });
    } else {
        alert("You need to have at least one question!");
    }
}
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