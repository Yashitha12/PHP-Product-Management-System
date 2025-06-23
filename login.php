<?php
// login.php - Modern Professional Login Page

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();

  $result = $stmt->get_result();
  if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
      session_start();
      $_SESSION['user_id'] = $row['id'];
      $_SESSION['user_email'] = $row['email'];
      header("Location: welcome.php");
      exit();
    } else {
      $error_message = "Invalid password. Please try again.";
    }
  } else {
    $error_message = "No account found with this email address.";
  }

  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In - Your App</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary-color: #6366f1;
      --primary-hover: #5855eb;
      --secondary-color: #f8fafc;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --border-color: #e2e8f0;
      --error-color: #ef4444;
      --success-color: #10b981;
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      margin: 0;
      padding: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-wrapper {
      width: 100%;
      max-width: 440px;
      position: relative;
    }

    .login-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 24px;
      padding: 48px 40px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 
                  0 10px 10px -5px rgba(0, 0, 0, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
    }

    .login-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .brand-logo {
      width: 64px;
      height: 64px;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 24px;
      box-shadow: 0 8px 25px -8px var(--primary-color);
    }

    .brand-logo i {
      font-size: 28px;
      color: white;
    }

    .login-title {
      font-size: 28px;
      font-weight: 700;
      color: var(--text-primary);
      text-align: center;
      margin-bottom: 8px;
      letter-spacing: -0.025em;
    }

    .login-subtitle {
      color: var(--text-secondary);
      text-align: center;
      margin-bottom: 32px;
      font-size: 16px;
      line-height: 1.5;
    }

    .form-group {
      margin-bottom: 24px;
      position: relative;
    }

    .form-label {
      font-weight: 500;
      color: var(--text-primary);
      margin-bottom: 8px;
      font-size: 14px;
    }

    .form-control {
      height: 52px;
      border: 2px solid var(--border-color);
      border-radius: 12px;
      padding: 0 16px 0 48px;
      font-size: 16px;
      transition: all 0.2s ease;
      background-color: #ffffff;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
      outline: none;
    }

    .input-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-secondary);
      font-size: 18px;
      z-index: 2;
    }

    .password-toggle {
      position: absolute;
      right: 16px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--text-secondary);
      cursor: pointer;
      font-size: 18px;
      z-index: 2;
      padding: 4px;
      border-radius: 6px;
      transition: all 0.2s ease;
    }

    .password-toggle:hover {
      color: var(--primary-color);
      background-color: rgba(99, 102, 241, 0.1);
    }

    .form-check {
      margin-bottom: 32px;
    }

    .form-check-input {
      width: 18px;
      height: 18px;
      border: 2px solid var(--border-color);
      border-radius: 4px;
    }

    .form-check-input:checked {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .form-check-label {
      color: var(--text-secondary);
      font-size: 14px;
      margin-left: 8px;
    }

    .btn-login {
      width: 100%;
      height: 52px;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
      border: none;
      border-radius: 12px;
      color: white;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.2s ease;
      position: relative;
      overflow: hidden;
    }

    .btn-login:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .btn-login:disabled {
      opacity: 0.7;
      cursor: not-allowed;
      transform: none;
    }

    .divider {
      text-align: center;
      margin: 32px 0;
      position: relative;
      color: var(--text-secondary);
      font-size: 14px;
    }

    .divider::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 1px;
      background: var(--border-color);
    }

    .divider span {
      background: rgba(255, 255, 255, 0.95);
      padding: 0 16px;
    }

    .footer-links {
      text-align: center;
      margin-top: 32px;
    }

    .footer-links a {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 500;
      font-size: 14px;
      transition: all 0.2s ease;
    }

    .footer-links a:hover {
      color: var(--primary-hover);
      text-decoration: underline;
    }

    .footer-links p {
      color: var(--text-secondary);
      margin: 16px 0 0 0;
      font-size: 14px;
    }

    .alert {
      border-radius: 12px;
      border: none;
      padding: 16px;
      margin-bottom: 24px;
      font-size: 14px;
      font-weight: 500;
    }

    .alert-danger {
      background-color: rgba(239, 68, 68, 0.1);
      color: var(--error-color);
      border-left: 4px solid var(--error-color);
    }

    .loading-spinner {
      width: 20px;
      height: 20px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: white;
      animation: spin 1s ease-in-out infinite;
      margin-right: 8px;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .btn-text {
      transition: opacity 0.2s ease;
    }

    .btn-login.loading .btn-text {
      opacity: 0;
    }

    .btn-login.loading::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 20px;
      height: 20px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: white;
      animation: spin 1s ease-in-out infinite;
    }

    /* Responsive Design */
    @media (max-width: 480px) {
      body {
        padding: 16px;
      }
      
      .login-card {
        padding: 32px 24px;
      }
      
      .login-title {
        font-size: 24px;
      }
    }

    /* Accessibility */
    .form-control:focus-visible {
      outline: 2px solid var(--primary-color);
      outline-offset: 2px;
    }

    .btn-login:focus-visible {
      outline: 2px solid var(--primary-color);
      outline-offset: 2px;
    }
  </style>
