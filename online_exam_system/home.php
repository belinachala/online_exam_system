<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam System | Software Engineering</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Custom CSS -->
 
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <!-- Header with Navigation -->
    <header class="animate__animated animate__fadeInDown">
        <div class="container">
            <div class="logo">
                <i class="fas fa-laptop-code"></i>
                <span>CodeExam</span>
            </div>
            <nav>
                <ul>
                    <li><a href="home.php" class="active"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="about.php"><i class="fas fa-info-circle"></i> About</a></li>
                    <li><a href="feature.php"><i class="fas fa-star"></i> Features</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="auth/login.php" class="btn-login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <a href="auth/register.php" class="btn-primary"><i class="fas fa-user-plus"></i> Register</a>
                    
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero animate__animated animate__fadeIn">
        <div class="container">
            <div class="hero-content">
                <h1 class="animate__animated animate__fadeInLeft">Master Your <span class="highlight">Software Engineering</span> Exams</h1>
                <p class="animate__animated animate__fadeInLeft animate__delay-1s">The ultimate platform for SE students to practice, test, and excel in programming exams.</p>
                <div class="hero-buttons animate__animated animate__fadeInUp animate__delay-1s">
                    <a href="auth/register.php" class="btn-primary"><i class="fas fa-user-plus"></i> Get Started</a>
                    <a href="about.php" class="btn-secondary"><i class="fas fa-play-circle"></i> How It Works</a>
                </div>
            </div>
            <div class="hero-image animate__animated animate__fadeInRight animate__delay-1s">
                <img src="https://cdn-icons-png.flaticon.com/512/3271/3271342.png" alt="Programming Exam Illustration">
            </div>
        </div>
        <div class="wave"></div>
    </section>

    <!-- Features Preview -->
    <section id="features" class="features">
        <div class="container">
            <h2 class="section-title animate__animated animate__fadeIn">Why Choose <span>CodeExam</span>?</h2>
            <div class="features-grid">
                <!-- Feature 1 -->
                <div class="feature-card animate__animated animate__fadeInUp">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3>Real Coding Challenges</h3>
                    <p>Practice with actual programming problems in multiple languages with live code execution.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3>Algorithm Testing</h3>
                    <p>Test your algorithms against various test cases with instant feedback.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);">
                        <i class="fas fa-stopwatch"></i>
                    </div>
                    <h3>Timed Exams</h3>
                    <p>Simulate real exam conditions with our timed testing environment.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Exam Categories -->
    <section class="categories">
        <div class="container">
            <h2 class="section-title animate__animated animate__fadeIn">Explore <span>Exam Categories</span></h2>
            <div class="categories-grid">
                <!-- Category 1 -->
                <div class="category-card animate__animated animate__flipInX">
                    <div class="category-icon" style="background: linear-gradient(135deg, #8e2de2 0%, #4a00e0 100%);">
                        <i class="fab fa-java"></i>
                    </div>
                    <h3>Java Programming</h3>
                    <p>Master OOP concepts, collections, and Java frameworks.</p>
                    <a href="#" class="btn-category">Start Practice <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <!-- Category 2 -->
                <div class="category-card animate__animated animate__flipInX animate__delay-1s">
                    <div class="category-icon" style="background: linear-gradient(135deg, #00b4db 0%, #0083b0 100%);">
                        <i class="fab fa-python"></i>
                    </div>
                    <h3>Python</h3>
                    <p>Data structures, algorithms, and Pythonic patterns.</p>
                    <a href="#" class="btn-category">Start Practice <i class="fas fa-arrow-right"></i></a>
                </div>
                
                <!-- Category 3 -->
                <div class="category-card animate__animated animate__flipInX animate__delay-2s">
                    <div class="category-icon" style="background: linear-gradient(135deg, #f46b45 0%, #eea849 100%);">
                        <i class="fas fa-database"></i>
                    </div>
                    <h3>Database Systems</h3>
                    <p>SQL queries, normalization, and transaction management.</p>
                    <a href="#" class="btn-category">Start Practice <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>
<!-- Testimonials -->
    <section class="testimonials">
        <div class="container">
            <h2 class="section-title animate__animated animate__fadeIn">
                <i class="fas fa-quote-left"></i> What Students Say
            </h2>
            
            <div class="testimonial-slider">
                <div class="testimonial animate__animated animate__fadeIn">
                    <div class="testimonial-content">
                        <p>"CodeExam helped me ace my Data Structures final! The realistic exam simulations were invaluable."</p>
                        <div class="author">
                            
                            <img src="images/1111.jpeg"  alt="Student">
                            <div>
                                <h4>Sahara Alemu</h4>
                                <span>Computer Science Senior</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial animate__animated animate__fadeIn animate__delay-1s">
                    <div class="testimonial-content">
                         <p>"The algorithm challenges prepared me perfectly for technical interviews. Landed my dream job at Google!"</p>                        <div class="author">
                            <img src="images/111.jpeg"  alt="Student">
                             <div>
                                <h4>Abenezer Bamlak</h4>
                                <span>Software Engineer</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
 
    <!-- CTA Section -->
    <section class="cta animate__animated animate__fadeIn">
        <div class="container">
            <h2>Ready to Boost Your <span>Coding Skills</span>?</h2>
            <p>Join thousands of software engineering students who are acing their exams with CodeExam</p>
            <a href="auth/register.php" class="btn-primary btn-large"><i class="fas fa-rocket"></i> Start Free Trial</a>
        </div>
    </section>

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
                    <a href="auth/register.php" class="btn-primary"><i class="fas fa-user-plus"></i> Get Started</a>
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

    <!-- Script for animations on scroll -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html>