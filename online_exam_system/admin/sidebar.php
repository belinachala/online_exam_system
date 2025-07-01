<?php
// Verify admin role - this should be included in files that include the sidebar
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     header("Location: ../auth/login.php");
//     exit();
// }
?>

<!-- Sidebar Navigation -->
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <!-- Dashboard Branding -->
        <div class="sidebar-header text-center py-4">
            <h3 class="text-white">
                <i class="fas fa-graduation-cap"></i>
                <span class="ms-2">Exam System</span>
            </h3>
        </div>

        <!-- Admin Profile -->
        <div class="user-panel d-flex align-items-center px-3 py-3 mb-3">
            <div class="image">
                <img src="images/admin.png" class="img-circle elevation-2" alt="Admin Image" width="40">
            </div>
            <div class="info ms-2">
                <a href="profile.php" class="d-block text-white">
                    <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>
                    <small class="d-block text-muted">Administrator</small>
                </a>
            </div>
        </div>

        <!-- Main Navigation -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>

            <!-- User Management -->
            <li class="nav-item">
                <a class="nav-link text-white collapsed" data-bs-toggle="collapse" href="#userManagement" aria-expanded="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['manage_users.php', 'approve_users.php', 'user_reports.php']) ? 'true' : 'false'; ?>">
                    <i class="fas fa-users me-2"></i>
                    User Management
                    <i class="fas fa-angle-down float-end mt-1"></i>
                </a>
                <div class="collapse <?php echo in_array(basename($_SERVER['PHP_SELF']), ['manage_users.php', 'approve_users.php', 'user_reports.php']) ? 'show' : ''; ?>" id="userManagement">
                    <ul class="nav flex-column ms-4">
                        <li class="nav-item">
                            <a href="manage_users.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'manage_users.php' ? 'active' : ''; ?>">
                                <i class="fas fa-list me-2"></i>
                                All Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="approve_users.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'approve_users.php' ? 'active' : ''; ?>">
                                <i class="fas fa-user-check me-2"></i>
                                Approve Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="user_reports.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'user_reports.php' ? 'active' : ''; ?>">
                                <i class="fas fa-chart-bar me-2"></i>
                                User Reports
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Content Management -->
            <li class="nav-item">
                <a class="nav-link text-white collapsed" data-bs-toggle="collapse" href="#contentManagement" aria-expanded="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['review_content.php', 'manage_exams.php', 'question_bank.php']) ? 'true' : 'false'; ?>">
                    <i class="fas fa-book me-2"></i>
                    Content Management
                    <i class="fas fa-angle-down float-end mt-1"></i>
                </a>
                <div class="collapse <?php echo in_array(basename($_SERVER['PHP_SELF']), ['review_content.php', 'manage_exams.php', 'question_bank.php']) ? 'show' : ''; ?>" id="contentManagement">
                    <ul class="nav flex-column ms-4">
                        <li class="nav-item">
                            <a href="review_content.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'review_content.php' ? 'active' : ''; ?>">
                                <i class="fas fa-check-circle me-2"></i>
                                Review Content
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="manage_exams.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'manage_exams.php' ? 'active' : ''; ?>">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Manage Exams
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="question_bank.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'question_bank.php' ? 'active' : ''; ?>">
                                <i class="fas fa-question-circle me-2"></i>
                                Question Bank
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Results & Reports -->
            <li class="nav-item">
                <a href="exam_results.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'exam_results.php' ? 'active' : ''; ?>">
                    <i class="fas fa-poll me-2"></i>
                    Exam Results
                </a>
            </li>

            <!-- System Configuration -->
            <li class="nav-item">
                <a class="nav-link text-white collapsed" data-bs-toggle="collapse" href="#systemConfig" aria-expanded="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['system_settings.php', 'manage_subjects.php', 'backup_restore.php']) ? 'true' : 'false'; ?>">
                    <i class="fas fa-cog me-2"></i>
                    System Configuration
                    <i class="fas fa-angle-down float-end mt-1"></i>
                </a>
                <div class="collapse <?php echo in_array(basename($_SERVER['PHP_SELF']), ['system_settings.php', 'manage_subjects.php', 'backup_restore.php']) ? 'show' : ''; ?>" id="systemConfig">
                    <ul class="nav flex-column ms-4">
                        <li class="nav-item">
                            <a href="system_settings.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'system_settings.php' ? 'active' : ''; ?>">
                                <i class="fas fa-sliders-h me-2"></i>
                                System Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="manage_subjects.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'manage_subjects.php' ? 'active' : ''; ?>">
                                <i class="fas fa-tags me-2"></i>
                                Manage Subjects
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="backup_restore.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'backup_restore.php' ? 'active' : ''; ?>">
                                <i class="fas fa-database me-2"></i>
                                Backup & Restore
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Help & Support -->
            <li class="nav-item">
                <a href="help_support.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'help_support.php' ? 'active' : ''; ?>">
                    <i class="fas fa-question-circle me-2"></i>
                    Help & Support
                </a>
            </li>
        </ul>

        <!-- Bottom Section -->
        <div class="sidebar-footer mt-auto p-3">
            <div class="text-center text-muted small">
                <div class="mb-2">
                    <i class="fas fa-circle text-success me-1"></i>
                    <span>Online</span>
                </div>
                <div>v1.0.0</div>
            </div>
        </div>
    </div>
</nav>

<!-- Toggle Button for Mobile -->
<button class="navbar-toggler position-fixed d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar" aria-controls="sidebar" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
</button>
 

<!-- Sidebar JavaScript -->
<script>
    // Activate the current menu item
    document.addEventListener('DOMContentLoaded', function() {
        // Highlight active menu item
        const currentPage = '<?php echo basename($_SERVER['PHP_SELF']); ?>';
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
                
                // Expand parent collapse if exists
                const parentCollapse = link.closest('.collapse');
                if (parentCollapse) {
                    parentCollapse.classList.add('show');
                    const collapseTrigger = parentCollapse.previousElementSibling;
                    if (collapseTrigger) {
                        collapseTrigger.classList.remove('collapsed');
                        collapseTrigger.setAttribute('aria-expanded', 'true');
                    }
                }
            }
        });
        
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>