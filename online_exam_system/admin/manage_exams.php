<?php
session_start();
include '../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle exam activation/deactivation
if (isset($_GET['action']) && isset($_GET['exam_id'])) {
    $exam_id = intval($_GET['exam_id']);
    
    if ($_GET['action'] == 'start') {
        $query = "UPDATE exams SET status = 'active' WHERE id = ?";
    } elseif ($_GET['action'] == 'stop') {
        $query = "UPDATE exams SET status = 'inactive' WHERE id = ?";
    }

    if (isset($query)) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $exam_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: manage_exams.php");
    exit();
}

// Fetch all exams
$query = "SELECT * FROM exams ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Exams - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <!-- Header -->
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <h2>Manage Exams</h2>
        <table>
            <tr>
                <th>Exam Title</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            <?php while ($exam = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($exam['title']); ?></td>
                    <td><?php echo htmlspecialchars($exam['subject']); ?></td>
                    <td>
                        <?php if ($exam['status'] == 'active') { ?>
                            <span style="color: green;">Active</span>
                        <?php } else { ?>
                            <span style="color: red;">Inactive</span>
                        <?php } ?>
                    </td>
                    <td><?php echo $exam['created_at']; ?></td>
                    <td>
                        <?php if ($exam['status'] == 'inactive') { ?>
                            <a href="manage_exams.php?action=start&exam_id=<?php echo $exam['id']; ?>" 
                               style="color: green;">Start</a>
                        <?php } else { ?>
                            <a href="manage_exams.php?action=stop&exam_id=<?php echo $exam['id']; ?>" 
                               style="color: red;">Stop</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

</body>
</html>
