<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Online Exam System</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="contact.css">
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
            <h1><i class="fas fa-headset"></i> Contact Us</h1>
            <p>You have a questions? Reach out to our team for support!</p>
        </section>

        <section class="contact-container">
            <div class="contact-info">
                <div class="info-card">
                    <i class="fas fa-map-marker-alt icon" style="color: #ff6b6b;"></i>
                    <h3>Address</h3>
                    <p>Ethiopia, Oromia, East Wollega , Nekemte city:</p>
                </div>

                <div class="info-card">
                    <i class="fas fa-phone-alt icon" style="color: #48dbfb;"></i>
                    <h3>Phone</h3>
                    <p>+251 955076338</p>
                </div>

                <div class="info-card">
                    <i class="fas fa-envelope icon" style="color: #1dd1a1;"></i>
                    <h3>Email</h3>
                    <p>oroafrilove@gmail.com</p>
                </div>
            </div>

            <div class="contact-form">
                <h2><i class="fas fa-paper-plane"></i> Send Us a Message</h2>
                <form action="submit_contact.php" method="POST">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Your Name" required>
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Your Email" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="Your Message" required></textarea>
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
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