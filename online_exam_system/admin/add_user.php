<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

// Verify admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize variables
$username = $email = $role = '';
$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = isset($_POST['role']) ? $_POST['role'] : '';
    
    // Validation
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Username can only contain letters, numbers and underscores";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (!in_array($role, ['student', 'staff'])) {
        $errors[] = "Invalid role selected";
    }
    
    // Check for existing username or email
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors[] = "Username or email already exists";
        }
    }
    
    // Create user if no errors
    if (empty($errors)) {
        // Generate a random password
        $temp_password = bin2hex(random_bytes(4)); // 8-character temporary password
        $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
        $created_at = date('Y-m-d H:i:s');
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, status, created_at) VALUES (?, ?, ?, ?, 'approved', ?)");
        $stmt->bind_param("sssss", $username, $email, $hashed_password, $role, $created_at);
        
        if ($stmt->execute()) {
            // Prepare success message with credentials
            $user_id = $stmt->insert_id;
            $_SESSION['success'] = "User registered successfully!<br>
                                  <strong>Username:</strong> $username<br>
                                  <strong>Temporary Password:</strong> $temp_password<br>
                                  <strong>Role:</strong> " . ucfirst($role);
            
            header("Location: manage_users.php");
            exit();
        } else {
            $errors[] = "Error creating user: " . $stmt->error;
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/add_user.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-user-plus"></i> Register New Exam User</h4>
                    <a href="manage_users.php" class="btn btn-sm btn-light float-right">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Please fix these errors:</h5>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> This form registers users with immediate access to the exam system.
                        A temporary password will be generated automatically.
                    </div>
                    
                    <form method="POST" id="addUserForm">
                        <div class="form-group">
                            <label for="username"><i class="fas fa-user"></i> Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= htmlspecialchars($username); ?>" required
                                   placeholder=" ">
                            <small class="form-text text-muted"> </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($email); ?>" required
                                   placeholder=" ">
                        </div>
                        
                        <div class="form-group">
                            <label for="role"><i class="fas fa-user-tag"></i> User Role</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="">Select User</option>
                                <option value="student" <?= $role === 'student' ? 'selected' : ''; ?>>Student</option>
                                <option value="staff" <?= $role === 'staff' ? 'selected' : ''; ?>>Staff</option>
                            </select>
                            <small class="form-text text-muted"> </small>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-user-check"></i> Register User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Client-side validation
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    const username = document.getElementById('username').value;
    if (!/^[a-zA-Z0-9_]{4,20}$/.test(username)) {
        alert('Username must be 4-20 characters and can only contain letters, numbers and underscores');
        e.preventDefault();
    }
});
</script>

<?php
include __DIR__ . '/../includes/footer.php';
$conn->close();
?>
</body>
</html>