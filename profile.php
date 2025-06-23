<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Check if connection exists
if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  if ($_POST['action'] === 'update_profile') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $user_id = $_SESSION['user_id'];
    
    // Validate inputs
    if (empty($full_name) || empty($username) || empty($email)) {
      $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error = "Invalid email format.";
    } else {
      // Check if username or email already exists (excluding current user)
      $check_stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
      $check_stmt->bind_param("ssi", $username, $email, $user_id);
      $check_stmt->execute();
      $check_result = $check_stmt->get_result();
      
      if ($check_result->num_rows > 0) {
        $error = "Username or email already exists.";
      } else {
        // Update user information
        $update_stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, email = ? WHERE id = ?");
        $update_stmt->bind_param("sssi", $full_name, $username, $email, $user_id);
        
        if ($update_stmt->execute()) {
          $success = "Profile updated successfully!";
        } else {
          $error = "Error updating profile: " . $conn->error;
        }
        $update_stmt->close();
      }
      $check_stmt->close();
    }
  } elseif ($_POST['action'] === 'delete_account') {
    $user_id = $_SESSION['user_id'];
    
    // Delete user account
    $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $delete_stmt->bind_param("i", $user_id);
    
    if ($delete_stmt->execute()) {
      $delete_stmt->close();
      $conn->close();
      
      // Destroy session and redirect
      session_destroy();
      header("Location: login.php?deleted=1");
      exit();
    } else {
      $error = "Error deleting account: " . $conn->error;
    }
    $delete_stmt->close();
  }
}

// First try with just the basic fields that definitely exist
$stmt = $conn->prepare("SELECT full_name, username, email FROM users WHERE id = ?");
if (!$stmt) {
  die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $_SESSION['user_id']);
if (!$stmt->execute()) {
  die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
  die("User not found");
}

// Try to get additional fields if they exist
$stmt2 = $conn->prepare("SHOW COLUMNS FROM users LIKE 'created_at'");
$has_created_at = false;
$has_last_login = false;

if ($stmt2) {
  $stmt2->execute();
  $result2 = $stmt2->get_result();
  $has_created_at = $result2->num_rows > 0;
  $stmt2->close();
}

$stmt3 = $conn->prepare("SHOW COLUMNS FROM users LIKE 'last_login'");
if ($stmt3) {
  $stmt3->execute();
  $result3 = $stmt3->get_result();
  $has_last_login = $result3->num_rows > 0;
  $stmt3->close();
}

// Get additional fields if they exist
if ($has_created_at || $has_last_login) {
  $fields = "full_name, username, email";
  if ($has_created_at) $fields .= ", created_at";
  if ($has_last_login) $fields .= ", last_login";
  
  $stmt4 = $conn->prepare("SELECT $fields FROM users WHERE id = ?");
  if ($stmt4) {
    $stmt4->bind_param("i", $_SESSION['user_id']);
    $stmt4->execute();
    $result4 = $stmt4->get_result();
    $user = $result4->fetch_assoc();
    $stmt4->close();
  }
}

$stmt->close();
$conn->close();

