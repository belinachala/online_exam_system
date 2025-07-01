<?php
session_start();
include '../includes/db.php';

// Ensure student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

$student_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle exam submission
    $exam_id = intval($_POST['exam_id']);
    $question_ids = $_POST['question_ids'];
    $answers = $_POST['answers'];

    $check_stmt = $conn->prepare("SELECT id FROM student_attempts WHERE student_id = ? AND exam_id = ?");
    $check_stmt->bind_param("ii", $student_id, $exam_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        die("You have already submitted this exam.");
    }

    $insert_attempt = $conn->prepare("INSERT INTO student_attempts (student_id, exam_id, completed_at) VALUES (?, ?, NOW())");
    $insert_attempt->bind_param("ii", $student_id, $exam_id);
    $insert_attempt->execute();
    $attempt_id = $insert_attempt->insert_id;

    $score = 0;
    foreach ($question_ids as $qid) {
        $chosen = $answers[$qid];
        $correct_stmt = $conn->prepare("SELECT correct_option FROM questions WHERE id = ?");
        $correct_stmt->bind_param("i", $qid);
        $correct_stmt->execute();
        $correct_result = $correct_stmt->get_result();
        $correct_row = $correct_result->fetch_assoc();
        $is_correct = ($correct_row['correct_option'] === $chosen) ? 1 : 0;
        $score += $is_correct;

        $insert_ans = $conn->prepare("INSERT INTO student_answers (attempt_id, question_id, chosen_option, is_correct) VALUES (?, ?, ?, ?)");
        $insert_ans->bind_param("iisi", $attempt_id, $qid, $chosen, $is_correct);
        $insert_ans->execute();
    }

    $update_score = $conn->prepare("UPDATE student_attempts SET total_score = ? WHERE id = ?");
    $update_score->bind_param("ii", $score, $attempt_id);
    $update_score->execute();

    $submitted = true;
} else {
    // Handle exam display
    if (!isset($_GET['exam_id'])) {
        die("No exam ID provided.");
    }

    $exam_id = intval($_GET['exam_id']);

    // Check for duplicate attempt
    $check_stmt = $conn->prepare("SELECT id FROM student_attempts WHERE student_id = ? AND exam_id = ?");
    $check_stmt->bind_param("ii", $student_id, $exam_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        die("You have already submitted this exam.");
    }

    // Fetch exam info
    $paper_stmt = $conn->prepare("SELECT paper_id FROM exams WHERE id = ?");
    $paper_stmt->bind_param("i", $exam_id);
    $paper_stmt->execute();
    $exam_result = $paper_stmt->get_result();

    if ($exam_result->num_rows === 0) {
        die("Invalid exam ID.");
    }

    $exam_data = $exam_result->fetch_assoc();
    $paper_id = $exam_data['paper_id'];

    // Fetch questions
    $questions = [];
    $question_stmt = $conn->prepare("SELECT * FROM questions WHERE paper_id = ?");
    $question_stmt->bind_param("i", $paper_id);
    $question_stmt->execute();
    $question_result = $question_stmt->get_result();

    while ($row = $question_result->fetch_assoc()) {
        $questions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Solve Exam</title>
    <link rel="stylesheet" href="css/view_exam.css">
    <style>
        .question-container { display: none; }
        .question-container.active { display: block; }
        .nav-buttons { margin-top: 20px; }
        button { padding: 8px 16px; cursor: pointer; }
        
        /* Question navigation styles */
        #question-nav-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            font-size: 24px;
            cursor: pointer;
            z-index: 1000;
            background: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px 10px;
        }
        
        #question-nav-table {
            display: none;
            position: fixed;
            top: 60px;
            right: 20px;
            background: white;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 999;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        #question-nav-table table {
            border-collapse: collapse;
        }
        
        #question-nav-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            cursor: pointer;
            min-width: 30px;
        }
        
        #question-nav-table td:hover {
            background-color: #f0f0f0;
        }
        
        #question-nav-table td.answered {
            background-color: #000;
            color: #fff;
        }
        
        #question-nav-table td.current {
            background-color: #4CAF50;
            color: #fff;
        }
        
        /* Warning message for screenshot attempts */
        #screenshot-warning {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #ff0000;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            display: none;
            z-index: 9999;
        }
    </style>
</head>
<body>

<!-- Screenshot attempt warning -->
<div id="screenshot-warning">
    WARNING: Screenshots are not allowed during exams! Repeated attempts may invalidate your test.
</div>

<!-- Question navigation toggle button -->
<div id="question-nav-toggle">☰</div>
<div id="question-nav-table">
    <table id="question-numbers"></table>
</div>

