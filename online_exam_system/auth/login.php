<?php
session_start();
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "All fields are required!";
    } else {
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                if ($user['status'] === 'approved') {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    if ($user['role'] == 'admin') {
                        header("Location: ../admin/dashboard.php");
                    } elseif ($user['role'] == 'staff') {
                        header("Location: ../staff/dashboard.php");
                    } elseif ($user['role'] == 'student') {
                        header("Location: ../student/dashboard.php");
                    }
                    exit();
                } else {
                    $error = "Your account is pending approval!";
                }
            } else {
                $error = "Invalid username or password!";
            }
        } else {
            $error = "User not found!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Online Exam System</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container animate__animated animate__fadeIn">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-laptop-code"></i>
                    <span>Online Exam</span>
                </div>
                <h2 class="animate__animated animate__fadeInDown">Welcome Back!</h2>
                <p class="animate__animated animate__fadeInDown animate__delay-1s">Login to access your exams and learning materials</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="error-message animate__animated animate__shakeX">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="login-form animate__animated animate__fadeIn animate__delay-1s">
                <div class="form-group">
                    <div class="input-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <input type="text" id="username" name="username" required 
                           placeholder=""
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                    <label for="username">Username</label>
                </div>
                
                <div class="form-group">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input type="password" id="password" name="password" required placeholder="">
                    <label for="password">Password</label>
                    <span class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <div class="form-options">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                    <span class="btn-animation"></span>
                </button>
            </form>

            <div class="login-footer animate__animated animate__fadeIn animate__delay-2s">
                <p>Don't have an account? <a href="register.php" class="register-link">Create one now</a></p>
                <div class="social-login">
                    <p>Or if you don't ready to access now, go:</p>
                    <div class="social-icons">
                     <li><a href="../home.php" class="active"><i class="fas fa-home"></i> Home</a></li>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="login-graphics animate__animated animate__fadeInRight">
            <div class="graphic-circle circle-1"></div>
            <div class="graphic-circle circle-2"></div>
            <div class="graphic-circle circle-3"></div>
            <img src="https://cdn-icons-png.flaticon.com/512/2991/2991232.png" alt="Login Illustration" class="login-image">
        </div>
    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.querySelector('.toggle-password i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Add animation to form inputs on focus
        document.querySelectorAll('.form-group input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });
            
            // Check if input has value on page load
            if (input.value !== '') {
                input.parentElement.classList.add('focused');
            }
        });
    </script>
</body>
</html>