// Generate user initials for avatar
$nameParts = explode(' ', $user['full_name']);
$initials = '';
foreach ($nameParts as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
}
$initials = substr($initials, 0, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile - <?= htmlspecialchars($user['full_name']) ?></title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #2563eb;
      --primary-dark: #1d4ed8;
      --secondary-color: #64748b;
      --accent-color: #f8fafc;
      --background: #f1f5f9;
      --surface: #ffffff;
      --text-primary: #0f172a;
      --text-secondary: #475569;
      --text-muted: #94a3b8;
      --border-color: #e2e8f0;
      --success-color: #10b981;
      --warning-color: #f59e0b;
      --danger-color: #ef4444;
      --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
      --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
      --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
      --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
      background: linear-gradient(135deg, var(--background) 0%, #e2e8f0 100%);
      color: var(--text-primary);
      line-height: 1.6;
      min-height: 100vh;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 2rem;
    }

    /* Enhanced Navigation */
    .nav {
      background: var(--surface);
      padding: 1rem 1.5rem;
      border-radius: 1rem;
      box-shadow: var(--shadow);
      margin-bottom: 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      backdrop-filter: blur(10px);
      border: 1px solid var(--border-color);
    }

    .nav-brand {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .nav-brand i {
      color: var(--primary-color);
      font-size: 1.5rem;
    }

    .nav h1 {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--text-primary);
      background: linear-gradient(135deg, var(--primary-color), #8b5cf6);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .nav-links {
      display: flex;
      gap: 1.5rem;
      align-items: center;
    }

    .nav-links a {
      text-decoration: none;
      color: var(--text-secondary);
      font-size: 0.875rem;
      font-weight: 500;
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .nav-links a:hover {
      color: var(--primary-color);
      background: var(--accent-color);
    }

    .nav-links a.active {
      color: var(--primary-color);
      background: var(--accent-color);
      font-weight: 600;
    }

    /* Alert Messages */
    .alert {
      padding: 1rem 1.5rem;
      border-radius: 0.75rem;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-weight: 500;
    }

    .alert-success {
      background: #dcfce7;
      color: #166534;
      border: 1px solid #bbf7d0;
    }

    .alert-error {
      background: #fef2f2;
      color: #dc2626;
      border: 1px solid #fecaca;
    }

    /* Enhanced Profile Card */
    .profile-section {
      display: grid;
      grid-template-columns: 1fr;
      gap: 2rem;
    }

    .profile-header {
      background: linear-gradient(135deg, var(--primary-color), #8b5cf6);
      border-radius: 1.5rem;
      padding: 3rem 2rem 2rem;
      text-align: center;
      position: relative;
      overflow: hidden;
      box-shadow: var(--shadow-xl);
    }

    .profile-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="25" cy="75" r="1" fill="white" opacity="0.05"/><circle cx="75" cy="25" r="1" fill="white" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
      opacity: 0.3;
    }

    .avatar-container {
      position: relative;
      display: inline-block;
      margin-bottom: 1.5rem;
    }

    .avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: var(--surface);
      color: var(--primary-color);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      font-weight: 700;
      box-shadow: var(--shadow-lg);
      border: 4px solid rgba(255, 255, 255, 0.2);
      position: relative;
      z-index: 1;
    }

    .status-indicator {
      position: absolute;
      bottom: 8px;
      right: 8px;
      width: 24px;
      height: 24px;
      background: var(--success-color);
      border-radius: 50%;
      border: 3px solid var(--surface);
      box-shadow: var(--shadow);
    }

    .profile-title {
      color: white;
      position: relative;
      z-index: 1;
    }

    .name {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .username {
      font-size: 1.125rem;
      opacity: 0.9;
      margin-bottom: 1rem;
    }

    /* Profile Details Card */
    .profile-details {
      background: var(--surface);
      border-radius: 1.5rem;
      padding: 2rem;
      box-shadow: var(--shadow);
      border: 1px solid var(--border-color);
    }

    .details-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.5rem;
    }

    .details-title {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .details-title h3 {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .details-title i {
      color: var(--primary-color);
    }

    .edit-toggle {
      background: none;
      border: none;
      color: var(--primary-color);
      cursor: pointer;
      font-size: 0.875rem;
      font-weight: 500;
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .edit-toggle:hover {
      background: var(--accent-color);
    }

    .info-grid {
      display: grid;
      gap: 1.5rem;
    }

    .info-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem;
      background: var(--accent-color);
      border-radius: 0.75rem;
      border: 1px solid var(--border-color);
      transition: all 0.2s ease;
    }

    .info-item:hover {
      transform: translateY(-1px);
      box-shadow: var(--shadow);
    }

    .info-left {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      flex: 1;
    }

    .info-icon {
      width: 2.5rem;
      height: 2.5rem;
      border-radius: 0.5rem;
      background: var(--primary-color);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.875rem;
    }

    .info-text {
      display: flex;
      flex-direction: column;
      flex: 1;
    }

    .info-label {
      color: var(--text-muted);
      font-size: 0.875rem;
      font-weight: 500;
    }

    .info-value {
      font-weight: 600;
      color: var(--text-primary);
      font-size: 1rem;
    }

    /* Edit Form Styles */
    .edit-form {
      display: none;
    }

    .edit-form.active {
      display: block;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--text-primary);
    }

    .form-input {
      width: 100%;
      padding: 0.875rem 1rem;
      border: 2px solid var(--border-color);
      border-radius: 0.75rem;
      font-size: 1rem;
      transition: all 0.2s ease;
      background: var(--surface);
    }

    .form-input:focus {
      outline: none;
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-actions {
      display: flex;
      gap: 1rem;
      justify-content: flex-end;
      margin-top: 2rem;
    }

    /* Action Buttons */
    .action-buttons {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
      margin-top: 2rem;
    }

    .btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      padding: 0.875rem 1.5rem;
      border: none;
      border-radius: 0.75rem;
      font-weight: 600;
      font-size: 0.875rem;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
      position: relative;
      overflow: hidden;
    }

    .btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }

    .btn:hover::before {
      left: 100%;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      color: white;
      box-shadow: var(--shadow);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .btn-secondary {
      background: var(--surface);
      color: var(--text-secondary);
      border: 2px solid var(--border-color);
    }

    .btn-secondary:hover {
      background: var(--accent-color);
      border-color: var(--primary-color);
      color: var(--primary-color);
      transform: translateY(-2px);
    }

    .btn-danger {
      background: linear-gradient(135deg, var(--danger-color), #dc2626);
      color: white;
      box-shadow: var(--shadow);
    }

    .btn-danger:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .btn-success {
      background: linear-gradient(135deg, var(--success-color), #059669);
      color: white;
      box-shadow: var(--shadow);
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      backdrop-filter: blur(5px);
    }

    .modal.active {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background: var(--surface);
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: var(--shadow-xl);
      max-width: 400px;
      width: 90%;
      margin: 2rem;
    }

    .modal-header {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 1rem;
    }

    .modal-header i {
      color: var(--danger-color);
      font-size: 1.5rem;
    }

    .modal-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .modal-body {
      margin-bottom: 2rem;
      color: var(--text-secondary);
      line-height: 1.6;
    }

    .modal-actions {
      display: flex;
      gap: 1rem;
      justify-content: flex-end;
    }

    /* Notification Toast */
    .toast {
      position: fixed;
      top: 2rem;
      right: 2rem;
      background: var(--surface);
      color: var(--text-primary);
      padding: 1rem 1.5rem;
      border-radius: 0.75rem;
      box-shadow: var(--shadow-lg);
      border: 1px solid var(--border-color);
      transform: translateX(400px);
      opacity: 0;
      transition: all 0.3s ease;
      z-index: 1000;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .toast.show {
      transform: translateX(0);
      opacity: 1;
    }

    .toast i {
      color: var(--success-color);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .container {
        padding: 1rem;
      }

      .nav {
        flex-direction: column;
        gap: 1rem;
      }

      .nav-links {
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.75rem;
      }

      .profile-header {
        padding: 2rem 1.5rem 1.5rem;
      }

      .name {
        font-size: 1.5rem;
      }

      .action-buttons {
        grid-template-columns: 1fr;
      }

      .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
      }

      .info-left {
        width: 100%;
      }

      .details-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }

      .form-actions {
        flex-direction: column;
      }

      .modal-actions {
        flex-direction: column;
      }
    }

    /* Loading Animation */
    .loading {
      display: inline-block;
      width: 1rem;
      height: 1rem;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: white;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Smooth Animations */
    .profile-section > * {
      animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

<div class="container">
  <!-- Enhanced Navigation -->
  <nav class="nav">
    <div class="nav-brand">
      <i class="fas fa-user-circle"></i>
      <h1>ProfileHub</h1>
    </div>
    <div class="nav-links">
      <a href="welcome.php">
        <i class="fas fa-home"></i>
        Dashboard
      </a>
      <a href="profile.php" class="active">
        <i class="fas fa-user"></i>
        Profile
      </a>
      <a href="settings.php">
        <i class="fas fa-cog"></i>
        Settings
      </a>
      <a href="logout.php">
        <i class="fas fa-sign-out-alt"></i>
        Logout
      </a>
    </div>
  </nav>

  <!-- Alert Messages -->
  <?php if (isset($success)): ?>
    <div class="alert alert-success">
      <i class="fas fa-check-circle"></i>
      <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>

  <?php if (isset($error)): ?>
    <div class="alert alert-error">
      <i class="fas fa-exclamation-triangle"></i>
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <!-- Enhanced Profile Section -->
  <div class="profile-section">
    <!-- Profile Header -->
    <div class="profile-header">
      <div class="avatar-container">
        <div class="avatar">
          <?= htmlspecialchars($initials) ?>
        </div>
        <div class="status-indicator" title="Online"></div>
      </div>
      
      <div class="profile-title">
        <div class="name"><?= htmlspecialchars($user['full_name']) ?></div>
        <div class="username">@<?= htmlspecialchars($user['username']) ?></div>
      </div>
    </div>

    <!-- Profile Details -->
    <div class="profile-details">
      <div class="details-header">
        <div class="details-title">
          <i class="fas fa-info-circle"></i>
          <h3>Account Information</h3>
        </div>
        <button class="edit-toggle" onclick="toggleEdit()">
          <i class="fas fa-edit"></i>
          <span id="edit-btn-text">Edit Profile</span>
        </button>
      </div>

      <!-- View Mode -->
      <div class="info-view" id="info-view">
        <div class="info-grid">
          <div class="info-item">
            <div class="info-left">
              <div class="info-icon">
                <i class="fas fa-user"></i>
              </div>
              <div class="info-text">
                <span class="info-label">Full Name</span>
                <span class="info-value"><?= htmlspecialchars($user['full_name']) ?></span>
              </div>
            </div>
          </div>

          <div class="info-item">
            <div class="info-left">
              <div class="info-icon">
                <i class="fas fa-at"></i>
              </div>
              <div class="info-text">
                <span class="info-label">Username</span>
                <span class="info-value"><?= htmlspecialchars($user['username']) ?></span>
              </div>
            </div>
          </div>

          <div class="info-item">
            <div class="info-left">
              <div class="info-icon">
                <i class="fas fa-envelope"></i>
              </div>
              <div class="info-text">
                <span class="info-label">Email Address</span>
                <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
              </div>
            </div>
          </div>
          
          <?php if (isset($user['created_at'])): ?>
          <div class="info-item">
            <div class="info-left">
              <div class="info-icon">
                <i class="fas fa-calendar-alt"></i>
              </div>
              <div class="info-text">
                <span class="info-label">Member Since</span>
                <span class="info-value"><?= date('F j, Y', strtotime($user['created_at'])) ?></span>
              </div>
            </div>
          </div>
          <?php endif; ?>
          
          <?php if (isset($user['last_login'])): ?>
          <div class="info-item">
            <div class="info-left">
              <div class="info-icon">
                <i class="fas fa-clock"></i>
              </div>
              <div class="info-text">
                <span class="info-label">Last Active</span>
                <span class="info-value"><?= date('M j, Y \a\t g:i A', strtotime($user['last_login'])) ?></span>
              </div>
            </div>
          </div>
          <?php endif; ?>
          
          <div class="info-item">
            <div class="info-left">
              <div class="info-icon">
                <i class="fas fa-shield-alt"></i>
              </div>
              <div class="info-text">
                <span class="info-label">Account Status</span>
                <span class="info-value">Verified & Active</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Mode -->
      <div class="edit-form" id="edit-form">
        <form method="POST" action="">
          <input type="hidden" name="action" value="update_profile">
          
          <div class="form-group">
            <label class="form-label" for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" class="form-input" 
                   value="<?= htmlspecialchars($user['full_name']) ?>" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" id="username" name="username" class="form-input" 
                   value="<?= htmlspecialchars($user['username']) ?>" required>
          </div>

          <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-input" 
                   value="<?= htmlspecialchars($user['email']) ?>" required>
          </div>

          <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="cancelEdit()">
              <i class="fas fa-times"></i>
              Cancel
            </button>
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save"></i>
              Save Changes
            </button>
          </div>
        </form>
      </div>

      <!-- Action Buttons -->
      <div class="action-buttons">
        <button class="btn btn-primary" onclick="shareProfile()">
          <i class="fas fa-share-alt"></i>
          Share Profile
        </button>
        <button class="btn btn-danger" onclick="confirmDelete()">
          <i class="fas fa-trash-alt"></i>
          Delete Account
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal">
  <div class="modal-content">
    <div class="modal-header">
      <i class="fas fa-exclamation-triangle"></i>
      <h3 class="modal-title">Delete Account</h3>
    </div>
    <div class="modal-body">
      <p>Are you sure you want to delete your account? This action cannot be undone and will permanently remove all your data.</p>
      <p><strong>This action is irreversible!</strong></p>
    </div>
    <div class="modal-actions">
      <button class="btn btn-secondary" onclick="closeDeleteModal()">
        <i class="fas fa-times"></i>
        Cancel
      </button>
      <form method="POST" action="" style="display: inline;">
        <input type="hidden" name="action" value="delete_account">
        <button type="submit" class="btn btn-danger">
          <i class="fas fa-trash-alt"></i>
          Delete Account
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Toast Notification -->
<div class="toast" id="toast">
  <i class="fas fa-check-circle"></i>
  <span id="toast-message">Success!</span>
</div>

<script>
let isEditing = false;

function toggleEdit() {
  const infoView = document.getElementById('info-view');
  const editForm = document.getElementById('edit-form');
  const editBtnText = document.getElementById('edit-btn-text');
  const editBtn = document.querySelector('.edit-toggle i');
  
  if (!isEditing) {
    // Switch to edit mode
    infoView.style.display = 'none';
    editForm.classList.add('active');
    editBtnText.textContent = 'Cancel Edit';
    editBtn.className = 'fas fa-times';
    isEditing = true;
  } else {
    // Switch to view mode
    cancelEdit();
  }
}

function cancelEdit() {
  const infoView = document.getElementById('info-view');
  const editForm = document.getElementById('edit-form');
  const editBtnText = document.getElementById('edit-btn-text');
  const editBtn = document.querySelector('.edit-toggle i');
  
  infoView.style.display = 'block';
  editForm.classList.remove('active');
  editBtnText.textContent = 'Edit Profile';
  editBtn.className = 'fas fa-edit';
  isEditing = false;
  
  // Reset form values to original
  document.getElementById('full_name').value = '<?= htmlspecialchars($user['full_name']) ?>';
  document.getElementById('username').value = '<?= htmlspecialchars($user['username']) ?>';
  document.getElementById('email').value = '<?= htmlspecialchars($user['email']) ?>';
}

function confirmDelete() {
  const modal = document.getElementById('deleteModal');
  modal.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
  const modal = document.getElementById('deleteModal');
  modal.classList.remove('active');
  document.body.style.overflow = 'auto';
}

function showToast(message, type = 'success') {
  const toast = document.getElementById('toast');
  const toastMessage = document.getElementById('toast-message');
  const icon = toast.querySelector('i');
  
  toastMessage.textContent = message;
  
  if (type === 'success') {
    icon.className = 'fas fa-check-circle';
    icon.style.color = 'var(--success-color)';
  } else if (type === 'error') {
    icon.className = 'fas fa-exclamation-triangle';
    icon.style.color = 'var(--warning-color)';
  }
  
  toast.classList.add('show');
  
  setTimeout(() => {
    toast.classList.remove('show');
  }, 3000);
}

function shareProfile() {
  if (navigator.share) {
    navigator.share({
      title: '<?= htmlspecialchars($user['full_name']) ?> - Profile',
      text: 'Check out my profile on ProfileHub',
      url: window.location.href
    }).then(() => {
      showToast('Profile shared successfully!');
    }).catch(() => {
      fallbackShare();
    });
  } else {
    fallbackShare();
  }
}

function fallbackShare() {
  navigator.clipboard.writeText(window.location.href).then(() => {
    showToast('Profile link copied to clipboard!');
  }).catch(() => {
    showToast('Unable to copy link. Please copy manually.', 'error');
  });
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeDeleteModal();
  }
});

// Handle form submission with loading state
document.querySelector('form').addEventListener('submit', function(e) {
  const submitBtn = this.querySelector('button[type="submit"]');
  const originalHtml = submitBtn.innerHTML;
  
  submitBtn.innerHTML = '<div class="loading"></div> Saving...';
  submitBtn.disabled = true;
  
  // Re-enable button after a delay if form submission fails
  setTimeout(() => {
    if (submitBtn.disabled) {
      submitBtn.innerHTML = originalHtml;
      submitBtn.disabled = false;
    }
  }, 5000);
});

// Add smooth scrolling and enhanced interactions
document.addEventListener('DOMContentLoaded', function() {
  // Add hover effects to info items
  const infoItems = document.querySelectorAll('.info-item');
  infoItems.forEach(item => {
    item.addEventListener('mouseenter', function() {
      this.style.transform = 'translateY(-2px)';
    });
    item.addEventListener('mouseleave', function() {
      this.style.transform = 'translateY(0)';
    });
  });
  
  // Add ripple effect to buttons
  const buttons = document.querySelectorAll('.btn');
  buttons.forEach(button => {
    button.addEventListener('click', function(e) {
      const ripple = document.createElement('span');
      const rect = this.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      const x = e.clientX - rect.left - size / 2;
      const y = e.clientY - rect.top - size / 2;
      
      ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
      `;
      
      this.appendChild(ripple);
      
      setTimeout(() => {
        ripple.remove();
      }, 600);
    });
  });

  // Form validation
  const form = document.querySelector('form');
  const inputs = form.querySelectorAll('.form-input');
  
  inputs.forEach(input => {
    input.addEventListener('blur', function() {
      validateField(this);
    });
    
    input.addEventListener('input', function() {
      clearFieldError(this);
    });
  });
  
  function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    if (field.hasAttribute('required') && !value) {
      isValid = false;
      errorMessage = 'This field is required.';
    } else if (field.type === 'email' && value && !isValidEmail(value)) {
      isValid = false;
      errorMessage = 'Please enter a valid email address.';
    } else if (field.name === 'username' && value && value.length < 3) {
      isValid = false;
      errorMessage = 'Username must be at least 3 characters long.';
    }
    
    if (!isValid) {
      showFieldError(field, errorMessage);
    } else {
      clearFieldError(field);
    }
    
    return isValid;
  }
  
  function showFieldError(field, message) {
    clearFieldError(field);
    field.style.borderColor = 'var(--danger-color)';
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.cssText = `
      color: var(--danger-color);
      font-size: 0.875rem;
      margin-top: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    `;
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    
    field.parentNode.appendChild(errorDiv);
  }
  
  function clearFieldError(field) {
    field.style.borderColor = 'var(--border-color)';
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
      errorDiv.remove();
    }
  }
  
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }
  
  // Form submission validation
  form.addEventListener('submit', function(e) {
    let isFormValid = true;
    
    inputs.forEach(input => {
      if (!validateField(input)) {
        isFormValid = false;
      }
    });
    
    if (!isFormValid) {
      e.preventDefault();
      showToast('Please fix the errors in the form.', 'error');
      return false;
    }
  });
});

// Add CSS for ripple animation
const style = document.createElement('style');
style.textContent = `
  @keyframes ripple {
    to {
      transform: scale(4);
      opacity: 0;
    }
  }
  
  .field-error {
    animation: slideDown 0.3s ease-out;
  }
  
  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
`;
document.head.appendChild(style);

// Show success/error messages from PHP
<?php if (isset($success)): ?>
  setTimeout(() => {
    showToast('<?= addslashes($success) ?>', 'success');
  }, 500);
<?php endif; ?>

<?php if (isset($error)): ?>
  setTimeout(() => {
    showToast('<?= addslashes($error) ?>', 'error');
  }, 500);
<?php endif; ?>
</script>

</body>
</html>