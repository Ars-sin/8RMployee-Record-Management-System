<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - 8RMployee</title>
  <style>
    /* Basic Reset and Font */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
    }

    body {
      /* Removed properties that center the container */
    }

    .container {
      display: flex;
      width: 100%; /* Changed from 900px */
      height: 100vh; /* Changed from 550px */
      /* Removed box-shadow, border-radius, and overflow */
    }

    .left {
      flex: 1;
      background-image:
        linear-gradient(to right, rgba(94, 133, 185, 0.6), rgba(225, 229, 233, 0.8)),
        url('logo.4.png');
      background-size: cover;
      background-position: center;
    }

    .right {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px;
      background-image: linear-gradient(to right, rgb(241, 240, 240) , rgb(211, 211, 208));
      position: relative;
    }

    .logo-graphic {
      width: 100px;
      margin-bottom: 15px;
    }

    .logo-text {
      font-size: 36px;
      font-weight: bold;
      margin-bottom: 5px;
    }

    .logo-text .green { color: #28a745; }
    .logo-text .blue { color: #1e3a8a; }
    .logo-text .yellow { color: #facc15; }

    .description {
      color: #333;
      margin-bottom: 40px;
      font-size: 18px;
    }

    .login-form {
      width: 100%;
      max-width: 320px;
      text-align: center; /* Center form contents */
    }

    .password-wrapper {
      position: relative;
      width: 100%;
      margin-bottom: 10px;
    }

    input[type="password"],
    input[type="text"] { /* Corrected: Removed .password-visible class from here */
      width: 100%;
      padding: 15px;
      border: 1px solid #ddd;
      border-radius: 30px;
      background: #fef9c3;
      font-size: 16px;
      text-align: center;
      padding-right: 45px; /* Make space for the eye icon */
    }

    input[type="password"]::placeholder,
    input[type="text"]::placeholder { /* Corrected: Applied placeholder style to both types */
      color: #9ca3af;
      font-weight: normal;
    }

    .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #9ca3af;
    }

    /* New style for the validation message */
    #validation-message {
      color: #dc2626; /* A strong red color */
      font-size: 12px;
      height: 15px; /* Reserve space to prevent layout shift */
      margin-bottom: 10px;
    }

    button {
      width: 100%;
      padding: 15px;
      background-color: #1e40af;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 30px;
      font-size: 16px;
      cursor: pointer;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: background-color 0.3s, opacity 0.3s;
    }

    button:hover:not(:disabled) {
      background-color: #1e3a8a;
    }

    button:disabled {
      background-color: #9ca3af;
      cursor: not-allowed;
      opacity: 0.7;
    }

    .footer {
      position: absolute;
      bottom: 20px;
      font-size: 12px;
      color: #6b7280;
    }

    /* --- Loading Screen Styles --- */
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
    }

    /* --- New Progress Bar Styles --- */
    .progress-bar-container {
        width: 300px;
        height: 20px;
        background-color: #f0f0f0;
        border-radius: 10px;
        overflow: hidden;
        /* Replicating the subtle 3D effect from the image */
        background: linear-gradient(to bottom, #ffffff, #e0e0e0);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.15);
    }

    .progress-bar-fill {
        width: 0%; /* Start at 0% width for the animation */
        height: 100%;
        border-radius: 10px;
        background: linear-gradient(to right, #04c42e, #dcf741);
        /* Animation applied here - now animates width */
        animation: loading-fill 2.5s ease-in-out forwards; /* This will be triggered/reset by JS */
    }

    /* Keyframe animation for the progress bar to fill from 0% to 100% */
    @keyframes loading-fill {
        0% {
            width: 0%; /* Start at 0% fill */
        }
        100% {
            width: 100%; /* End at 100% fill */
        }
    }
  </style>
</head>
<body>

  <!-- Loading Screen HTML -->
  <div id="loading-screen" class="loader">
    <img src="logo.png.png" alt="Loading..." class="loader-logo">
    <!-- The pulsating dot is replaced with the progress bar -->
    <div class="progress-bar-container">
        <div class="progress-bar-fill"></div>
    </div>
  </div>

  <!--<div class="container">-->
  <!--  <div class="left"></div>-->
  <!--  <div class="right">-->
  <!--    <img src="logo.png.png" alt="8RMployee Logo" class="logo-graphic">-->
  <!--    <div class="logo-text">-->
  <!--      <span class="green">8</span><span class="blue">RM</span><span class="yellow">ployee</span>-->
  <!--    </div>-->
  <!--    <div class="description">Record Management System</div>-->

  <!--    <div class="login-form">-->
  <!--      <div class="password-wrapper">-->
  <!--        <input type="password" id="adminPinInput" placeholder="Admin PIN">-->
  <!--        <span class="toggle-password" id="togglePassword">-->
  <!--          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>-->
  <!--        </span>-->
  <!--      </div>-->
  <!--      <p id="validation-message"></p>-->
  <!--      <button id="loginButton" disabled>Log In</button>-->
  <!--    </div>-->

  <!--    <div class="footer">8RM Utility Projects Construction</div>-->
  <!--  </div>-->
  <!--</div>-->

  <script>
    // const adminPinInput = document.getElementById('adminPinInput');
    // const loginButton = document.getElementById('loginButton');
    // const validationMessage = document.getElementById('validation-message');
    // const loadingScreen = document.getElementById('loading-screen');
    // const progressBarFill = document.querySelector('.progress-bar-fill');
    // const togglePassword = document.getElementById('togglePassword');

    // adminPinInput.addEventListener('input', function() {
    //   const pinValue = adminPinInput.value;
    //   const hasLetter = /[a-zA-Z]/.test(pinValue);
    //   const hasNumber = /\d/.test(pinValue);

    //   if (hasLetter && hasNumber) {
    //     loginButton.disabled = false;
    //     validationMessage.textContent = '';
    //   } else {
    //     loginButton.disabled = true;
    //     if (pinValue.length > 0) {
    //         validationMessage.textContent = 'PIN must contain letters and numbers.';
    //     } else {
    //         validationMessage.textContent = '';
    //     }
    //   }
    // });

    // togglePassword.addEventListener('click', function() {
    //   // Toggle the type attribute
    //   const type = adminPinInput.getAttribute('type') === 'password' ? 'text' : 'password';
    //   adminPinInput.setAttribute('type', type);

    //   // Toggle the eye icon (you might want to switch between eye-open and eye-slash SVGs)
    //   if (type === 'password') {
    //     togglePassword.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
    //   } else {
    //     togglePassword.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye-off"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
    //   }
    // });

    // loginButton.addEventListener('click', function() {
    //   loadingScreen.classList.add('visible');

    //   // Reset and restart the progress bar animation
    //   progressBarFill.style.animation = 'none';
    //   void progressBarFill.offsetWidth;
    //   progressBarFill.style.animation = 'loading-fill 2.5s ease-in-out forwards';

      setTimeout(function() {
          window.location.href = 'overview.php';
      }, 2500);
    });
  </script>

</body>
</html>