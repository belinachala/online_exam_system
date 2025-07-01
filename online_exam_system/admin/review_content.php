<?php
session_start();
require_once '../includes/db.php';

// Helper function to shorten text
function shortenText($text, $length = 100) {
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length) . '...';
    }
    return $text;
}

// Verify admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize variables
$content_type = isset($_GET['type']) ? $_GET['type'] : 'papers';
$search = '';
$status_filter = '';
$subject_filter = '';
$message = '';

// Handle search and filters
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
    $subject_filter = isset($_GET['subject']) ? intval($_GET['subject']) : '';
}

// Handle content approval actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && isset($_POST['content_id'])) {
        $content_id = intval($_POST['content_id']);
        $content_type = $_POST['content_type'];
        
        switch ($_POST['action']) {
            case 'approve':
                $table = $content_type === 'papers' ? 'question_papers' : 'questions';
                $stmt = $conn->prepare("UPDATE $table SET status = 'approved' WHERE id = ?");
                $stmt->bind_param("i", $content_id);
                if ($stmt->execute()) {
                    $message = "Content approved successfully!";
                } else {
                    $message = "Error approving content: " . $stmt->error;
                }
                break;
                
            case 'reject':
                $table = $content_type === 'papers' ? 'question_papers' : 'questions';
                $stmt = $conn->prepare("UPDATE $table SET status = 'rejected' WHERE id = ?");
                $stmt->bind_param("i", $content_id);
                if ($stmt->execute()) {
                    $message = "Content rejected successfully!";
                } else {
                    $message = "Error rejecting content: " . $stmt->error;
                }
                break;
                
            case 'delete':
                $table = $content_type === 'papers' ? 'question_papers' : 'questions';
                $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
                $stmt->bind_param("i", $content_id);
                if ($stmt->execute()) {
                    $message = "Content deleted successfully!";
                } else {
                    $message = "Error deleting content: " . $stmt->error;
                }
                break;
        }
    }
}

// Fetch subjects for filter
$subjects = [];
$subject_result = $conn->query("SELECT * FROM subjects ORDER BY name");
if ($subject_result) {
    $subjects = $subject_result->fetch_all(MYSQLI_ASSOC);
}