</head>
<body>
  <div class="login-wrapper">
    <div class="login-card">
      <!-- Brand Logo -->
      <div class="brand-logo">
        <i class="bi bi-shield-lock"></i>
      </div>
      
      <!-- Header -->
      <h1 class="login-title">Welcome Back</h1>
      <p class="login-subtitle">Sign in to your account to continue</p>
      
      <!-- Error Message -->
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i>
          <?php echo htmlspecialchars($error_message); ?>
        </div>
      <?php endif; ?>
      
      <!-- Login Form -->
      <form id="loginForm" action="login.php" method="POST" novalidate>
        <div class="form-group">
          <label for="email" class="form-label">Email Address</label>
          <div class="position-relative">
            <i class="bi bi-envelope input-icon"></i>
            <input 
              type="email" 
              class="form-control" 
              id="email" 
              name="email" 
              placeholder="Enter your email"
              value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
              required 
              autocomplete="email"
            />
          </div>
        </div>
        
        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <div class="position-relative">
            <i class="bi bi-lock input-icon"></i>
            <input 
              type="password" 
              class="form-control" 
              id="password" 
              name="password" 
              placeholder="Enter your password"
              required 
              autocomplete="current-password"
            />
            <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>
        
        <div class="form-check">
          <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me" />
          <label class="form-check-label" for="rememberMe">
            Keep me signed in
          </label>
        </div>
        
        <button type="submit" class="btn btn-login" id="loginBtn">
          <span class="btn-text">Sign In</span>
        </button>
        
        <div class="divider">
          <span>or</span>
        </div>
        <button type="submit" class="btn btn-login" id="loginBtn">
        <a href="admin_login.php" class="btn btn-login" id="loginBtn">
          <span class="btn-text">Sign In As Admin</span>
        </a>
        </button>

      </form>
      
      <!-- Footer Links -->
      <div class="footer-links">
        <a href="forgot-password.php">Forgot your password?</a>
        <p>Don't have an account? <a href="register.php">Create one here</a></p>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Password toggle functionality
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = this.querySelector('i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
      }
    });

    // Form submission with loading state
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      const submitBtn = document.getElementById('loginBtn');
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      
      // Basic validation
      if (!email || !password) {
        e.preventDefault();
        return;
      }
      
      // Add loading state
      submitBtn.classList.add('loading');
      submitBtn.disabled = true;
    });

    // Enhanced form validation
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
      input.addEventListener('blur', function() {
        if (this.hasAttribute('required') && !this.value.trim()) {
          this.style.borderColor = 'var(--error-color)';
        } else {
          this.style.borderColor = 'var(--border-color)';
        }
      });
      
      input.addEventListener('input', function() {
        if (this.style.borderColor === 'rgb(239, 68, 68)') {
          this.style.borderColor = 'var(--border-color)';
        }
      });
    });

    // Auto-focus first input
    document.getElementById('email').focus();
  </script>
</body>
</html>