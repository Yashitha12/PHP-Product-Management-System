<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
include 'db.php';

// Check if product ID is provided
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    $_SESSION['error_message'] = "Invalid product ID.";
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch product details first
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    $_SESSION['error_message'] = "Product not found.";
    header("Location: admin_dashboard.php");
    exit();
}

// Handle confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Delete the product from database
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        
        if ($stmt->execute()) {
            // Delete associated image file if it exists
            if (!empty($product['image'])) {
                $image_path = "uploads/" . $product['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            // Commit transaction
            $conn->commit();
            $_SESSION['success_message'] = "Product '" . $product['name'] . "' has been deleted successfully.";
        } else {
            throw new Exception("Failed to delete product from database.");
        }
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        $_SESSION['error_message'] = "Failed to delete product: " . $e->getMessage();
    }
    
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product - Admin Dashboard</title>
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
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 1.5rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .container {
            max-width: 600px;
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

        .delete-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .delete-header {
            background: #fef2f2;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #fecaca;
            border-left: 4px solid #ef4444;
        }

        .delete-header h2 {
            color: #991b1b;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .delete-body {
            padding: 2rem;
        }

        .warning-message {
            background-color: #fef3cd;
            border: 1px solid #fde68a;
            color: #92400e;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .warning-icon {
            color: #f59e0b;
            font-size: 1.2rem;
            margin-top: 0.1rem;
        }

        .product-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .product-info h3 {
            color: #1e293b;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .product-details {
            display: grid;
            gap: 0.75rem;
        }

        .product-detail {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .product-detail:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: #4b5563;
            min-width: 100px;
        }

        .detail-value {
            flex: 1;
            text-align: right;
            color: #1f2937;
        }

        .product-image {
            max-width: 100px;
            max-height: 100px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .price {
            font-weight: 600;
            color: #059669;
            font-size: 1.1rem;
        }

        .description {
            max-width: 300px;
            word-wrap: break-word;
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

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
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

        .loading {
            display: none;
            margin-left: 0.5rem;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .delete-body {
                padding: 1.5rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                justify-content: center;
            }

            .product-detail {
                flex-direction: column;
                gap: 0.25rem;
            }

            .detail-value {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="container">
            <div class="header-content">
                <h1><i class="fas fa-trash-alt"></i> Delete Product</h1>
                <div class="breadcrumb">
                    <a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Delete Product</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="delete-container">
                <div class="delete-header">
                    <h2>
                        <i class="fas fa-exclamation-triangle"></i>
                        Confirm Product Deletion
                    </h2>
                </div>
                
                <div class="delete-body">
                    <div class="warning-message">
                        <i class="fas fa-exclamation-triangle warning-icon"></i>
                        <div>
                            <strong>Warning:</strong> This action cannot be undone. The product and all associated data will be permanently deleted from the system.
                        </div>
                    </div>

                    <div class="product-info">
                        <h3>Product Information</h3>
                        <div class="product-details">
                            <div class="product-detail">
                                <span class="detail-label">ID:</span>
                                <span class="detail-value">#<?php echo htmlspecialchars($product['id']); ?></span>
                            </div>
                            
                            <div class="product-detail">
                                <span class="detail-label">Name:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($product['name']); ?></span>
                            </div>
                            
                            <?php if (!empty($product['price'])): ?>
                            <div class="product-detail">
                                <span class="detail-label">Price:</span>
                                <span class="detail-value price">$<?php echo number_format($product['price'], 2); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($product['category'])): ?>
                            <div class="product-detail">
                                <span class="detail-label">Category:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($product['category']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($product['description'])): ?>
                            <div class="product-detail">
                                <span class="detail-label">Description:</span>
                                <span class="detail-value description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($product['image'])): ?>
                            <div class="product-detail">
                                <span class="detail-label">Image:</span>
                                <span class="detail-value">
                                    <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         class="product-image">
                                </span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($product['created_at'])): ?>
                            <div class="product-detail">
                                <span class="detail-label">Created:</span>
                                <span class="detail-value"><?php echo date('M j, Y g:i A', strtotime($product['created_at'])); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <form method="POST" id="deleteForm">
                        <div class="form-actions">
                            <a href="admin_dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Cancel
                            </a>
                            <button type="submit" name="confirm_delete" class="btn btn-danger" onclick="return confirmDelete()">
                                <i class="fas fa-trash-alt"></i>
                                Delete Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            const productName = "<?php echo addslashes($product['name']); ?>";
            const confirmed = confirm(`Are you absolutely sure you want to delete "${productName}"?\n\nThis action cannot be undone.`);
            
            if (confirmed) {
                // Show loading state
                const deleteBtn = document.querySelector('.btn-danger');
                
                deleteBtn.disabled = true;
                deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                
                return true;
            }
            
            return false;
        }

        // Add keyboard shortcut for quick navigation
        document.addEventListener('keydown', function(e) {
            // Escape key to cancel
            if (e.key === 'Escape') {
                window.location.href = 'admin_dashboard.php';
            }
        });

        // Prevent accidental form submission on page refresh
        window.addEventListener('beforeunload', function(e) {
            const form = document.getElementById('deleteForm');
            if (form.querySelector('.btn-danger').disabled) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    </script>
</body>
</html>