// Fetch content based on type
if ($content_type === 'papers') {
    // Build query for question papers
    $query = "SELECT qp.*, u.username, s.name as subject_name 
              FROM question_papers qp
              JOIN users u ON qp.staff_id = u.id
              JOIN subjects s ON qp.subject_id = s.id
              WHERE 1=1";
    $params = [];
    $types = '';

    if (!empty($search)) {
        $query .= " AND (qp.title LIKE ? OR qp.description LIKE ? OR u.username LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= 'sss';
    }

    if (!empty($status_filter)) {
        $query .= " AND qp.status = ?";
        $params[] = $status_filter;
        $types .= 's';
    }

    if (!empty($subject_filter)) {
        $query .= " AND qp.subject_id = ?";
        $params[] = $subject_filter;
        $types .= 'i';
    }

    $query .= " ORDER BY qp.created_at DESC";

    // Prepare and execute query
    $stmt = $conn->prepare($query);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $content_items = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Build query for questions
    $query = "SELECT q.*, qp.title as paper_title, u.username, s.name as subject_name 
              FROM questions q
              JOIN question_papers qp ON q.paper_id = qp.id
              JOIN users u ON qp.staff_id = u.id
              JOIN subjects s ON qp.subject_id = s.id
              WHERE 1=1";
    $params = [];
    $types = '';

    if (!empty($search)) {
        $query .= " AND (q.question_text LIKE ? OR qp.title LIKE ? OR u.username LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= 'sss';
    }

    if (!empty($status_filter)) {
        $query .= " AND q.status = ?";
        $params[] = $status_filter;
        $types .= 's';
    }

    if (!empty($subject_filter)) {
        $query .= " AND qp.subject_id = ?";
        $params[] = $subject_filter;
        $types .= 'i';
    }

    $query .= " ORDER BY q.created_at DESC";

    // Prepare and execute query
    $stmt = $conn->prepare($query);

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $content_items = $result->fetch_all(MYSQLI_ASSOC);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/review_content.css">
</head>
<body>
    <div class="container-fluid">
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

    <div class="row">
        <?php include 'sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Content Review</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="review_content.php?type=papers" class="btn btn-sm <?php echo $content_type === 'papers' ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                            <i class="fas fa-file-alt"></i> Question Papers
                        </a>
                        <a href="review_content.php?type=questions" class="btn btn-sm <?php echo $content_type === 'questions' ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                            <i class="fas fa-question-circle"></i> Questions
                        </a>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <!-- Search and Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <input type="hidden" name="type" value="<?php echo $content_type; ?>">
                        
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="<?php echo $content_type === 'papers' ? 'Search by title, description or creator' : 'Search by question text, paper title or creator'; ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="subject" class="form-label">Subject</label>
                            <select class="form-select" id="subject" name="subject">
                                <option value="">All Subjects</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo $subject['id']; ?>" <?php echo $subject_filter == $subject['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($subject['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Content Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <?php if ($content_type === 'papers'): ?>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Subject</th>
                                        <th>Description</th>
                                        <th>Created By</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($content_items)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No question papers found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($content_items as $paper): ?>
                                            <tr>
                                                <td><?php echo $paper['id']; ?></td>
                                                <td><?php echo htmlspecialchars($paper['title']); ?></td>
                                                <td><?php echo htmlspecialchars($paper['subject_name']); ?></td> 
                                                <td><?php echo isset($paper['description']) ? htmlspecialchars(shortenText($paper['description'], 1000)) : 'No description provided'; ?></td>
                                                <td><?php echo htmlspecialchars($paper['username']); ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?php echo $paper['status'] === 'approved' ? 'bg-success' : 
                                                              ($paper['status'] === 'pending' ? 'bg-secondary' : 'bg-danger'); ?>">
                                                        <?php echo ucfirst($paper['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($paper['created_at'])); ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                                type="button" id="actionsDropdown<?php echo $paper['id']; ?>" 
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                            Actions
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="actionsDropdown<?php echo $paper['id']; ?>">
                                                            <?php if ($paper['status'] === 'pending'): ?>
                                                                <li>
                                                                    <form method="POST" style="display:inline;">
                                                                        <input type="hidden" name="content_id" value="<?php echo $paper['id']; ?>">
                                                                        <input type="hidden" name="content_type" value="papers">
                                                                        <input type="hidden" name="action" value="approve">
                                                                        <button type="submit" class="dropdown-item text-success">
                                                                            <i class="fas fa-check"></i> Approve
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form method="POST" style="display:inline;">
                                                                        <input type="hidden" name="content_id" value="<?php echo $paper['id']; ?>">
                                                                        <input type="hidden" name="content_type" value="papers">
                                                                        <input type="hidden" name="action" value="reject">
                                                                        <button type="submit" class="dropdown-item text-danger">
                                                                            <i class="fas fa-times"></i> Reject
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            <?php endif; ?>
                                                            
                                                            <li>
                                                                <a href="preview_paper.php?id=<?php echo $paper['id']; ?>" 
                                                                   class="dropdown-item text-primary">
                                                                    <i class="fas fa-eye"></i> Preview
                                                                </a>
                                                            </li>
                                                            
                                                            <li><hr class="dropdown-divider"></li>
                                                            
                                                            <li>
                                                                <form method="POST" style="display:inline;" 
                                                                      onsubmit="return confirm('Are you sure you want to delete this question paper?');">
                                                                    <input type="hidden" name="content_id" value="<?php echo $paper['id']; ?>">
                                                                    <input type="hidden" name="content_type" value="papers">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="fas fa-trash"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Question</th>
                                        <th>Paper Title</th>
                                        <th>Subject</th>
                                        <th>Created By</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($content_items)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No questions found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($content_items as $question): ?>
                                            <tr>
                                                <td><?php echo $question['id']; ?></td>
                                                <td><?php echo htmlspecialchars(shortenText($question['question_text'], 50)); ?></td>
                                                <td><?php echo htmlspecialchars($question['paper_title']); ?></td>
                                                <td><?php echo htmlspecialchars($question['subject_name']); ?></td>
                                                <td><?php echo htmlspecialchars($question['username']); ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?php echo $question['status'] === 'approved' ? 'bg-success' : 
                                                              ($question['status'] === 'pending' ? 'bg-secondary' : 'bg-danger'); ?>">
                                                        <?php echo ucfirst($question['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($question['created_at'])); ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                                type="button" id="actionsDropdown<?php echo $question['id']; ?>" 
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                            Actions
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="actionsDropdown<?php echo $question['id']; ?>">
                                                            <?php if ($question['status'] === 'pending'): ?>
                                                                <li>
                                                                    <form method="POST" style="display:inline;">
                                                                        <input type="hidden" name="content_id" value="<?php echo $question['id']; ?>">
                                                                        <input type="hidden" name="content_type" value="questions">
                                                                        <input type="hidden" name="action" value="approve">
                                                                        <button type="submit" class="dropdown-item text-success">
                                                                            <i class="fas fa-check"></i> Approve
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <form method="POST" style="display:inline;">
                                                                        <input type="hidden" name="content_id" value="<?php echo $question['id']; ?>">
                                                                        <input type="hidden" name="content_type" value="questions">
                                                                        <input type="hidden" name="action" value="reject">
                                                                        <button type="submit" class="dropdown-item text-danger">
                                                                            <i class="fas fa-times"></i> Reject
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            <?php endif; ?>
                                                            
                                                            <li>
                                                                <a href="view_question.php?id=<?php echo $question['id']; ?>" 
                                                                   class="dropdown-item text-primary">
                                                                    <i class="fas fa-eye"></i> View
                                                                </a>
                                                            </li>
                                                            
                                                            <li><hr class="dropdown-divider"></li>
                                                            
                                                            <li>
                                                                <form method="POST" style="display:inline;" 
                                                                      onsubmit="return confirm('Are you sure you want to delete this question?');">
                                                                    <input type="hidden" name="content_id" value="<?php echo $question['id']; ?>">
                                                                    <input type="hidden" name="content_type" value="questions">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="fas fa-trash"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>