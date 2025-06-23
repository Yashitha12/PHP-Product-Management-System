<?php
// register.php - Modern Professional Registration Page

include 'db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $full_name = trim($_POST['fullName']);
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirmPassword'];

  // Enhanced validation
  if (empty($full_name) || empty($username) || empty($email) || empty($password)) {
    $error = "All fields are required.";
  } elseif (strlen($password) < 8) {
    $error = "Password must be at least 8 characters long.";
  } elseif ($password !== $confirmPassword) {
    $error = "Passwords do not match.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Please enter a valid email address.";
  } else {
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
      $error = "An account with this email already exists.";
      $stmt->close();
    } else {
      $stmt->close();
      
      // Check if username already exists
      $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $stmt->store_result();
      
      if ($stmt->num_rows > 0) {
        $error = "This username is already taken.";
        $stmt->close();
      } else {
        $stmt->close();
        
        // Insert new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $full_name, $username, $email, $hashed_password);

        if ($stmt->execute()) {
          $stmt->close();
          $conn->close();
          header("Location: login.php?registered=1");
          exit();
        } else {
          $error = "Registration failed. Please try again.";
        }
        $stmt->close();
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Account - Your App</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary-color: #10b981;
      --primary-hover: #059669;
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
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      min-height: 100vh;
      margin: 0;
      padding: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .register-wrapper {
      width: 100%;
      max-width: 480px;
      position: relative;
    }

    .register-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 24px;
      padding: 48px 40px;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 
                  0 10px 10px -5px rgba(0, 0, 0, 0.04);
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: all 0.3s ease;
    }

    .register-card:hover {
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

    .register-title {
      font-size: 28px;
      font-weight: 700;
      color: var(--text-primary);
      text-align: center;
      margin-bottom: 8px;
      letter-spacing: -0.025em;
    }

    .register-subtitle {
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
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
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
      background-color: rgba(16, 185, 129, 0.1);
    }

    .password-strength {
      margin-top: 8px;
      font-size: 12px;
    }

    .strength-meter {
      height: 4px;
      background-color: #e2e8f0;
      border-radius: 2px;
      overflow: hidden;
      margin-top: 4px;
    }

    .strength-fill {
      height: 100%;
      transition: all 0.3s ease;
      border-radius: 2px;
    }

    .strength-weak { width: 25%; background-color: #ef4444; }
    .strength-fair { width: 50%; background-color: #f59e0b; }
    .strength-good { width: 75%; background-color: #3b82f6; }
    .strength-strong { width: 100%; background-color: #10b981; }

    .btn-register {
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
      margin-top: 8px;
    }

    .btn-register:hover {
      transform: translateY(-1px);
      box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
    }

    .btn-register:active {
      transform: translateY(0);
    }

    .btn-register:disabled {
      opacity: 0.7;
      cursor: not-allowed;
      transform: none;
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
      margin: 0;
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

    .alert-success {
      background-color: rgba(16, 185, 129, 0.1);
      color: var(--success-color);
      border-left: 4px solid var(--success-color);
    }

    .btn-register.loading::after {
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

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .btn-register.loading .btn-text {
      opacity: 0;
    }

    .form-validation {
      font-size: 12px;
      margin-top: 4px;
      color: var(--error-color);
      display: none;
    }

    .form-control.is-invalid {
      border-color: var(--error-color);
    }

    .form-control.is-valid {
      border-color: var(--success-color);
    }

    .form-validation.show {
      display: block;
    }

    /* Responsive Design */
    @media (max-width: 480px) {
      body {
        padding: 16px;
      }
      
      .register-card {
        padding: 32px 24px;
      }
      
      .register-title {
        font-size: 24px;
      }
    }

    /* Accessibility */
    .form-control:focus-visible {
      outline: 2px solid var(--primary-color);
      outline-offset: 2px;
    }

    .btn-register:focus-visible {
      outline: 2px solid var(--primary-color);
      outline-offset: 2px;
    }
  </style>
</head>
<body>
  <div class="register-wrapper">
    <div class="register-card">
      <!-- Brand Logo -->
      <div class="brand-logo">
        <i class="bi bi-person-plus"></i>
      </div>
      
      <!-- Header -->
      <h1 class="register-title">Create Account</h1>
      <p class="register-subtitle">Join us today and get started</p>
      
      <!-- Error Message -->
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i>
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
      
      <!-- Registration Form -->
      <form id="registerForm" action="register.php" method="POST" novalidate>
        <div class="form-group">
          <label for="fullName" class="form-label">Full Name</label>
          <div class="position-relative">
            <i class="bi bi-person input-icon"></i>
            <input 
              type="text" 
              class="form-control" 
              id="fullName" 
              name="fullName" 
              placeholder="Enter your full name"
              value="<?php echo isset($_POST['fullName']) ? htmlspecialchars($_POST['fullName']) : ''; ?>"
              required 
              autocomplete="name"
            />
            <div class="form-validation" id="fullNameError"></div>
          </div>
        </div>
        
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
            <div class="form-validation" id="emailError"></div>
          </div>
        </div>
        
        <div class="form-group">
          <label for="username" class="form-label">Username</label>
          <div class="position-relative">
            <i class="bi bi-at input-icon"></i>
            <input 
              type="text" 
              class="form-control" 
              id="username" 
              name="username" 
              placeholder="Choose a username"
              value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
              required 
              autocomplete="username"
            />
            <div class="form-validation" id="usernameError"></div>
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
              placeholder="Create a password"
              required 
              autocomplete="new-password"
            />
            <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
              <i class="bi bi-eye"></i>
            </button>
          </div>
          <div class="password-strength" id="passwordStrength" style="display: none;">
            <div class="strength-meter">
              <div class="strength-fill" id="strengthFill"></div>
            </div>
            <span id="strengthText"></span>
          </div>
          <div class="form-validation" id="passwordError"></div>
        </div>
        
        <div class="form-group">
          <label for="confirmPassword" class="form-label">Confirm Password</label>
          <div class="position-relative">
            <i class="bi bi-shield-check input-icon"></i>
            <input 
              type="password" 
              class="form-control" 
              id="confirmPassword" 
              name="confirmPassword" 
              placeholder="Confirm your password"
              required 
              autocomplete="new-password"
            />
            <button type="button" class="password-toggle" id="toggleConfirmPassword" aria-label="Toggle password visibility">
              <i class="bi bi-eye"></i>
            </button>
          </div>
          <div class="form-validation" id="confirmPasswordError"></div>
        </div>
        
        <button type="submit" class="btn btn-register" id="registerBtn">
          <span class="btn-text">Create Account</span>
        </button>
      </form>
      
      <!-- Footer Links -->
      <div class="footer-links">
        <p>Already have an account? <a href="login.php">Sign in here</a></p>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Password toggle functionality
    function setupPasswordToggle(inputId, toggleId) {
      document.getElementById(toggleId).addEventListener('click', function() {
        const passwordInput = document.getElementById(inputId);
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
    }

    setupPasswordToggle('password', 'togglePassword');
    setupPasswordToggle('confirmPassword', 'toggleConfirmPassword');

    // Password strength checker
    document.getElementById('password').addEventListener('input', function() {
      const password = this.value;
      const strengthElement = document.getElementById('passwordStrength');
      const strengthFill = document.getElementById('strengthFill');
      const strengthText = document.getElementById('strengthText');
      
      if (password.length === 0) {
        strengthElement.style.display = 'none';
        return;
      }
      
      strengthElement.style.display = 'block';
      
      let strength = 0;
      let strengthLabel = '';
      
      // Check password criteria
      if (password.length >= 8) strength++;
      if (/[a-z]/.test(password)) strength++;
      if (/[A-Z]/.test(password)) strength++;
      if (/[0-9]/.test(password)) strength++;
      if (/[^A-Za-z0-9]/.test(password)) strength++;
      
      // Update strength display
      strengthFill.className = 'strength-fill';
      switch (strength) {
        case 0:
        case 1:
          strengthFill.classList.add('strength-weak');
          strengthLabel = 'Weak';
          break;
        case 2:
          strengthFill.classList.add('strength-fair');
          strengthLabel = 'Fair';
          break;
        case 3:
        case 4:
          strengthFill.classList.add('strength-good');
          strengthLabel = 'Good';
          break;
        case 5:
          strengthFill.classList.add('strength-strong');
          strengthLabel = 'Strong';
          break;
      }
      
      strengthText.textContent = `Password strength: ${strengthLabel}`;
    });

    // Form validation
    const form = document.getElementById('registerForm');
    const inputs = {
      fullName: document.getElementById('fullName'),
      email: document.getElementById('email'),
      username: document.getElementById('username'),
      password: document.getElementById('password'),
      confirmPassword: document.getElementById('confirmPassword')
    };

    // Real-time validation
    inputs.fullName.addEventListener('blur', function() {
      validateField('fullName', this.value.trim().length >= 2, 'Full name must be at least 2 characters long');
    });

    inputs.email.addEventListener('blur', function() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      validateField('email', emailRegex.test(this.value), 'Please enter a valid email address');
    });

    inputs.username.addEventListener('blur', function() {
      const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
      validateField('username', usernameRegex.test(this.value), 'Username must be 3-20 characters, letters, numbers, and underscores only');
    });

    inputs.password.addEventListener('blur', function() {
      validateField('password', this.value.length >= 8, 'Password must be at least 8 characters long');
    });

    inputs.confirmPassword.addEventListener('blur', function() {
      validateField('confirmPassword', this.value === inputs.password.value, 'Passwords do not match');
    });

    inputs.password.addEventListener('input', function() {
      if (inputs.confirmPassword.value) {
        validateField('confirmPassword', this.value === inputs.confirmPassword.value, 'Passwords do not match');
      }
    });

    function validateField(fieldName, isValid, errorMessage) {
      const input = inputs[fieldName];
      const errorElement = document.getElementById(fieldName + 'Error');
      
      if (isValid) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        errorElement.textContent = '';
        errorElement.classList.remove('show');
      } else if (input.value.length > 0) {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        errorElement.textContent = errorMessage;
        errorElement.classList.add('show');
      }
    }

    // Form submission
    form.addEventListener('submit', function(e) {
      const submitBtn = document.getElementById('registerBtn');
      
      // Validate all fields
      let isFormValid = true;
      
      Object.keys(inputs).forEach(fieldName => {
        const input = inputs[fieldName];
        if (!input.checkValidity() || input.classList.contains('is-invalid')) {
          isFormValid = false;
        }
      });
      
      if (!isFormValid) {
        e.preventDefault();
        return;
      }
      
      // Add loading state
      submitBtn.classList.add('loading');
      submitBtn.disabled = true;
    });

    // Auto-focus first input
    document.getElementById('fullName').focus();
  </script>
</body>
</html>