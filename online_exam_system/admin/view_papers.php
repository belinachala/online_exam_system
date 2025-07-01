<?php
require_once 'db.php';
session_start();

// Check if admin or staff is logged in
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: " . (isset($_SESSION['admin_id']) ? 'admin_dashboard.php' : 'staff_dashboard.php'));
    exit();
}

$paper_id = sanitizeInput($conn, $_GET['id']);
$paper = [];
$questions = [];

// Fetch paper details
$sql = "SELECT qp.*, s.name as subject_name, st.name as staff_name
        FROM question_papers qp
        JOIN subjects s ON qp.subject_id = s.id
        JOIN staff st ON qp.staff_id = st.id
        WHERE qp.id = '$paper_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $paper = $result->fetch_assoc();
} else {
    header("Location: " . (isset($_SESSION['admin_id']) ? 'admin_dashboard.php' : 'staff_dashboard.php'));
    exit();
}

// Fetch questions for this paper
$sql = "SELECT * FROM questions WHERE paper_id = '$paper_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Question Paper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Question Paper: <?php echo htmlspecialchars($paper['title']); ?></h2>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Paper Details</h5>
                <p class="card-text">
                    <strong>Subject:</strong> <?php echo htmlspecialchars($paper['subject_name']); ?><br>
                    <strong>Created By:</strong> <?php echo htmlspecialchars($paper['staff_name']); ?><br>
                    <strong>Status:</strong> 
                    <span class="badge bg-<?php 
                        echo $paper['status'] == 'approved' ? 'success' : 
                             ($paper['status'] == 'rejected' ? 'danger' : 'warning'); 
                    ?>">
                        <?php echo ucfirst($paper['status']); ?>
                    </span><br>
                    <strong>Created At:</strong> <?php echo date('d M Y H:i', strtotime($paper['created_at'])); ?>
                </p>
            </div>
        </div>
        
        <h4>Questions</h4>
        <?php if (empty($questions)): ?>
            <div class="alert alert-info">No questions added to this paper yet.</div>
        <?php else: ?>
            <div class="list-group">
                <?php foreach ($questions as $index => $question): ?>
                    <div class="list-group-item">
                        <h5>Question <?php echo $index + 1; ?></h5>
                        <p><?php echo htmlspecialchars($question['question_text']); ?></p>
                        <!-- Add more question details as needed -->
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="<?php echo isset($_SESSION['admin_id']) ? 'admin_approval.php' : 'staff_dashboard.php'; ?>" 
               class="btn btn-secondary">
                Back
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>