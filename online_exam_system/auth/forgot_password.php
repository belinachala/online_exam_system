<?php
session_start();
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);

    // Check if user exists
    $query = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a new random password
        $new_password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $query = "UPDATE users SET password = :password WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':username', $username);

        if ($stmt->execute()) {
            $success = "Your new password is: <strong>$new_password</strong>. Please change it after logging in.";
        } else {
            $error = "Error resetting password. Try again.";
        }
    } else {
        $error = "Username not found!";
    }
}
?>

<?php include('../includes/header.php'); ?>

<h2>Forgot Password</h2>
<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
<?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>

<form method="POST">
    <label for="username">Enter your Username:</label>
    <input type="text" name="username" required>
    
    <button type="submit">Reset Password</button>
</form>

<p><a href="login.php">Back to Login</a></p>

<?php include('../includes/footer.php'); ?>
