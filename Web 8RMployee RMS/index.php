<?php
session_start();

// SECURITY: Prevent browser caching to stop users from going back to the login page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// If user is already logged in, redirect to overview
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

// Check for logout or session messages
$message = '';
if (isset($_GET['logout'])) {
    $message = 'You have been logged out successfully.';
}
if (isset($_GET['expired'])) {
    $message = 'Your session has expired. Please log in again.';
}
if (isset($_GET['security'])) {
    $message = 'Security alert: Please log in again.';
}
?>








<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/logo.png.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>8RMployee - Login</title>
    <style>
        /* Basic Reset and Font */
        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        }
        
        body {
            overflow: hidden;
            height: 100vh;
            background: #0f172a;
            position: relative;
        }
        
        /* Animated geometric background */
        .bg-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(1px);
            animation: float 20s infinite linear;
        }
        
        .shape-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.3), rgba(30, 64, 175, 0.3));
            top: -150px;
            left: -150px;
            animation-duration: 25s;
        }
        
        .shape-2 {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.3), rgba(250, 204, 21, 0.3));
            top: 20%;
            right: -100px;
            animation-duration: 30s;
            animation-direction: reverse;
        }
        
        .shape-3 {
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg, rgba(250, 204, 21, 0.2), rgba(40, 167, 69, 0.2));
            bottom: -125px;
            left: 30%;
            animation-duration: 35s;
        }
        
        .shape-4 {
            width: 180px;
            height: 180px;
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.3), rgba(40, 167, 69, 0.3));
            top: 60%;
            right: 20%;
            animation-duration: 28s;
            animation-direction: reverse;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(100px, -50px) rotate(120deg); }
            66% { transform: translate(-50px, 100px) rotate(240deg); }
            100% { transform: translate(0, 0) rotate(360deg); }
        }
        
        /* Grid pattern overlay */
        .grid-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: 1;
        }
        
        .container { 
            display: flex; 
            width: 100%; 
            height: 100vh; 
            position: relative;
            z-index: 2;
        }
        
        .left { 
            flex: 1.2; 
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(10px);
        }
        
        .left-content {
            text-align: center;
            color: white;
            padding: 60px 40px;
            max-width: 500px;
        }
        
        .welcome-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #28a745, #1e40af, #facc15);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% 200%;
            animation: gradientText 4s ease infinite;
            line-height: 1.1;
        }
        
        @keyframes gradientText {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .welcome-subtitle {
            font-size: 1.3rem;
            opacity: 0.8;
            margin-bottom: 2rem;
            color: #cbd5e1;
            font-weight: 300;
        }
        
        .feature-list {
            text-align: left;
            margin-top: 3rem;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            color: #cbd5e1;
            font-size: 1rem;
        }
        
        .feature-icon {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            background: linear-gradient(135deg, #28a745, #1e40af);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            font-weight: bold;
        }
        
        .right { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            padding: 60px 40px; 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            position: relative;
            box-shadow: -20px 0 40px rgba(0, 0, 0, 0.1);
        }
        
        .login-section {
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        
        .login-title { 
            font-size: 48px; 
            font-weight: 900; 
            margin-bottom: 8px;
            letter-spacing: -2px;
        }
        
        .login-title .green { 
            color: #28a745; 
            text-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }
        
        .login-title .blue { 
            color: #1e40af;
            text-shadow: 0 4px 8px rgba(30, 64, 175, 0.3);
        }
        
        .login-title .yellow { 
            color: #facc15;
            text-shadow: 0 4px 8px rgba(250, 204, 21, 0.3);
        }
        
        .logo-text { 
            font-size: 48px; 
            font-weight: 900; 
            margin-bottom: 8px;
            letter-spacing: -2px;
        }
        
        .logo-text .green { 
            color: #28a745; 
            text-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }
        
        .logo-text .blue { 
            color: #1e40af;
            text-shadow: 0 4px 8px rgba(30, 64, 175, 0.3);
        }
        
        .logo-text .yellow { 
            color: #facc15;
            text-shadow: 0 4px 8px rgba(250, 204, 21, 0.3);
        }
        
        .description { 
            color: #64748b; 
            margin-bottom: 50px; 
            font-size: 16px;
            font-weight: 500;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .login-form { 
            width: 100%;
            margin-bottom: 30px;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 30px;
        }
        
        .form-label {
            position: absolute;
            top: 50%;
            left: 28px;
            background: transparent;
            padding: 0;
            font-size: 17px;
            color: #94a3b8;
            font-weight: 400;
            opacity: 1;
            transform: translateY(-50%);
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            pointer-events: none;
            z-index: 10;
            white-space: nowrap;
            font-style: italic;
            letter-spacing: 0.5px;
        }
        
        .form-group.field-active .form-label,
        .form-group.field-filled .form-label {
            top: -12px;
            left: 28px;
            font-size: 12px;
            color: #1e40af;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.95);
            padding: 4px 12px;
            border-radius: 8px;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(30, 64, 175, 0.15);
            font-style: normal;
            transform: translateY(0);
        }
        
        input[type="password"],
        input[type="text"]#adminPinInput {
            width: 100%;
            padding: 22px 60px 22px 28px;
            border: 2px solid rgba(226, 232, 240, 0.6);
            border-radius: 20px;
            background: #ffffff;
            font-size: 17px;
            text-align: left;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        
        input[type="password"]:focus,
        input[type="text"]#adminPinInput:focus {
            outline: none;
            border-color: #1e40af;
            box-shadow: 0 0 0 6px rgba(30, 64, 175, 0.15);
            transform: translateY(-3px);
            background: #ffffff;
        }
        
        input::placeholder { 
            color: #94a3b8; 
            font-weight: 400; 
            text-align: center;
            font-style: italic;
            letter-spacing: 0.5px;
        }
        
        .toggle-pin {
            position: absolute;
            top: 50%;
            right: 24px;
            transform: translateY(-50%);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: rgba(30, 64, 175, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .toggle-pin:hover {
            background: rgba(30, 64, 175, 0.2);
            transform: translateY(-50%) scale(1.1);
        }
        
        .toggle-pin svg {
            width: 22px;
            height: 22px;
            stroke: #1e40af;
            transition: stroke 0.2s;
        }
        
        .toggle-pin:hover svg {
            stroke: #1e3a8a;
        }
        
        .toggle-pin .eye-slash-icon {
            display: none;
        }
        
        .toggle-pin.visible .eye-slash-icon {
            display: block;
        }
        
        .toggle-pin.visible .eye-icon {
            display: none;
        }
        
        #validation-message {
            color: #dc2626;
            font-size: 14px;
            height: 20px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .session-message {
            color: #059669;
            font-size: 14px;
            margin-bottom: 25px;
            font-weight: 500;
            padding: 16px 20px;
            border-radius: 16px;
            background: rgba(16, 185, 129, 0.1);
            border: 2px solid rgba(16, 185, 129, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .session-message.warning {
            color: #dc2626;
            background: rgba(220, 38, 38, 0.1);
            border: 2px solid rgba(220, 38, 38, 0.2);
        }
        
        button { 
            width: 100%; 
            padding: 22px; 
            background: linear-gradient(135deg, #1e40af, #1e3a8a, #28a745);
            background-size: 200% 200%;
            color: white; 
            font-weight: 800; 
            border: none; 
            border-radius: 20px; 
            font-size: 17px; 
            cursor: pointer; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: buttonGradient 3s ease infinite;
        }
        
        @keyframes buttonGradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.6s;
        }
        
        button:hover::before {
            left: 100%;
        }
        
        button:hover { 
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(30, 64, 175, 0.4);
        }
        
        button:active {
            transform: translateY(-2px);
        }
        
        .footer { 
            font-size: 13px; 
            color: #64748b;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        /* Decorative elements */
        .decorative-line {
            width: 60px;
            height: 4px;
            background: linear-gradient(135deg, #28a745, #1e40af, #facc15);
            border-radius: 2px;
            margin: 20px auto;
            animation: pulse 2s ease infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.7; transform: scaleX(1); }
            50% { opacity: 1; transform: scaleX(1.2); }
        }
        
        /* --- Loading Screen Styles (Unchanged) --- */
        .loader { 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background-color: #ffffff; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            z-index: 9999; 
            opacity: 0; 
            pointer-events: none; 
            transition: opacity 0.5s ease-in-out; 
        }
        
        .loader.visible { 
            opacity: 1; 
            pointer-events: all; 
        }
        
        .loader-logo { 
            width: 150px; 
            margin-bottom: 30px; 
            animation: logo-pulse 2s ease-in-out infinite; 
        }
        
        @keyframes logo-pulse { 
            0%, 100% { transform: scale(1); opacity: 1; } 
            50% { transform: scale(1.05); opacity: 0.8; } 
        }
        
        .progress-bar-container { 
            width: 300px; 
            height: 20px; 
            background-color: #f0f0f0; 
            border-radius: 10px; 
            overflow: hidden; 
            background: linear-gradient(to bottom, #ffffff, #e0e0e0); 
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.15); 
            margin-bottom: 20px; 
        }
        
        .progress-bar-fill { 
            width: 0%; 
            height: 100%; 
            border-radius: 10px; 
            background: linear-gradient(to right, #04c42e, #dcf741); 
            animation: loading-fill 2.5s ease-in-out forwards; 
        }
        
        @keyframes loading-fill { 
            0% { width: 0%; } 
            100% { width: 100%; } 
        }
        
        .loading-text { 
            color: #1e40af; 
            font-size: 16px; 
            font-weight: bold; 
            margin-top: 10px; 
            animation: text-fade 1.5s ease-in-out infinite alternate; 
        }
        
        @keyframes text-fade { 
            0% { opacity: 0.6; } 
            100% { opacity: 1; } 
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .left {
                flex: 0;
                height: 250px;
            }
            
            .left-content {
                padding: 30px 20px;
            }
            
            .welcome-title {
                font-size: 2.5rem;
            }
            
            .feature-list {
                display: none;
            }
            
            .right {
                flex: 1;
                padding: 30px 20px;
            }
            
            .logo-text {
                font-size: 36px;
            }
        }
        
        @media (max-width: 480px) {
            .welcome-title {
                font-size: 2rem;
            }
            
            .logo-text {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>
    </div>
    
    <!-- Grid Overlay -->
    <div class="grid-overlay"></div>
    
    <div id="loading-screen" class="loader">
        <img src="logo.png.png" alt="Loading..." class="loader-logo">
        <div class="progress-bar-container">
            <div class="progress-bar-fill"></div>
        </div>
        <div class="loading-text">Loading your dashboard...</div>
    </div>
    
    <div class="container">
        <div class="left">
            <div class="left-content">
                <img src="logo.png.png" alt="8RMployee Logo" style="width: 100px; margin-bottom: 20px; filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.1)); transition: all 0.3s ease;">
                <div class="logo-text">
                    <span class="green">8</span><span class="blue">RM</span><span class="yellow">ployee</span>
                </div>
                <p class="welcome-subtitle">Record Management System</p>
                <div class="decorative-line"></div>
                
                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <span>Secure Record Management</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <span>Track Remittances</span>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <span>Advanced Security</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="right">
            <div class="login-section">
                <div class="login-title">
                    <span class="blue">Login</span>
                </div>
                <div class="description">Access your secure management dashboard</div>
                
                <!-- PHP message placeholder -->
                <div class="session-message" style="display: none;">
                    Session message would appear here
                </div>
                
                <form id="loginForm" class="login-form">
                    <div class="form-group" id="pinFormGroup">
                        <label for="adminPinInput" class="form-label">Admin PIN</label>
                        <input type="password" id="adminPinInput" name="pin_pass" placeholder="" required>
                        <div id="togglePin" class="toggle-pin">
                            <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg class="eye-slash-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </div>
                    </div>
                    <p id="validation-message"></p>
                    <button type="submit" id="loginButton">Log In</button>
                </form>
                
                <div class="decorative-line"></div>
                <div class="footer">8RM Utility Projects Construction</div>
            </div>
        </div>
    </div>


<script>
const loginForm = document.getElementById('loginForm');
const adminPinInput = document.getElementById('adminPinInput');
const validationMessage = document.getElementById('validation-message');
const pinFormGroup = document.getElementById('pinFormGroup');
const loadingScreen = document.getElementById('loading-screen');
const togglePin = document.getElementById('togglePin');

// Listener for the form submission
loginForm.addEventListener('submit', function(event) {
  event.preventDefault();
  const formData = new FormData(loginForm);

  fetch('login_process.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Show loading screen and start animation on successful login
      loadingScreen.classList.add('visible');
      const progressFill = document.querySelector('.progress-bar-fill');
      // Reset animation to allow re-triggering
      progressFill.style.animation = 'none';
      progressFill.offsetHeight; // Trigger reflow
      progressFill.style.animation = 'loading-fill 2.5s ease-in-out forwards';
      // Redirect after animation completes
      setTimeout(function() {
        window.history.replaceState(null, null, 'dashboard.php');
        window.location.replace('dashboard.php');
      }, 2800);
    } else {
      // Display error message from server
      validationMessage.textContent = data.message || 'An unknown error occurred.';
    }
  })
  .catch(error => {
    console.error('Login Error:', error);
    validationMessage.textContent = 'A network error occurred. Please try again.';
  });
});

// Add class on input to keep label floated if there's text
adminPinInput.addEventListener('input', function() {
  if (validationMessage.textContent !== '') {
    validationMessage.textContent = '';
  }
  if (adminPinInput.value.length > 0) {
    pinFormGroup.classList.add('field-active');
  } else {
    pinFormGroup.classList.remove('field-active');
  }
});

// Add class on focus to float the label immediately
adminPinInput.addEventListener('focus', function() {
  pinFormGroup.classList.add('field-active');
});

// Remove class on blur ONLY if the input is empty
adminPinInput.addEventListener('blur', function() {
  if (adminPinInput.value.length === 0) {
    pinFormGroup.classList.remove('field-active');
  }
});

// Event listener for the password visibility toggle icon
togglePin.addEventListener('click', function() {
  // Toggle the input type attribute between 'password' and 'text'
  const type = adminPinInput.getAttribute('type') === 'password' ? 'text' : 'password';
  adminPinInput.setAttribute('type', type);

  // Toggle the '.visible' class on the icon container to switch icons
  this.classList.toggle('visible');
});
</script>

</body>
</html>