<?php
session_start();
require_once('../includes/db.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle user status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $user_id = $_POST['user_id'];
    $new_status = $_POST['status'];
    
    // Validate status
    $allowed_statuses = ['pending', 'approved', 'rejected'];
    if (!in_array($new_status, $allowed_statuses)) {
        $_SESSION['error'] = "Invalid status selected.";
        header("Location: manage_users.php");
        exit();
    }
    
    // Update user status
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "User status updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update user status: " . $conn->error;
    }
    header("Location: manage_users.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "User not found.";
        header("Location: manage_users.php");
        exit();
    }
    
    $user = $result->fetch_assoc();
    
    // Prevent admin from deleting themselves
    if ($user['role'] === 'admin' && $user_id == $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot delete your own admin account.";
        header("Location: manage_users.php");
        exit();
    }
    
    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "User deleted successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete user: " . $conn->error;
    }
    header("Location: manage_users.php");
    exit();
}

// Search functionality
$search = '';
$where_clause = '';
$params = [];
$types = '';

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = "%" . trim($_GET['search']) . "%";
    $where_clause = "WHERE (name LIKE ? OR email LIKE ? OR username LIKE ?)";
    $params = [$search, $search, $search];
    $types = 'sss';
}

// Role filter
if (isset($_GET['role']) && in_array($_GET['role'], ['admin', 'staff', 'student'])) {
    if ($where_clause === '') {
        $where_clause = "WHERE role = ?";
    } else {
        $where_clause .= " AND role = ?";
    }
    $params[] = $_GET['role'];
    $types .= 's';
}

// Status filter
if (isset($_GET['status']) && in_array($_GET['status'], ['pending', 'approved', 'rejected'])) {
    if ($where_clause === '') {
        $where_clause = "WHERE status = ?";
    } else {
        $where_clause .= " AND status = ?";
    }
    $params[] = $_GET['status'];
    $types .= 's';
}

// Pagination
$results_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $results_per_page;

// Get total number of users for pagination
$count_query = "SELECT COUNT(*) as total FROM users $where_clause";
$count_stmt = $conn->prepare($count_query);

if ($where_clause !== '') {
    $count_stmt->bind_param($types, ...$params);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $results_per_page);

// Get users with filters and pagination
$query = "SELECT * FROM users $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);

if ($where_clause !== '') {
    $params[] = $results_per_page;
    $params[] = $offset;
    $types .= 'ii';
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param("ii", $results_per_page, $offset);
}

$stmt->execute();
$users = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Online Exam System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/approve_users.css">
     
</head>
<body>


    <div class="container-fluid">
        <header>
            <h1><i class="fas fa-tachometer-alt"></i>Online Exam Syestem, Admin Dashboard</h1>
            <div class="user-info">
                <span>Welcome To Your Dashboard:, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
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
                    <h1 class="h2">Manage Users</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="add_user.php" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add New User
                        </a>
                    </div>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Filters Card -->
                <div class="card filter-card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       placeholder="Name, Email or Username" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="">All Roles</option>
                                    <option value="admin" <?= (isset($_GET['role']) && $_GET['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                                    <option value="staff" <?= (isset($_GET['role']) && $_GET['role'] === 'staff') ? 'selected' : '' ?>>Staff</option>
                                    <option value="student" <?= (isset($_GET['role']) && $_GET['role'] === 'student') ? 'selected' : '' ?>>Student</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="pending" <?= (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="approved" <?= (isset($_GET['status']) && $_GET['status'] === 'approved') ? 'selected' : '' ?>>Approved</option>
                                    <option value="rejected" <?= (isset($_GET['status']) && $_GET['status'] === 'rejected') ? 'selected' : '' ?>>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($users->num_rows > 0): ?>
                                        <?php while ($user = $users->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($user['id']) ?></td>
                                                <td><?= htmlspecialchars($user['name']) ?></td>
                                                <td><?= htmlspecialchars($user['username']) ?></td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?= $user['role'] === 'admin' ? 'bg-danger' : 
                                                           ($user['role'] === 'staff' ? 'bg-primary' : 'bg-success') ?>">
                                                        <?= ucfirst($user['role']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge 
                                                        <?= $user['status'] === 'approved' ? 'bg-success' : 
                                                           ($user['status'] === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') ?>">
                                                        <?= ucfirst($user['status']) ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M d, Y h:i A', strtotime($user['created_at'])) ?></td>
                                                <td class="action-btns">
                                                    <form method="POST" action="manage_users.php" class="d-inline">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <select name="status" class="form-select form-select-sm d-inline w-auto" 
                                                                onchange="this.form.submit()" <?= $user['role'] === 'admin' && $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
                                                            <option value="pending" <?= $user['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                            <option value="approved" <?= $user['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                                                            <option value="rejected" <?= $user['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                                        </select>
                                                        <input type="hidden" name="update_status" value="1">
                                                    </form>
                                                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="manage_users.php?delete=<?= $user['id'] ?>" 
                                                       class="btn btn-sm btn-danger <?= $user['role'] === 'admin' && $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>"
                                                       onclick="return confirm('Are you sure you want to delete this user?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No users found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Previous</a>
                                    </li>
                                    
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-submit status change after confirmation
        document.querySelectorAll('select[name="status"]').forEach(select => {
            select.addEventListener('change', function() {
                if (confirm('Are you sure you want to change this user\'s status?')) {
                    this.form.submit();
                } else {
                    // Reset to original value
                    this.value = this.dataset.originalValue;
                }
            });
            
            // Store original value
            select.dataset.originalValue = select.value;
        });
    </script>
</body>
</html>