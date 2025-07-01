<?php
session_start();
require_once __DIR__ . '/../includes/db.php'; // Use require_once to ensure db.php is loaded

// Verify admin role
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Make role check case-insensitive
if (strtolower($_SESSION['role']) !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Initialize variables
$exams = [];
$results = [];
$exam_details = null;
$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;

// Check if database connection exists
if (!isset($pdo) || !($pdo instanceof PDO)) {
    die("Database connection is not properly initialized");
}

try {
    // Fetch all exams for the dropdown
    $stmt = $pdo->query("SELECT e.id, qp.title, e.start_time, e.end_time 
                        FROM exams e
                        JOIN question_papers qp ON e.paper_id = qp.id
                        ORDER BY e.start_time DESC");
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If specific exam is selected, fetch results
    if ($exam_id > 0) {
        // Get exam details
        $stmt = $pdo->prepare("SELECT e.*, qp.title, u.name AS created_by_name 
                             FROM exams e
                             JOIN question_papers qp ON e.paper_id = qp.id
                             JOIN users u ON e.created_by = u.id
                             WHERE e.id = ?");
        $stmt->execute([$exam_id]);
        $exam_details = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($exam_details) {
            // Get all attempts for this exam
            $stmt = $pdo->prepare("SELECT sa.id, sa.student_id, u.name AS student_name, u.username,
                                 sa.total_score, sa.completed_at,
                                 (SELECT COUNT(*) FROM questions q WHERE q.paper_id = e.paper_id) AS total_questions,
                                 (sa.total_score / (SELECT COUNT(*) FROM questions q WHERE q.paper_id = e.paper_id)) * 100 AS percentage
                                 FROM student_attempts sa
                                 JOIN users u ON sa.student_id = u.id
                                 JOIN exams e ON sa.exam_id = e.id
                                 WHERE sa.exam_id = ?");
            $stmt->execute([$exam_id]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: exam_results.php");
    exit();
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">Exam Results</h2>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <!-- Exam Selection Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h4>Select Exam</h4>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-8">
                    <select name="exam_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Select Exam --</option>
                        <?php foreach ($exams as $exam): ?>
                            <option value="<?= $exam['id'] ?>" <?= $exam['id'] == $exam_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($exam['title']) ?> 
                                (<?= date('M d, Y', strtotime($exam['start_time'])) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">View Results</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($exam_id > 0 && $exam_details): ?>
        <!-- Exam Details -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h4>Exam Details</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Title:</strong> <?= htmlspecialchars($exam_details['title']) ?></p>
                        <p><strong>Created By:</strong> <?= htmlspecialchars($exam_details['created_by_name']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Start Time:</strong> <?= date('M d, Y H:i', strtotime($exam_details['start_time'])) ?></p>
                        <p><strong>End Time:</strong> <?= date('M d, Y H:i', strtotime($exam_details['end_time'])) ?></p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-<?= 
                                $exam_details['status'] == 'completed' ? 'success' : 
                                ($exam_details['status'] == 'ongoing' ? 'warning' : 'info') 
                            ?>">
                                <?= ucfirst($exam_details['status']) ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h4>Student Results</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Score</th>
                                <th>Total Questions</th>
                                <th>Percentage</th>
                                <th>Completed At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($results) > 0): ?>
                                <?php foreach ($results as $result): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($result['student_id']) ?></td>
                                        <td><?= htmlspecialchars($result['student_name']) ?></td>
                                        <td><?= htmlspecialchars($result['username']) ?></td>
                                        <td><?= htmlspecialchars($result['total_score']) ?></td>
                                        <td><?= htmlspecialchars($result['total_questions']) ?></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar 
                                                    <?= $result['percentage'] >= 70 ? 'bg-success' : 
                                                       ($result['percentage'] >= 50 ? 'bg-warning' : 'bg-danger') ?>" 
                                                    role="progressbar" 
                                                    style="width: <?= $result['percentage'] ?>%" 
                                                    aria-valuenow="<?= $result['percentage'] ?>" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100">
                                                    <?= round($result['percentage'], 2) ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= $result['completed_at'] ? date('M d, Y H:i', strtotime($result['completed_at'])) : 'Not completed' ?></td>
                                        <td>
                                            <a href="exam_result_details.php?attempt_id=<?= $result['id'] ?>" class="btn btn-sm btn-primary">Details</a>
                                            <button class="btn btn-sm btn-info" onclick="printResult(<?= $result['id'] ?>)">Print</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No results found for this exam</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Export Options -->
                <div class="mt-4">
                    <h5>Export Results:</h5>
                    <a href="export_results.php?exam_id=<?= $exam_id ?>&format=csv" class="btn btn-sm btn-success me-2">
                        <i class="fas fa-file-csv"></i> Export as CSV
                    </a>
                    <a href="export_results.php?exam_id=<?= $exam_id ?>&format=pdf" class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf"></i> Export as PDF
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function printResult(attemptId) {
        window.open('print_result.php?attempt_id=' + attemptId, '_blank');
    }
</script>

<?php
include __DIR__ . '/../includes/footer.php';
?>