<?php
include 'db.php';

// Initialize variables
$products = [];
$categories = [];
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build the products query with filters
$products_query = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

// Add search filter
if (!empty($search_query)) {
    $products_query .= " AND (name LIKE ? OR description LIKE ?)";
    $search_param = "%" . $search_query . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

// Add category filter
if (!empty($category_filter)) {
    $products_query .= " AND name LIKE ?";
    $params[] = "%" . $category_filter . "%";
    $types .= "s";
}

// Add sorting
switch ($sort_by) {
    case 'price_low':
        $products_query .= " ORDER BY price ASC";
        break;
    case 'price_high':
        $products_query .= " ORDER BY price DESC";
        break;
    case 'name':
        $products_query .= " ORDER BY name ASC";
        break;
    case 'oldest':
        $products_query .= " ORDER BY id ASC";
        break;
    default:
        $products_query .= " ORDER BY id DESC";
}

// Execute the query
try {
    if (!empty($params)) {
        $stmt = $conn->prepare($products_query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $products_result = $stmt->get_result();
    } else {
        $products_result = $conn->query($products_query);
    }
    
    if ($products_result && $products_result->num_rows > 0) {
        while ($row = $products_result->fetch_assoc()) {
            $products[] = $row;
        }
    }
} catch (Exception $e) {
    error_log("Error fetching products: " . $e->getMessage());
}

// Get categories for filter dropdown
try {
    $categories_query = "SELECT DISTINCT SUBSTRING_INDEX(name, ' ', 1) as category FROM products ORDER BY category";
    $categories_result = $conn->query($categories_query);
    if ($categories_result && $categories_result->num_rows > 0) {
        while ($row = $categories_result->fetch_assoc()) {
            if (!empty($row['category'])) {
                $categories[] = $row['category'];
            }
        }
    }
} catch (Exception $e) {
    error_log("Error fetching categories: " . $e->getMessage());
}

// Get total product count for statistics
$total_products = 0;
try {
    $count_result = $conn->query("SELECT COUNT(*) as total FROM products");
    if ($count_result) {
        $total_products = $count_result->fetch_assoc()['total'];
    }
} catch (Exception $e) {
    error_log("Error getting product count: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopHub - Your Premium Shopping Destination</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --accent-color: #f59e0b;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --success-color: #10b981;
            --danger-color: #ef4444;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--dark-color);
            padding-top: 76px;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
            transform: translateY(-2px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: var(--primary-color);
            transition: all 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
            left: 0;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 60vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><radialGradient id="a" cx=".5" cy=".5" r=".5"><stop offset="0" stop-color="%23ffffff" stop-opacity=".1"/><stop offset="1" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="20" cy="20" r="20" fill="url(%23a)"/><circle cx="80" cy="80" r="25" fill="url(%23a)"/></svg>');
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: slideInUp 1s ease;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            animation: slideInUp 1s ease 0.2s both;
        }

        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--accent-color), #f97316);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
            animation: slideInUp 1s ease 0.4s both;
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(245, 158, 11, 0.4);
            background: linear-gradient(135deg, #f97316, var(--accent-color));
        }

        .search-filters {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            margin: -50px 0 3rem 0;
            position: relative;
            z-index: 10;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        .product-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
            overflow: hidden;
            background: white;
            height: 100%;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .product-image {
            height: 250px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: var(--primary-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--success-color);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .product-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .product-rating {
            color: var(--accent-color);
        }

        .stats-section {
            background: linear-gradient(135deg, var(--dark-color), #374151);
            color: white;
            padding: 4rem 0;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--accent-color);
            display: block;
        }

        .no-products {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .no-products i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }

        .filter-badge {
            background: var(--primary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin: 0 5px 10px 0;
            display: inline-block;
        }

        .clear-filters {
            color: var(--danger-color);
            text-decoration: none;
            font-weight: 500;
        }

        .clear-filters:hover {
            color: var(--danger-color);
            text-decoration: underline;
        }

        .cart-notification {
            position: fixed;
            top: 100px;
            right: 20px;
            background: var(--primary-color);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            transform: translateX(400px);
            transition: all 0.3s ease;
            z-index: 1050;
        }

        .cart-notification.show {
            transform: translateX(0);
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .quantity-btn:hover {
            background: var(--secondary-color);
            transform: scale(1.1);
        }

        .cart-total {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-color);
            border-top: 2px solid #e5e7eb;
            padding-top: 15px;
            margin-top: 15px;
        }

        .footer {
            background: var(--dark-color);
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 4rem;
        }

        .footer-link {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-link:hover {
            color: var(--primary-color);
            transform: translateX(5px);
        }

        .social-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            margin: 0 0.5rem;
        }

        .social-icon:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
            color: white;
        }

        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .truncate-text {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-shopping-bag me-2"></i>ShopHub</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Log Out</a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <a href="#" class="nav-link me-3 position-relative" data-bs-toggle="modal" data-bs-target="#cartModal">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">0</span>
                    </a>
                    <a href="profile.php" class="nav-link"><i class="fas fa-user"></i></a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="hero-content">
                        <h1 class="hero-title">Discover Amazing Products</h1>
                        <p class="hero-subtitle">Shop the latest trends with unbeatable prices and premium quality. Your perfect shopping experience awaits.</p>
                        <a href="#products" class="btn btn-primary-custom btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i>Shop Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search and Filters -->
    <section class="py-5">
        <div class="container">
            <div class="search-filters">
                <form method="GET" action="" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Products</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Search by name or description..." 
                               value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>" 
                                        <?php echo $category_filter === $category ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(ucfirst($category)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="sort" class="form-label">Sort By</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="newest" <?php echo $sort_by === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="oldest" <?php echo $sort_by === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                            <option value="price_low" <?php echo $sort_by === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sort_by === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="name" <?php echo $sort_by === 'name' ? 'selected' : ''; ?>>Name A-Z</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary-custom w-100">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                    </div>
                </form>
                
                <!-- Active Filters Display -->
                <?php if (!empty($search_query) || !empty($category_filter)): ?>
                    <div class="mt-3">
                        <strong>Active Filters:</strong>
                        <?php if (!empty($search_query)): ?>
                            <span class="filter-badge">Search: "<?php echo htmlspecialchars($search_query); ?>"</span>
                        <?php endif; ?>
                        <?php if (!empty($category_filter)): ?>
                            <span class="filter-badge">Category: <?php echo htmlspecialchars($category_filter); ?></span>
                        <?php endif; ?>
                        <a href="?" class="clear-filters ms-2">
                            <i class="fas fa-times"></i> Clear All Filters
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number" data-target="<?php echo $total_products * 100; ?>">0</span>
                        <p>Happy Customers</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number" data-target="<?php echo $total_products; ?>">0</span>
                        <p>Products Available</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number" data-target="50">0</span>
                        <p>Countries Served</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number" data-target="24">0</span>
                        <p>Support Hours</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section id="products" class="py-5">
        <div class="container">
            <h2 class="section-title fade-in">
                <?php 
                if (!empty($search_query) || !empty($category_filter)) {
                    echo "Search Results";
                } else {
                    echo "All Products";
                }
                ?>
                <small class="text-muted fs-6 d-block mt-2"><?php echo count($products); ?> products found</small>
            </h2>
            
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <i class="fas fa-box-open"></i>
                    <h3>
                        <?php if (!empty($search_query) || !empty($category_filter)): ?>
                            No Products Found
                        <?php else: ?>
                            No Products Available
                        <?php endif; ?>
                    </h3>
                    <p>
                        <?php if (!empty($search_query) || !empty($category_filter)): ?>
                            Try adjusting your search criteria or clearing the filters.
                        <?php else: ?>
                            Products added by the admin will appear here. Check back soon for amazing deals!
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($search_query) || !empty($category_filter)): ?>
                        <a href="?" class="btn btn-primary-custom mt-3">
                            <i class="fas fa-arrow-left me-2"></i>View All Products
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="row" id="products-container">
                    <?php foreach ($products as $index => $product): ?>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card product-card fade-in" data-product-id="<?php echo $product['id']; ?>">
                                <?php if ($index < 3): // Show "New" badge for first 3 products ?>
                                    <div class="product-badge">New</div>
                                <?php endif; ?>
                                
                                <div class="product-image">
                                    <?php if (!empty($product['image']) && file_exists("uploads/" . $product['image'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center; font-size: 4rem; color: var(--primary-color);">
                                            <i class="fas fa-box"></i>
                                        </div>
                                    <?php else: ?>
                                        <i class="fas fa-box"></i>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <div class="product-rating mb-2">
                                        <?php 
                                        $rating = rand(4, 5); // Random rating between 4-5 stars
                                        for ($i = 1; $i <= 5; $i++): 
                                        ?>
                                            <i class="fas fa-star<?php echo $i > $rating ? '-o' : ''; ?>"></i>
                                        <?php endfor; ?>
                                        <span class="ms-1">(<?php echo $rating; ?>.<?php echo rand(0, 9); ?>)</span>
                                    </div>
                                    <p class="card-text text-muted truncate-text">
                                        <?php echo htmlspecialchars($product['description'] ?? 'High-quality product with excellent features.'); ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="product-price">$<?php echo number_format($product['price'], 2); ?></span>
                                        <div class="btn-group">
                                            <button class="btn btn-primary-custom add-to-cart" 
                                                    data-product-id="<?php echo $product['id']; ?>"
                                                    data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                                    data-product-price="<?php echo $product['price']; ?>"
                                                    data-product-image="<?php echo !empty($product['image']) ? 'uploads/' . $product['image'] : ''; ?>"
                                                    title="Add to Cart">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" 
                                                    onclick="toggleWishlist(<?php echo $product['id']; ?>)"
                                                    title="Add to Wishlist">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="mb-3">
                        <i class="fas fa-shopping-bag me-2" style="color: var(--primary-color);"></i>
                        ShopHub
                    </h5>
                    <p class="text-muted">Your premier destination for quality products at unbeatable prices. We're committed to providing an exceptional shopping experience.</p>
                    <div class="mt-3">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link">Home</a></li>
                        <li><a href="#products" class="footer-link">Products</a></li>
                        <<li><a href="#" class="footer-link">About Us</a></li>
                        <li><a href="#" class="footer-link">Contact</a></li>
                        <li><a href="#" class="footer-link">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Categories</h6>
                    <ul class="list-unstyled">
                        <?php foreach (array_slice($categories, 0, 5) as $category): ?>
                            <li><a href="?category=<?php echo urlencode($category); ?>" class="footer-link"><?php echo htmlspecialchars(ucfirst($category)); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Customer Service</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link">Shipping Info</a></li>
                        <li><a href="#" class="footer-link">Returns</a></li>
                        <li><a href="#" class="footer-link">Size Guide</a></li>
                        <li><a href="#" class="footer-link">Support</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Contact Info</h6>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-phone me-2"></i>+1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope me-2"></i>info@shophub.com</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>123 Shopping St, City</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">&copy; 2025 ShopHub. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-muted">
                        <a href="#" class="footer-link me-3">Privacy Policy</a>
                        <a href="#" class="footer-link me-3">Terms of Service</a>
                        <a href="#" class="footer-link">Cookies</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">
                        <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="cart-items">
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                            <p>Your cart is empty</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="cart-total w-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Total: <span id="cart-total">$0.00</span></span>
                            <div>
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Continue Shopping</button>
                                <button type="button" class="btn btn-primary-custom" id="checkout-btn" disabled>
                                    <i class="fas fa-credit-card me-2"></i>Checkout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Notification -->
    <div class="cart-notification" id="cart-notification">
        <i class="fas fa-check-circle me-2"></i>
        <span>Product added to cart!</span>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Cart functionality
        let cart = JSON.parse(localStorage.getItem('shophub_cart')) || [];
        
        function updateCartUI() {
            const cartCount = document.getElementById('cart-count');
            const cartItems = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');
            const checkoutBtn = document.getElementById('checkout-btn');
            
            // Update cart count
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartCount.textContent = totalItems;
            cartCount.style.display = totalItems > 0 ? 'block' : 'none';
            
            // Update cart items display
            if (cart.length === 0) {
                cartItems.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                        <p>Your cart is empty</p>
                    </div>
                `;
                cartTotal.textContent = '$0.00';
                checkoutBtn.disabled = true;
            } else {
                let cartHTML = '';
                let total = 0;
                
                cart.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;
                    
                    cartHTML += `
                        <div class="cart-item border-bottom pb-3 mb-3">
                            <div class="row align-items-center">
                                <div class="col-3">
                                    ${item.image ? `<img src="${item.image}" class="img-fluid rounded" alt="${item.name}">` : `<div class="bg-light p-4 text-center rounded"><i class="fas fa-box fa-2x text-muted"></i></div>`}
                                </div>
                                <div class="col-6">
                                    <h6 class="mb-1">${item.name}</h6>
                                    <p class="text-muted mb-2">$${item.price.toFixed(2)} each</p>
                                    <div class="quantity-controls">
                                        <button class="quantity-btn" onclick="updateQuantity(${item.id}, -1)">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span class="mx-2">${item.quantity}</span>
                                        <button class="quantity-btn" onclick="updateQuantity(${item.id}, 1)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-3 text-end">
                                    <div class="fw-bold mb-2">$${itemTotal.toFixed(2)}</div>
                                    <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${item.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                cartItems.innerHTML = cartHTML;
                cartTotal.textContent = `$${total.toFixed(2)}`;
                checkoutBtn.disabled = false;
            }
            
            // Save to localStorage
            localStorage.setItem('shophub_cart', JSON.stringify(cart));
        }
        
        function addToCart(productId, productName, productPrice, productImage) {
            const existingItem = cart.find(item => item.id === productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    price: parseFloat(productPrice),
                    image: productImage,
                    quantity: 1
                });
            }
            
            updateCartUI();
            showCartNotification();
        }
        
        function updateQuantity(productId, change) {
            const item = cart.find(item => item.id === productId);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    removeFromCart(productId);
                } else {
                    updateCartUI();
                }
            }
        }
        
        function removeFromCart(productId) {
            cart = cart.filter(item => item.id !== productId);
            updateCartUI();
        }
        
        function showCartNotification() {
            const notification = document.getElementById('cart-notification');
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }
        
        function toggleWishlist(productId) {
            // Placeholder for wishlist functionality
            const btn = event.target.closest('button');
            const icon = btn.querySelector('i');
            
            if (icon.classList.contains('fas')) {
                icon.classList.remove('fas');
                icon.classList.add('far');
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-outline-danger');
            } else {
                icon.classList.remove('far');
                icon.classList.add('fas');
                btn.classList.remove('btn-outline-danger');
                btn.classList.add('btn-outline-secondary');
            }
        }
        
        // Add event listeners for "Add to Cart" buttons
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize cart UI
            updateCartUI();
            
            // Add to cart button handlers
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = parseInt(this.getAttribute('data-product-id'));
                    const productName = this.getAttribute('data-product-name');
                    const productPrice = this.getAttribute('data-product-price');
                    const productImage = this.getAttribute('data-product-image');
                    
                    addToCart(productId, productName, productPrice, productImage);
                });
            });
            
            // Checkout button handler
            document.getElementById('checkout-btn').addEventListener('click', function() {
                if (cart.length > 0) {
                    alert('Checkout functionality would be implemented here. Total: ' + document.getElementById('cart-total').textContent);
                    // Here you would typically redirect to a checkout page or process the order
                }
            });
            
            // Smooth scrolling for navigation links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
            
            // Animated counters for stats
            function animateCounters() {
                const counters = document.querySelectorAll('.stat-number');
                
                counters.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target'));
                    const increment = target / 100;
                    let current = 0;
                    
                    const updateCounter = () => {
                        if (current < target) {
                            current += increment;
                            counter.textContent = Math.floor(current);
                            requestAnimationFrame(updateCounter);
                        } else {
                            counter.textContent = target;
                        }
                    };
                    
                    updateCounter();
                });
            }
            
            // Intersection Observer for fade-in animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        
                        // Trigger counter animation for stats section
                        if (entry.target.classList.contains('stat-item')) {
                            setTimeout(animateCounters, 200);
                        }
                    }
                });
            }, observerOptions);
            
            // Observe all fade-in elements
            document.querySelectorAll('.fade-in').forEach(el => {
                observer.observe(el);
            });
            
            // Search form auto-submit on input change (with debounce)
            let searchTimeout;
            const searchInput = document.getElementById('search');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        // Auto-submit could be enabled here if desired
                        // this.form.submit();
                    }, 500);
                });
            }
            
            // Product card hover effects
            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // Navbar scroll effect
            window.addEventListener('scroll', function() {
                const navbar = document.querySelector('.navbar');
                if (window.scrollY > 100) {
                    navbar.style.background = 'rgba(255, 255, 255, 0.98)';
                    navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
                } else {
                    navbar.style.background = 'rgba(255, 255, 255, 0.95)';
                    navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
                }
            });
        });
        
        // Handle form submissions with loading states
        document.addEventListener('submit', function(e) {
            if (e.target.matches('form')) {
                const submitBtn = e.target.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
                    submitBtn.disabled = true;
                    
                    // Re-enable after a short delay (for demonstration)
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 2000);
                }
            }
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + K to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                document.getElementById('search').focus();
            }
            
            // Escape to close modals
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal.show');
                if (openModal) {
                    bootstrap.Modal.getInstance(openModal).hide();
                }
            }
        });
        
        // Add loading states for images
        document.querySelectorAll('.product-image img').forEach(img => {
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });
            
            img.addEventListener('error', function() {
                this.style.display = 'none';
                this.nextElementSibling.style.display = 'flex';
            });
        });
    </script>
</body>
</html>