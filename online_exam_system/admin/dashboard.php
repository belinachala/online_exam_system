<?php
session_start();
include('../includes/db.php');

// Verify admin role
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Fetch pending users
$users_query = "SELECT * FROM users WHERE status = 'pending'";
$users_result = $conn->query($users_query);
$pending_users = $users_result->fetch_all(MYSQLI_ASSOC);

// Fetch pending question papers
$papers_query = "SELECT qp.*, u.username 
                 FROM question_papers qp
                 JOIN users u ON qp.staff_id = u.id
                 WHERE qp.status = 'pending'";
$papers_result = $conn->query($papers_query);
$pending_papers = $papers_result->fetch_all(MYSQLI_ASSOC);

// Fetch pending questions
$questions_query = "SELECT q.*, qp.title as paper_title, u.username 
                    FROM questions q
                    JOIN question_papers qp ON q.paper_id = qp.id
                    JOIN users u ON qp.staff_id = u.id
                    WHERE q.status = 'pending'";
$questions_result = $conn->query($questions_query);
$pending_questions = $questions_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-tachometer-alt"></i> Online Exam Syestem, Admin Dashboard</h1>
            <div class="user-info">
                <span>Welcome To Your Dashboard: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="../home.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </header>

        <nav>
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="manageusers.php"><i class="fas fa-users"></i> Manage Users</a></li>
                <li><a href="review_content.php"><i class="fas fa-file-alt"></i> Review Content</a></li> 
            </ul>
        </nav>

        <div class="content-section">
            <h2><i class="fas fa-user-clock"></i> Pending User Approvals</h2>
            <?php if (count($pending_users) > 0): ?>
                <table class="approval-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Registered Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td class="actions">
                                    <a href="approve_user.php?id=<?= $user['id']; ?>" class="btn-approve"><i class="fas fa-check"></i> Approve</a>
                                    <a href="reject_user.php?id=<?= $user['id']; ?>" class="btn-reject"><i class="fas fa-times"></i> Reject</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-pending">No pending user approvals.</p>
            <?php endif; ?>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-file-alt"></i> Pending Question Papers</h2>
            <?php if (count($pending_papers) > 0): ?>
                <table class="approval-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Subject id</th>
                            <th>Created By</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_papers as $paper): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($paper['title']); ?></td>
                                <td><?php echo htmlspecialchars($paper['subject_id']); ?></td>
                                <td><?php echo htmlspecialchars($paper['username']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($paper['created_at'])); ?></td>
                                <td class="actions">
                                    <a href="approve_paper.php?id=<?= $paper['id']; ?>" class="btn-approve"><i class="fas fa-check"></i> Approve</a>
                                    <a href="reject_paper.php?id=<?= $paper['id']; ?>" class="btn-reject"><i class="fas fa-times"></i> Reject</a>
                                    <a href="preview_paper.php?id=<?= $paper['id']; ?>" class="btn-preview"><i class="fas fa-eye"></i> Preview</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-pending">No pending question papers.</p>
            <?php endif; ?>
        </div>

        <div class="content-section">
            <h2><i class="fas fa-question-circle"></i> Pending Questions</h2>
            <?php if (count($pending_questions) > 0): ?>
                <table class="approval-table">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Paper Title</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_questions as $question): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(substr($question['question_text'], 0, 50)); ?>...</td>
                                <td><?php echo htmlspecialchars($question['paper_title']); ?></td>
                                <td><?php echo htmlspecialchars($question['username']); ?></td>
                                <td class="actions">
                                    <a href="approve_question.php?id=<?= $question['id']; ?>" class="btn-approve"><i class="fas fa-check"></i> Approve</a>
                                    <a href="reject_question.php?id=<?= $question['id']; ?>" class="btn-reject"><i class="fas fa-times"></i> Reject</a>
                                    <a href="view_question.php?id=<?= $question['id']; ?>" class="btn-preview"><i class="fas fa-eye"></i> View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-pending">No pending questions.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>