<?php
session_start();
include('../includes/db.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle user approval
if (isset($_GET['approve'])) {
    $user_id = $_GET['approve'];
    
    // Update user status to 'approved'
    $query = "UPDATE users SET status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $success = "User approved successfully!";
    } else {
        $error = "Failed to approve the user. Please try again.";
    }
}

// Handle user rejection
if (isset($_GET['reject'])) {
    $user_id = $_GET['reject'];
    
    // Delete the user from the database
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $success = "User rejected successfully!";
    } else {
        $error = "Failed to reject the user. Please try again.";
    }
}

// Fetch all users with pending status
$query = "SELECT * FROM users WHERE status = 'pending'";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
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
 

<h2>Approve Users</h2>

<?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

<table border="1">
    <thead>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td><?php echo htmlspecialchars($user['status']); ?></td>
                <td>
                    <a href="approve_users.php?approve=<?php echo $user['id']; ?>">Approve</a> |
                    <a href="approve_users.php?reject=<?php echo $user['id']; ?>">Reject</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include('../includes/footer.php'); ?>
        </body>
        </html>