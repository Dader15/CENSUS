<?php
session_start();
session_unset();
// include centralized security headers for the public login page as well
if (file_exists('assets/php/security_headers.php')) {
    include_once 'assets/php/security_headers.php';
}
include_once 'connection/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>stry of Barangay Inhabitants and Migrants</title>
    <link href="assets/img/caloocan.png" rel="icon">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="loader-wrapper">
        <span class="loader"></span>
    </div>
    
    <!-- Modern Login Container -->
    <div class="login-container hidden">
        <div class="login-card">
            <!-- Header Section -->
            <div class="login-header">
                <div class="logo-wrapper">
                    <img src="assets/img/caloocan.png" alt="Caloocan Logo" class="logo-img">
                </div>
                <h1 class="login-title">Caloocan City Hall</h1>
                <p class="login-subtitle">Registry of Barangay Inhabitants and Migrants</p>
            </div>

            <!-- Form Section -->
            <form class="login-form">
                <!-- Username Input -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            id="usr" 
                            name="usr"
                            class="form-input" 
                            placeholder=" " 
                            required
                            autocomplete="username"
                        >
                        <label for="usr" class="form-label">
                            <i class="fas fa-user"></i> Username
                        </label>
                        <div class="input-underline"></div>
                    </div>
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="pss" 
                            name="pss"
                            class="form-input" 
                            placeholder=" " 
                            required
                            autocomplete="current-password"
                        >
                        <label for="pss" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="input-underline"></div>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div> -->

                <!-- Login Button -->
                <button type="submit" class="login-btn" id="submitLogin">
                    <span class="btn-text">Sign In</span>
                    <span class="btn-icon">→</span>
                </button>
            </form>

            <!-- Footer Info -->
            <div class="login-footer">
                <p class="security-note">
                    <i class="fas fa-shield-alt"></i> ITDO - Caloocan City Hall
                </p>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="decoration decoration-1"></div>
        <div class="decoration decoration-2"></div>
    </div>

<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/sweetalert/sweetalert.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script src="assets/js/ajax/ajax_login_logout.js"></script>
<script src="assets/js/function.js"></script>
<script>
    // Show login card after loader finishes
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            document.querySelector('.loader-wrapper').style.display = 'none';
            document.querySelector('.login-container').classList.remove('hidden');
        }, 1500);
    });
</script>
</body>
</html>
