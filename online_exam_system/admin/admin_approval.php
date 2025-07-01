<?php
session_start();
include('C:/xampp/htdocs/online_exam_system/includes/db.php');

// Restrict access to staff only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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

$error = '';
$success = '';

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['paper_id'])) {
    $paper_id = sanitizeInput($conn, $_POST['paper_id']);
    $action = sanitizeInput($conn, $_POST['action']);
    
    if (in_array($action, ['approved', 'rejected'])) {
        $sql = "UPDATE question_papers SET status = '$action' WHERE id = '$paper_id'";
        
        if ($conn->query($sql) === TRUE) {
            $success = "Paper has been " . $action . " successfully!";
        } else {
            $error = "Error updating paper: " . $conn->error;
        }
    }
}

// Fetch pending papers (simplified to use only question_papers table)
$papers = [];
$sql = "SELECT id, title, staff_id, created_at 
        FROM question_papers 
        WHERE status = 'pending' 
        ORDER BY created_at ASC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $papers[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paper Approval</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Pending Paper Approvals</h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (empty($papers)): ?>
            <div class="alert alert-info">No pending papers for approval.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Staff ID</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($papers as $paper): ?>
                            <tr>
                                <td><?php echo $paper['id']; ?></td>
                                <td><?php echo htmlspecialchars($paper['title']); ?></td>
                                <td><?php echo $paper['staff_id']; ?></td>
                                <td><?php echo date('d M Y H:i', strtotime($paper['created_at'])); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="paper_id" value="<?php echo $paper['id']; ?>">
                                        <input type="hidden" name="action" value="approved">
                                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="paper_id" value="<?php echo $paper['id']; ?>">
                                        <input type="hidden" name="action" value="rejected">
                                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>