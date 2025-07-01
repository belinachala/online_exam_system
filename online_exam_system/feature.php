<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features | Online Exam System</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="feature.css">
    <link rel="stylesheet" href="about.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                    <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="about.php" class="active"><i class="fas fa-info-circle"></i> About</a></li>
                    <li><a href="feature.php"><i class="fas fa-star"></i> Features</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="auth/login.php" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <a href="auth/register.php" class="btn-primary"><i class="fas fa-user-plus"></i> Register</a>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero">
            <h1><i class="fas fa-gem"></i> Powerful Features</h1>
            <p>Discover what makes our Online Exam System stand out!</p>
        </section>

        <section class="features-container">
            <!-- Feature Card 1 -->
            <div class="feature-card">
                <div class="feature-icon" style="background: linear-gradient(135deg, #ff6b6b 0%, #ff8e8e 100%);">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure Exam Environment</h3>
                <p>Advanced anti-cheating measures including screen monitoring and plagiarism detection.</p>
            </div>

            <!-- Feature Card 2 -->
            <div class="feature-card">
                <div class="feature-icon" style="background: linear-gradient(135deg, #48dbfb 0%, #6c5ce7 100%);">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Instant Grading</h3>
                <p>Automated scoring for multiple-choice questions with detailed analytics.</p>
            </div>

            <!-- Feature Card 3 -->
            <div class="feature-card">
                <div class="feature-icon" style="background: linear-gradient(135deg, #1dd1a1 0%, #10ac84 100%);">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Multi-User Roles</h3>
                <p>Separate dashboards for Admins, Teachers, and Students with tailored permissions.</p>
            </div>

            <!-- Feature Card 4 -->
            <div class="feature-card">
                <div class="feature-icon" style="background: linear-gradient(135deg, #f368e0 0%, #ff9ff3 100%);">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3>Real-Time Analytics</h3>
                <p>Track student performance with interactive graphs and reports.</p>
            </div>

            <!-- Feature Card 5 -->
            <div class="feature-card">
                <div class="feature-icon" style="background: linear-gradient(135deg, #ff9f43 0%, #feca57 100%);">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <h3>Cloud-Based</h3>
                <p>Access exams from anywhere, anytime with 99.9% uptime.</p>
            </div>

            <!-- Feature Card 6 -->
            <div class="feature-card">
                <div class="feature-icon" style="background: linear-gradient(135deg, #5f27cd 0%, #341f97 100%);">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h3>Multiple Question Types</h3>
                <p>Support for MCQs, essays, coding challenges, and more.</p>
            </div>
        </section>

        <section class="cta">
            <h2>Ready to Transform Your Exam Process?</h2>
              <a href="auth/register.php" class="btn-primary"><i class="fas fa-user-plus"></i> Get Start Now</a>

        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <i class="fas fa-laptop-code"></i>
                    <span>CodeExam</span>
                </div>
                <div class="footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                    <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="about.php" class="active"><i class="fas fa-info-circle"></i> About</a></li>
                    <li><a href="feature.php"><i class="fas fa-star"></i> Features</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="auth/login.php" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <a href="auth/register.php" class="btn-primary"><i class="fas fa-user-plus"></i> Register</a>
                    </ul>
                </div>
                <div class="footer-social">
                    <h4>Follow Us</h4>
                    <div class="social-icons">
                         <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="https://www.youtube.com/channel/UC2y1IGj-6NP-uqYZbR34biw"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 CodeExam. All rights reserved. Designed for Ethiopia Software Engineering Students.</p>
            </div>
        </div>
    </footer>

</body>
</html>