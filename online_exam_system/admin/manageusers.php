<?php
session_start();
require_once '../includes/db.php'; 
// Verify admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize variables
$search = '';
$role_filter = '';
$status_filter = '';
$message = '';

// Handle search and filters
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $role_filter = isset($_GET['role']) ? $_GET['role'] : '';
    $status_filter = isset($_GET['status']) ? $_GET['status'] : '';
}

// Handle user actions (approve, reject, delete, etc.)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && isset($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        
        switch ($_POST['action']) {
            case 'approve':
                $stmt = $conn->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                if ($stmt->execute()) {
                    $message = "User approved successfully!";
                } else {
                    $message = "Error approving user: " . $stmt->error;
                }
                break;
                
            case 'reject':
                $stmt = $conn->prepare("UPDATE users SET status = 'rejected' WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                if ($stmt->execute()) {
                    $message = "User rejected successfully!";
                } else {
                    $message = "Error rejecting user: " . $stmt->error;
                }
                break;
                
            case 'delete':
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                if ($stmt->execute()) {
                    $message = "User deleted successfully!";
                } else {
                    $message = "Error deleting user: " . $stmt->error;
                }
                break;
                
            case 'promote_to_staff':
                $stmt = $conn->prepare("UPDATE users SET role = 'staff' WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                if ($stmt->execute()) {
                    $message = "User promoted to staff successfully!";
                } else {
                    $message = "Error promoting user: " . $stmt->error;
                }
                break;
                
            case 'promote_to_admin':
                $stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                if ($stmt->execute()) {
                    $message = "User promoted to admin successfully!";
                } else {
                    $message = "Error promoting user: " . $stmt->error;
                }
                break;
                
            case 'demote_to_student':
                $stmt = $conn->prepare("UPDATE users SET role = 'student' WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                if ($stmt->execute()) {
                    $message = "User demoted to student successfully!";
                } else {
                    $message = "Error demoting user: " . $stmt->error;
                }
                break;
                
            case 'reset_password':
                $new_password = password_hash('default123', PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $new_password, $user_id);
                if ($stmt->execute()) {
                    $message = "Password reset to 'default123' successfully!";
                } else {
                    $message = "Error resetting password: " . $stmt->error;
                }
                break;
        }
    }
}

// Build query for user listing
$query = "SELECT * FROM users WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (username LIKE ? OR email LIKE ? OR CONCAT(first_name, ' ', last_name) LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'sss';
}

if (!empty($role_filter)) {
    $query .= " AND role = ?";
    $params[] = $role_filter;
    $types .= 's';
}

if (!empty($status_filter)) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

$query .= " ORDER BY created_at DESC";

// Prepare and execute query
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);

 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/manageusers.css">
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
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">User Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="add_user.php" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus"></i> Add New User
                    </a>
                </div>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <!-- Search and Filter Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Search by name, username or email">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role">
                                <option value="">All Roles</option>
                                <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="staff" <?php echo $role_filter === 'staff' ? 'selected' : ''; ?>>Staff</option>
                                <option value="student" <?php echo $role_filter === 'student' ? 'selected' : ''; ?>>Student</option>
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
            
            <!-- Users Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No users found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td>
                                                <?php 
                                                $name = '';
                                                if (!empty($user['first_name']) || !empty($user['last_name'])) {
                                                    $name = trim($user['first_name'] . ' ' . $user['last_name']);
                                                }
                                                echo htmlspecialchars($name ?: 'N/A'); 
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?php echo $user['role'] === 'admin' ? 'bg-danger' : 
                                                          ($user['role'] === 'staff' ? 'bg-warning text-dark' : 'bg-primary'); ?>">
                                                    <?php echo ucfirst($user['role']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    <?php echo $user['status'] === 'approved' ? 'bg-success' : 
                                                          ($user['status'] === 'pending' ? 'bg-secondary' : 'bg-danger'); ?>">
                                                    <?php echo ucfirst($user['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                            type="button" id="actionsDropdown<?php echo $user['id']; ?>" 
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="actionsDropdown<?php echo $user['id']; ?>">
                                                        <?php if ($user['status'] === 'pending'): ?>
                                                            <li>
                                                                <form method="POST" style="display:inline;">
                                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                    <input type="hidden" name="action" value="approve">
                                                                    <button type="submit" class="dropdown-item text-success">
                                                                        <i class="fas fa-check"></i> Approve
                                                                    </button>
                                                                </form>
                                                            </li>
                                                            <li>
                                                                <form method="POST" style="display:inline;">
                                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                    <input type="hidden" name="action" value="reject">
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="fas fa-times"></i> Reject
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($user['role'] !== 'admin' && $user['status'] === 'approved'): ?>
                                                            <li>
                                                                <form method="POST" style="display:inline;">
                                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                    <input type="hidden" name="action" value="promote_to_staff">
                                                                    <button type="submit" class="dropdown-item text-warning">
                                                                        <i class="fas fa-user-shield"></i> Promote to Staff
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($user['role'] === 'staff' && $user['status'] === 'approved'): ?>
                                                            <li>
                                                                <form method="POST" style="display:inline;">
                                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                    <input type="hidden" name="action" value="promote_to_admin">
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="fas fa-user-cog"></i> Promote to Admin
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($user['role'] !== 'student' && $user['status'] === 'approved'): ?>
                                                            <li>
                                                                <form method="POST" style="display:inline;">
                                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                    <input type="hidden" name="action" value="demote_to_student">
                                                                    <button type="submit" class="dropdown-item text-primary">
                                                                        <i class="fas fa-user-graduate"></i> Demote to Student
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        <?php endif; ?>
                                                        
                                                        <li>
                                                            <form method="POST" style="display:inline;">
                                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                <input type="hidden" name="action" value="reset_password">
                                                                <button type="submit" class="dropdown-item text-info">
                                                                    <i class="fas fa-key"></i> Reset Password
                                                                </button>
                                                            </form>
                                                        </li>
                                                        
                                                        <li><hr class="dropdown-divider"></li>
                                                        
                                                        <li>
                                                            <form method="POST" style="display:inline;" 
                                                                  onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                <input type="hidden" name="action" value="delete">
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-trash"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                                
                                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>