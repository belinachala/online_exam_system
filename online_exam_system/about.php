<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About | CodeExam - Online Exam System</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Roboto+Mono:wght@400;700&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Glide.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.5.0/css/glide.core.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="about.css">
</head>
<body>
    <!-- Animated Background Elements -->
    <div class="bg-elements">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
        <div class="square square-1"></div>
        <div class="square square-2"></div>
    </div>

    <!-- Header -->
    <header class="animate__animated animate__fadeInDown">
        <div class="container">
            <div class="logo">
                <i class="fas fa-laptop-code"></i>
                <span>CodeExam</span>
            </div>
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
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content animate__animated animate__fadeInLeft">
                <h1>Revolutionizing <span>Ethiopia Software Engineering</span> Education</h1>
                <p>CodeExam is the next-generation online assessment platform designed specifically for software engineering students.</p>
                <div class="hero-stats">
                    <div class="stat">
                        <i class="fas fa-users"></i>
                        <div>
                            <span class="count" data-target="12500">0</span>+
                            <small>Students</small>
                        </div>
                    </div>
                    <div class="stat">
                        <i class="fas fa-code"></i>
                        <div>
                            <span class="count" data-target="8">0</span>+
                            <small>Programming Languages</small>
                        </div>
                    </div>
                    <div class="stat">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <span class="count" data-target="98">0</span>%
                            <small>Success Rate</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero-image animate__animated animate__fadeInRight">
                <img src="https://cdn-icons-png.flaticon.com/512/4727/4727496.png" alt="Coding Exam Illustration" class="main-image">
                <img src="https://cdn-icons-png.flaticon.com/512/2103/2103633.png" alt="Floating Code Icon" class="float-icon icon-1">
                <img src="https://cdn-icons-png.flaticon.com/512/888/888954.png" alt="Floating File Icon" class="float-icon icon-2">
                <img src="https://cdn-icons-png.flaticon.com/512/1055/1055687.png" alt="Floating Server Icon" class="float-icon icon-3">
            </div>
        </div>
        <div class="wave"></div>
    </section>

    <!-- About System -->
    <section class="about-system">
        <div class="container">
            <h2 class="section-title animate__animated animate__fadeIn">
                <i class="fas fa-cogs"></i> About The System
            </h2>
            
            <div class="system-grid">
                <div class="system-card animate__animated animate__fadeInUp">
                    <div class="card-icon" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3>Built for Developers</h3>
                    <p>Specifically designed for Ethiopia software engineering students education with real code execution, algorithm testing, and automated grading.</p>
                </div>
                
                <div class="system-card animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="card-icon" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure & Reliable</h3>
                    <p>Military-grade encryption, anti-cheating measures, and 99.9% uptime ensure your exams are secure and always available.</p>
                </div>
                
                <div class="system-card animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="card-icon" style="background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Advanced Analytics</h3>
                    <p>Detailed performance reports with code quality metrics, runtime analysis, and personalized feedback.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tech Stack -->
    <section class="tech-stack">
        <div class="container">
            <h2 class="section-title animate__animated animate__fadeIn">
                <i class="fas fa-layer-group"></i> Our Tech Stack
            </h2>
            
            <div class="glide">
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        <li class="glide__slide">
                            <div class="tech-card">
                                <img src="https://cdn-icons-png.flaticon.com/512/226/226777.png" alt="Java">
                                <span>Java</span>
                            </div>
                        </li>
                        <li class="glide__slide">
                            <div class="tech-card">
                                <img src="https://cdn-icons-png.flaticon.com/512/6132/6132222.png" alt="C++">
                                <span>C++</span>
                            </div>
                        </li>
                        <li class="glide__slide">
                            <div class="tech-card">
                                <img src="https://cdn-icons-png.flaticon.com/512/5968/5968350.png" alt="Python">
                                <span>Python</span>
                            </div>
                        </li>
                        <li class="glide__slide">
                            <div class="tech-card">
                                <img src="https://cdn-icons-png.flaticon.com/512/6132/6132221.png" alt="C">
                                <span>C</span>
                            </div>
                        </li>
                        <li class="glide__slide">
                            <div class="tech-card">
                                <img src="https://cdn-icons-png.flaticon.com/512/5968/5968292.png" alt="JavaScript">
                                <span>JavaScript</span>
                            </div>
                        </li>
                        <li class="glide__slide">
                            <div class="tech-card">
                                <img src="https://cdn-icons-png.flaticon.com/512/6132/6132220.png" alt="SQL">
                                <span>SQL</span>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="glide__arrows" data-glide-el="controls">
                    <button class="glide__arrow glide__arrow--left" data-glide-dir="<"><i class="fas fa-chevron-left"></i></button>
                    <button class="glide__arrow glide__arrow--right" data-glide-dir=">"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Highlight -->
    <section class="features-highlight">
        <div class="container">
            <h2 class="section-title animate__animated animate__fadeIn">
                <i class="fas fa-star"></i> Why Choose CodeExam?
            </h2>
            
            <div class="features-grid">
                <div class="feature animate__animated animate__fadeInLeft">
                    <div class="feature-number">01</div>
                    <div class="feature-content">
                        <h3>Real-time Code Execution</h3>
                        <p>Execute and test code directly in the browser with our advanced compiler integration.</p>
                        <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="Code Execution" class="feature-img">
                    </div>
                </div>
                
                <div class="feature animate__animated animate__fadeInRight">
                    <div class="feature-number">02</div>
                    <div class="feature-content">
                        <h3>Automated Grading</h3>
                        <p>Instant feedback on code submissions with detailed analysis of correctness and efficiency.</p>
                        <img src="https://cdn-icons-png.flaticon.com/512/3143/3143461.png" alt="Automated Grading" class="feature-img">
                    </div>
                </div>
                
                <div class="feature animate__animated animate__fadeInLeft">
                    <div class="feature-number">03</div>
                    <div class="feature-content">
                        <h3>Algorithm Visualization</h3>
                        <p>Step-through visualization of algorithms to enhance understanding of complex concepts.</p>
                        <img src="https://cdn-icons-png.flaticon.com/512/2103/2103633.png" alt="Algorithm Visualization" class="feature-img">
                    </div>
                </div>
                
                <div class="feature animate__animated animate__fadeInRight">
                    <div class="feature-number">04</div>
                    <div class="feature-content">
                        <h3>Secure Exam Environment</h3>
                        <p>Browser lockdown, screen monitoring, and plagiarism detection for exam integrity.</p>
                        <img src="https://cdn-icons-png.flaticon.com/512/3064/3064155.png" alt="Secure Exam" class="feature-img">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <div class="container">
            <h2 class="section-title animate__animated animate__fadeIn">
                <i class="fas fa-quote-left"></i> What Educators Say
            </h2>
            
            <div class="testimonial-slider">
                <div class="testimonial animate__animated animate__fadeIn">
                    <div class="testimonial-content">
                        <p>"CodeExam transformed how we assess programming skills. The automated grading alone saved us hundreds of hours."</p>
                        <div class="author">
                            <img src="images/11.jpeg" alt="Professor Smith">
                            <div>
                                <h4>Dr. Shashitu</h4>
                                <span>Computer Science Professor</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial animate__animated animate__fadeIn animate__delay-1s">
                    <div class="testimonial-content">
                        <p>"Our students' engagement with programming assignments increased by 70% after switching to CodeExam."</p>
                        <div class="author">
                            <img src="images/1.jpeg" alt="Professor Johnson">
                            <div>
                                <h4>Prof. Mohamed Abdu</h4>
                                <span>Software Engineering </span>
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
            <h2>Ready to Revolutionize Your Programming Exams?</h2>
            <p>Join 500+ universities and coding bootcamps using CodeExam</p>
            <div class="cta-buttons">
                <a href="auth/register.php" class="btn-primary"><i class="fas fa-rocket"></i> Get Started Now</a>
                <a href="contact.php" class="btn-secondary"><i class="fas fa-envelope"></i> Contact Sales</a>
            </div>
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

    <!-- Glide.js Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.5.0/glide.min.js"></script>
    <!-- Custom Script -->
    <script>
        // Counter Animation
        const counters = document.querySelectorAll('.count');
        const speed = 200;
        
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const increment = target / speed;
            
            if (count < target) {
                counter.innerText = Math.ceil(count + increment);
                setTimeout(updateCount, 1);
            } else {
                counter.innerText = target;
            }
            
            function updateCount() {
                const count = +counter.innerText;
                if (count < target) {
                    counter.innerText = Math.ceil(count + increment);
                    setTimeout(updateCount, 1);
                } else {
                    counter.innerText = target + '+';
                }
            }
        });
        
        // Initialize Glide.js
        new Glide('.glide', {
            type: 'carousel',
            perView: 5,
            focusAt: 'center',
            breakpoints: {
                1200: {
                    perView: 4
                },
                992: {
                    perView: 3
                },
                768: {
                    perView: 2
                },
                576: {
                    perView: 1
                }
            }
        }).mount();
        
        // Floating icons animation
        const floatIcons = document.querySelectorAll('.float-icon');
        floatIcons.forEach((icon, index) => {
            icon.style.animation = `float ${6 + index}s ease-in-out infinite`;
        });
    </script>
</body>
</html>