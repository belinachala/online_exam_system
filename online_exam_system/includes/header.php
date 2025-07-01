<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam System</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <header>
        <h1>Online Exam System</h1>
        <nav>
            <ul>
                <li><a href="/online_exam_system/index.php">Home</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="/online_exam_system/auth/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="/online_exam_system/auth/login.php">Login</a></li>
                    <li><a href="/online_exam_system/auth/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
 </body>
</html>