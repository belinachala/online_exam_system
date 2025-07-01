<?php
session_start();
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = $_POST['role'];

    // Check if fields are empty
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = "All fields are required!";
    } 
    // Check if passwords match
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } 
    else {
        // Check if email or username already exists
        $query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username or Email already exists!";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user (default as pending)
            $query = "INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, 'pending')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $success = "Registration successful! Waiting for admin approval.";
            } else {
                $error = "Registration failed. Try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Online Exam System</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="register-wrapper">
        <div class="register-container animate__animated animate__fadeIn">
            <div class="register-header">
                <div class="logo">
                    <i class="fas fa-laptop-code"></i>
                    <span>CodeExam</span>
                </div>
                <h2 class="animate__animated animate__fadeInDown">Create Your Account</h2>
                <p class="animate__animated animate__fadeInDown animate__delay-1s">Join thousands of students mastering their exams</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert-message error animate__animated animate__shakeX">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert-message success animate__animated animate__fadeIn">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="register-form animate__animated animate__fadeIn animate__delay-1s">
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
                        <i class="fas fa-envelope"></i>
                    </div>
                    <input type="email" id="email" name="email" required 
                           placeholder=""
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <label for="email">Email</label>
                </div>
                
                <div class="form-group">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input type="password" id="password" name="password" required 
                           placeholder="">
                    <label for="password">Password</label>
                    <span class="toggle-password" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <div class="form-group">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           placeholder="">
                    <label for="confirm_password">Confirm Password</label>
                    <span class="toggle-password" onclick="togglePassword('confirm_password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <div class="form-group">
                    <div class="input-icon">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    <select id="role" name="role" required>
                        <option value="" disabled selected>Select your role</option>
                        <option value="staff" <?php echo (isset($_POST['role']) && $_POST['role'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
                        <option value="student" <?php echo (isset($_POST['role']) && $_POST['role'] == 'student') ? 'selected' : ''; ?>>Student</option>
                    </select>
                    <label for="role" class="select-label">Role</label>
                </div>

                <button type="submit" class="register-btn">
                    <i class="fas fa-user-plus"></i> Register
                    <span class="btn-animation"></span>
                </button>
            </form>

            <div class="register-footer animate__animated animate__fadeIn animate__delay-2s">
                <p>Already have an account? <a href="login.php" class="login-link">Login here</a></p>
                <div class="strength-meter">
                    <p>Password Strength:</p>
                    <div class="strength-bars">
                        <span class="bar"></span>
                        <span class="bar"></span>
                        <span class="bar"></span>
                    </div>
                     <p>Or if you don't ready to access now, go:</p>
                    <div class="social-icons">
                     <li><a href="../home.php" class="active"><i class="fas fa-home"></i> Home</a></li>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="register-graphics animate__animated animate__fadeInRight">
            <div class="graphic-circle circle-1"></div>
            <div class="graphic-circle circle-2"></div>
            <div class="graphic-circle circle-3"></div>
            <img src="https://cdn-icons-png.flaticon.com/512/4406/4406232.png" alt="Registration Illustration" class="register-image">
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.parentElement.querySelector('.toggle-password i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const bars = document.querySelectorAll('.strength-bars .bar');
            let strength = 0;
            
            // Reset bars
            bars.forEach(bar => bar.style.backgroundColor = '#e9ecef');
            
            // Check password strength
            if (password.length > 0) strength++;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) strength++;
            
            // Update bars
            for (let i = 0; i < strength; i++) {
                if (i === 0) bars[i].style.backgroundColor = '#ff6b6b';
                else if (i === 1) bars[i].style.backgroundColor = '#ffc107';
                else if (i === 2) bars[i].style.backgroundColor = '#28a745';
            }
        });
        
        // Add animation to form inputs on focus
        document.querySelectorAll('.form-group input, .form-group select').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '' && this.tagName !== 'SELECT') {
                    this.parentElement.classList.remove('focused');
                }
            });
            
            // Check if input has value on page load
            if (input.value !== '' || input.tagName === 'SELECT') {
                input.parentElement.classList.add('focused');
            }
        });
    </script>
</body>
</html>