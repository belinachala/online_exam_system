<?php
session_start();
include '../includes/db.php';

// Check if student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

// Check for exam_id in URL
if (!isset($_GET['exam_id'])) {
    die("No exam ID provided.");
}

$exam_id = intval($_GET['exam_id']);

// Step 1: Get paper_id and title using exam_id
$stmt = $conn->prepare("
    SELECT exams.paper_id, papers.title 
    FROM exams 
    JOIN papers ON exams.paper_id = papers.id 
    WHERE exams.id = ?
");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid exam ID.");
}

$data = $result->fetch_assoc();
$paper_id = $data['paper_id'];
$paper_title = $data['title'];

// Step 2: Fetch questions for the paper
$questions = [];
$q_stmt = $conn->prepare("SELECT * FROM questions WHERE paper_id = ?");
$q_stmt->bind_param("i", $paper_id);
$q_stmt->execute();
$q_result = $q_stmt->get_result();

while ($row = $q_result->fetch_assoc()) {
    $questions[] = $row;
}

include '../includes/header.php';
?>

<main>
    <h2>Exam Paper: <?php echo htmlspecialchars($paper_title); ?></h2>

    <?php if (count($questions) > 0): ?>
        <ol>
            <?php foreach ($questions as $q): ?>
                <li style="margin-bottom: 20px;">
                    <strong><?php echo htmlspecialchars($q['question_text']); ?></strong><br>
                    <ul style="list-style-type: none; padding-left: 0;">
                        <li style="color: <?php echo ($q['correct_option'] === 'A') ? 'green' : 'black'; ?>">
                            A. <?php echo htmlspecialchars($q['option_a']); ?>
                        </li>
                        <li style="color: <?php echo ($q['correct_option'] === 'B') ? 'green' : 'black'; ?>">
                            B. <?php echo htmlspecialchars($q['option_b']); ?>
                        </li>
                        <li style="color: <?php echo ($q['correct_option'] === 'C') ? 'green' : 'black'; ?>">
                            C. <?php echo htmlspecialchars($q['option_c']); ?>
                        </li>
                        <li style="color: <?php echo ($q['correct_option'] === 'D') ? 'green' : 'black'; ?>">
                            D. <?php echo htmlspecialchars($q['option_d']); ?>
                        </li>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php else: ?>
        <p>No questions found for this paper.</p>
    <?php endif; ?>

    <br>
    <a href="view_previous_papers.php" style="text-decoration: none; color: #007bff;">Back to Previous Papers</a>
</main>

<?php include '../includes/footer.php'; ?>