<?php if (isset($submitted) && $submitted): ?>
    <h2>Exam Submitted Successfully!</h2>
    <h3>Correct Answers:</h3>
    <ul>
        <?php
        foreach ($question_ids as $qid) {
            $q_stmt = $conn->prepare("SELECT question_text, correct_option FROM questions WHERE id = ?");
            $q_stmt->bind_param("i", $qid);
            $q_stmt->execute();
            $q_res = $q_stmt->get_result();
            $q = $q_res->fetch_assoc();
            $correct = $q['correct_option'];
            echo "<li><strong>" . htmlspecialchars($q['question_text']) . "</strong><br> Correct Option: <span class='correct'>$correct</span></li>";
        }
        ?>
    </ul>
<?php else: ?>
    <h1>The Exam is Started Now:</h1>
    <div id="countdown">Time Remaining: 60:00</div>
    <form id="examForm" action="solve_exam.php" method="POST">
        <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">

        <?php foreach ($questions as $index => $q): ?>
            <div class="question-container" id="question-<?php echo $index; ?>" data-question-id="<?php echo $q['id']; ?>">
                <h3><?php echo $index + 1; ?>. <?php echo htmlspecialchars($q['question_text']); ?></h3>
                <input type="hidden" name="question_ids[]" value="<?php echo $q['id']; ?>">
                <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="A" required> A. <?php echo htmlspecialchars($q['option_a']); ?></label><br>
                <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="B"> B. <?php echo htmlspecialchars($q['option_b']); ?></label><br>
                <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="C"> C. <?php echo htmlspecialchars($q['option_c']); ?></label><br>
                <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="D"> D. <?php echo htmlspecialchars($q['option_d']); ?></label><br>
            </div>
        <?php endforeach; ?>

        <div class="nav-buttons">
            <button type="button" id="prevBtn">Previous</button>
            <button type="button" id="nextBtn">Next</button>
            <button type="submit" id="submitBtn" style="display: none;">Submit Exam</button>
        </div>
    </form>
