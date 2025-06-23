<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
include 'db.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = $_POST['price'];
    
    // Validation
    if (empty($name) || empty($desc) || empty($price)) {
        $error_message = "All fields are required.";
    } elseif ($price <= 0) {
        $error_message = "Price must be greater than 0.";
    } else {
        $image = '';
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                $error_message = "Only JPEG, PNG, GIF, and WebP images are allowed.";
            } elseif ($_FILES['image']['size'] > $max_size) {
                $error_message = "Image size must be less than 5MB.";
            } else {
                // Create uploads directory if it doesn't exist
                $upload_dir = "uploads/";
                if (!is_dir($upload_dir)) {
                    if (!mkdir($upload_dir, 0755, true)) {
                        $error_message = "Failed to create upload directory.";
                    }
                }
                
                if (empty($error_message)) {
                    $image_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $image = uniqid() . '.' . $image_extension;
                    $target = $upload_dir . $image;
                    
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                        $error_message = "Failed to upload image. Please check directory permissions.";
                    }
                }
            }
        }
        
        if (empty($error_message)) {
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssds", $name, $desc, $price, $image);
            
            if ($stmt->execute()) {
                $success_message = "Product added successfully!";
                // Clear form data
                $name = $desc = $price = '';
            } else {
                $error_message = "Failed to add product. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product - Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            line-height: 1.6;
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 600;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
        }

        .breadcrumb a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            color: white;
        }

        .main-content {
            padding: 2rem 0;
        }

        .form-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .form-header {
            background: #f8fafc;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .form-header h2 {
            color: #1e293b;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .form-body {
            padding: 2rem;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .form-label.required::after {
            content: "*";
            color: #ef4444;
            margin-left: 0.25rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            background-color: #f9fafb;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-input:hover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }

        .file-input-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .file-input-icon {
            font-size: 2rem;
            color: #6b7280;
        }

        .file-input-text {
            font-weight: 500;
            color: #374151;
        }

        .file-input-subtext {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .file-input input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .image-preview {
            margin-top: 1rem;
            display: none;
        }

        .image-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .form-help {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .loading {
            display: none;
            margin-left: 0.5rem;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-body {
                padding: 1.5rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="header-content">
                <h1><i class="fas fa-plus-circle"></i> Add New Product</h1>
                <div class="breadcrumb">
                    <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Add Product</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h2>Product Information</h2>
                </div>
                <div class="form-body">
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?= htmlspecialchars($success_message) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= htmlspecialchars($error_message) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" id="productForm">
                        <div class="form-group">
                            <label class="form-label required" for="name">Product Name</label>
                            <input type="text" 
                                   name="name" 
                                   id="name"
                                   class="form-input" 
                                   value="<?= isset($name) ? htmlspecialchars($name) : '' ?>"
                                   required
                                   maxlength="255">
                            <div class="form-help">Enter a descriptive name for your product</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required" for="description">Description</label>
                            <textarea name="description" 
                                      id="description"
                                      class="form-input form-textarea" 
                                      required
                                      maxlength="1000"><?= isset($desc) ? htmlspecialchars($desc) : '' ?></textarea>
                            <div class="form-help">Provide a detailed description of your product (max 1000 characters)</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required" for="price">Price ($)</label>
                            <input type="number" 
                                   name="price" 
                                   id="price"
                                   class="form-input" 
                                   step="0.01" 
                                   min="0.01"
                                   value="<?= isset($price) ? htmlspecialchars($price) : '' ?>"
                                   required>
                            <div class="form-help">Enter the price in USD (e.g., 29.99)</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="image">Product Image</label>
                            <div class="file-input-wrapper">
                                <div class="file-input">
                                    <input type="file" 
                                           name="image" 
                                           id="image"
                                           accept="image/jpeg,image/png,image/gif,image/webp"
                                           onchange="previewImage(this)">
                                    <label for="image" class="file-input-label">
                                        <i class="fas fa-cloud-upload-alt file-input-icon"></i>
                                        <span class="file-input-text">Click to upload image</span>
                                        <span class="file-input-subtext">PNG, JPG, GIF, WebP up to 5MB</span>
                                    </label>
                                </div>
                            </div>
                            <div class="image-preview" id="imagePreview">
                                <img id="previewImg" src="" alt="Preview">
                            </div>
                            <div class="form-help">Upload a high-quality image of your product (optional)</div>
                        </div>

                        <div class="form-actions">
                            <a href="admin_dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-plus"></i> Add Product
                                <i class="fas fa-spinner fa-spin loading" id="loadingIcon"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Image preview functionality
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }

        // Form validation
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const loadingIcon = document.getElementById('loadingIcon');
            
            // Show loading state
            submitBtn.disabled = true;
            loadingIcon.style.display = 'inline-block';
            
            // Basic validation
            const name = document.getElementById('name').value.trim();
            const description = document.getElementById('description').value.trim();
            const price = document.getElementById('price').value;
            
            if (!name || !description || !price) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                submitBtn.disabled = false;
                loadingIcon.style.display = 'none';
                return;
            }
            
            if (parseFloat(price) <= 0) {
                e.preventDefault();
                alert('Price must be greater than 0.');
                submitBtn.disabled = false;
                loadingIcon.style.display = 'none';
                return;
            }
        });

        // Character counter for description
        const descriptionTextarea = document.getElementById('description');
        const maxLength = 1000;
        
        descriptionTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;
            
            let helpText = this.parentNode.querySelector('.form-help');
            if (remaining < 100) {
                helpText.innerHTML = `Provide a detailed description of your product (${remaining} characters remaining)`;
                helpText.style.color = remaining < 20 ? '#ef4444' : '#f59e0b';
            } else {
                helpText.innerHTML = 'Provide a detailed description of your product (max 1000 characters)';
                helpText.style.color = '#6b7280';
            }
        });

        // Auto-hide success message
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.opacity = '0';
                setTimeout(() => {
                    successAlert.remove();
                }, 300);
            }, 5000);
        }
    </script>
</body>
</html>