<?php endif; ?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Enhanced anti-cheating measures
        const warningEl = document.getElementById('screenshot-warning');
        let screenshotAttempts = 0;
        const maxAttempts = 3;
        
        // 1. Prevent context menu (right-click)
        document.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            showWarning("Right-click is disabled during exams");
        });
        
        // 2. Prevent text selection
        document.addEventListener('selectstart', (e) => {
            e.preventDefault();
            showWarning("Text selection is disabled during exams");
        });
        
        // 3. Detect PrintScreen key and other screenshot methods
        document.addEventListener('keydown', (e) => {
            // Detect PrintScreen, Alt+PrintScreen, Ctrl+PrintScreen, etc.
            if (e.key === 'PrintScreen' || 
                (e.ctrlKey && e.key === 'PrintScreen') || 
                (e.altKey && e.key === 'PrintScreen')) {
                e.preventDefault();
                handleScreenshotAttempt();
            }
            
            // Detect Windows+Shift+S (Windows snipping tool)
            if (e.key === 's' && e.shiftKey && (e.metaKey || e.ctrlKey)) {
                e.preventDefault();
                handleScreenshotAttempt();
            }
            
            // Detect Alt+Tab (switching windows)
            if (e.key === 'Tab' && e.altKey) {
                e.preventDefault();
                showWarning("Window switching is not allowed during exams");
            }
            
            // Detect F12 (developer tools)
            if (e.key === 'F12') {
                e.preventDefault();
                showWarning("Developer tools are disabled during exams");
            }
        });
        
        // 4. Detect browser dev tools opening
        let devToolsOpen = false;
        const devToolsCheck = setInterval(() => {
            const widthThreshold = window.outerWidth - window.innerWidth > 160;
            const heightThreshold = window.outerHeight - window.innerHeight > 160;
            
            if ((widthThreshold || heightThreshold) && !devToolsOpen) {
                devToolsOpen = true;
                showWarning("Developer tools detected. Please close them immediately or your exam will be submitted.");
                setTimeout(() => {
                    if (devToolsOpen) {
                        alert("Developer tools remained open. Submitting exam...");
                        document.getElementById('examForm').submit();
                    }
                }, 5000);
            }
        }, 1000);
        
        // 5. Fullscreen enforcement
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen().catch(err => {
                showWarning("Fullscreen is required for this exam");
            });
        }
        
        document.addEventListener('fullscreenchange', () => {
            if (!document.fullscreenElement) {
                showWarning("Please return to full screen or your exam may be invalidated");
                document.documentElement.requestFullscreen();
            }
        });
        
        // 6. Visibility API - detect tab switching
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                showWarning("You switched to another tab/window. This is not allowed during exams.");
            }
        });
        
        // 7. Mouse leave detection
        document.addEventListener('mouseleave', (e) => {
            if (e.clientY < 0) {
                showWarning("Please keep your mouse within the exam window");
            }
        });
        
        // Screenshot attempt handler
        function handleScreenshotAttempt() {
            screenshotAttempts++;
            showWarning(`Screenshot attempt detected (${screenshotAttempts}/${maxAttempts})`);
            
            if (screenshotAttempts >= maxAttempts) {
                alert("Maximum screenshot attempts reached. Submitting your exam...");
                document.getElementById('examForm').submit();
            }
        }
        
        // Show warning message
        function showWarning(message) {
            warningEl.textContent = "WARNING: " + message;
            warningEl.style.display = 'block';
            setTimeout(() => {
                warningEl.style.display = 'none';
            }, 5000);
        }
        
        // Timer logic (1 hour countdown)
        const examDuration = 60 * 60 * 1000; // 1 hour in milliseconds
        const startTime = new Date().getTime();
        const endTime = startTime + examDuration;
        
        const timerEl = document.getElementById("countdown");
        const interval = setInterval(() => {
            const now = new Date().getTime();
            const remaining = endTime - now;
            
            if (remaining <= 0) {
                clearInterval(interval);
                alert("Time's up! Submitting exam...");
                document.getElementById("examForm").submit();
            } else {
                const hours = Math.floor((remaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((remaining % (1000 * 60)) / 1000);
                
                // Display as MM:SS
                timerEl.innerText = `Time Remaining: ${minutes}m ${seconds}s`;
            }
        }, 1000);

        // Question navigation
        const questions = document.querySelectorAll('.question-container');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const navToggle = document.getElementById('question-nav-toggle');
        const navTable = document.getElementById('question-nav-table');
        const questionNumbers = document.getElementById('question-numbers');
        let currentQuestion = 0;

        // Initialize question navigation table
        function initQuestionNav() {
            questionNumbers.innerHTML = '';
            const rows = Math.ceil(questions.length / 5);
            let questionNum = 0;
            
            for (let i = 0; i < rows; i++) {
                const row = document.createElement('tr');
                for (let j = 0; j < 5; j++) {
                    if (questionNum >= questions.length) break;
                    
                    const cell = document.createElement('td');
                    cell.textContent = questionNum + 1;
                    cell.dataset.index = questionNum;
                    
                    // Check if question is answered
                    const questionId = questions[questionNum].dataset.questionId;
                    const isAnswered = document.querySelector(`input[name="answers[${questionId}]"]:checked`);
                    if (isAnswered) cell.classList.add('answered');
                    
                    // Highlight current question
                    if (questionNum === currentQuestion) {
                        cell.classList.add('current');
                    }
                    
                    cell.addEventListener('click', () => {
                        currentQuestion = parseInt(cell.dataset.index);
                        showQuestion(currentQuestion);
                        updateQuestionNav();
                    });
                    
                    row.appendChild(cell);
                    questionNum++;
                }
                questionNumbers.appendChild(row);
            }
        }

        // Update question navigation table
        function updateQuestionNav() {
            const cells = questionNumbers.querySelectorAll('td');
            cells.forEach((cell, index) => {
                cell.classList.remove('current');
                if (index === currentQuestion) {
                    cell.classList.add('current');
                }
                
                // Update answered status
                const questionId = questions[index].dataset.questionId;
                const isAnswered = document.querySelector(`input[name="answers[${questionId}]"]:checked`);
                if (isAnswered) {
                    cell.classList.add('answered');
                } else {
                    cell.classList.remove('answered');
                }
            });
        }

        // Toggle question navigation table
        navToggle.addEventListener('click', () => {
            if (navTable.style.display === 'block') {
                navTable.style.display = 'none';
            } else {
                initQuestionNav();
                navTable.style.display = 'block';
            }
        });

        // Show current question
        function showQuestion(index) {
            questions.forEach((q, i) => {
                q.classList.toggle('active', i === index);
            });
            prevBtn.disabled = index === 0;
            nextBtn.disabled = index === questions.length - 1;
            submitBtn.style.display = (index === questions.length - 1) ? 'block' : 'none';
            
            // Update navigation table if visible
            if (navTable.style.display === 'block') {
                updateQuestionNav();
            }
        }

        // Previous/Next buttons
        prevBtn.addEventListener('click', () => {
            if (currentQuestion > 0) {
                currentQuestion--;
                showQuestion(currentQuestion);
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentQuestion < questions.length - 1) {
                currentQuestion++;
                showQuestion(currentQuestion);
            }
        });

        // Update navigation when answers change
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', () => {
                if (navTable.style.display === 'block') {
                    updateQuestionNav();
                }
            });
        });

        // Initialize
        showQuestion(0);
    });
</script>
</body>
</html>