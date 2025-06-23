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
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--dark-color);
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
            min-height: 80vh;
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
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: slideInUp 1s ease;
        }

        .hero-subtitle {
            font-size: 1.3rem;
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
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .product-rating {
            color: var(--accent-color);
        }

        .category-card {
            background: linear-gradient(135deg, var(--light-color), #ffffff);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.4s ease;
            border: 2px solid transparent;
            cursor: pointer;
        }

        .category-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary-color);
            background: linear-gradient(135deg, #ffffff, var(--light-color));
        }

        .category-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .category-card:hover .category-icon {
            transform: scale(1.2);
            color: var(--secondary-color);
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
            counter-reset: stat-counter;
            animation: countUp 2s ease-in-out;
        }

        .footer {
            background: var(--dark-color);
            color: white;
            padding: 3rem 0 1rem;
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

        .newsletter-input {
            border: 2px solid transparent;
            border-radius: 50px;
            padding: 12px 20px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        .newsletter-input:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
        }

        .newsletter-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
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
                        <a class="nav-link" href="#categories">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Log In</a>
                    </li>
                </ul>
                
                <!-- <div class="d-flex align-items-center">
                    <a href="#" class="nav-link me-3"><i class="fas fa-search"></i></a>
                    <a href="#" class="nav-link me-3 position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                    </a>
                    <a href="#" class="nav-link"><i class="fas fa-user"></i></a>
                </div> -->
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">Discover Amazing Products</h1>
                        <p class="hero-subtitle">Shop the latest trends with unbeatable prices and premium quality. Your perfect shopping experience awaits.</p>
                        <a href="#products" class="btn btn-primary-custom btn-lg">
                            <i class="fas fa-shopping-bag me-2"></i>Shop Now
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="fas fa-shopping-cart" style="font-size: 15rem; color: rgba(255,255,255,0.2);"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number" data-target="10000">0</span>
                        <p>Happy Customers</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number" data-target="5000">0</span>
                        <p>Products</p>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item fade-in">
                        <span class="stat-number" data-target="50">0</span>
                        <p>Countries</p>
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

    <!-- Featured Products -->
    <section id="products" class="py-5">
        <div class="container">
            <h2 class="section-title fade-in">Featured Products</h2>
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card product-card fade-in">
                        <div class="product-image">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Premium Laptop</h5>
                            <div class="product-rating mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <span class="ms-1">(4.9)</span>
                            </div>
                            <p class="card-text text-muted">High-performance laptop for professionals</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">$999</span>
                                <button class="btn btn-primary-custom">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card product-card fade-in">
                        <div class="product-image">
                            <i class="fas fa-headphones"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Wireless Headphones</h5>
                            <div class="product-rating mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <span class="ms-1">(4.2)</span>
                            </div>
                            <p class="card-text text-muted">Premium sound quality headphones</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">$199</span>
                                <button class="btn btn-primary-custom">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card product-card fade-in">
                        <div class="product-image">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Smartphone Pro</h5>
                            <div class="product-rating mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <span class="ms-1">(4.8)</span>
                            </div>
                            <p class="card-text text-muted">Latest flagship smartphone</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">$799</span>
                                <button class="btn btn-primary-custom">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card product-card fade-in">
                        <div class="product-image">
                            <i class="fas fa-camera"></i>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Digital Camera</h5>
                            <div class="product-rating mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <span class="ms-1">(4.6)</span>
                            </div>
                            <p class="card-text text-muted">Professional photography camera</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">$1299</span>
                                <button class="btn btn-primary-custom">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section id="categories" class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title fade-in">Shop by Category</h2>
            <div class="row">
                <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                    <div class="category-card fade-in">
                        <div class="category-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <h5>Electronics</h5>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                    <div class="category-card fade-in">
                        <div class="category-icon">
                            <i class="fas fa-tshirt"></i>
                        </div>
                        <h5>Fashion</h5>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                    <div class="category-card fade-in">
                        <div class="category-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <h5>Home & Garden</h5>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                    <div class="category-card fade-in">
                        <div class="category-icon">
                            <i class="fas fa-gamepad"></i>
                        </div>
                        <h5>Gaming</h5>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                    <div class="category-card fade-in">
                        <div class="category-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h5>Books</h5>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                    <div class="category-card fade-in">
                        <div class="category-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <h5>Sports</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="py-5" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
        <div class="container">
            <div class="row justify-content-center text-center text-white">
                <div class="col-lg-6">
                    <h3 class="mb-3">Stay Updated</h3>
                    <p class="mb-4">Subscribe to our newsletter for exclusive deals and latest updates</p>
                    <form class="d-flex gap-2" id="newsletterForm">
                        <input type="email" class="form-control newsletter-input flex-grow-1" placeholder="Enter your email">
                        <button type="submit" class="btn btn-primary-custom">Subscribe</button>
                    </form>
                </div>
            </div>
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
                        <li><a href="#" class="footer-link">Products</a></li>
                        <li><a href="#" class="footer-link">Categories</a></li>
                        <li><a href="#" class="footer-link">About Us</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Customer Service</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link">Contact Us</a></li>
                        <li><a href="#" class="footer-link">FAQ</a></li>
                        <li><a href="#" class="footer-link">Shipping Info</a></li>
                        <li><a href="#" class="footer-link">Returns</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Account</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link">My Account</a></li>
                        <li><a href="#" class="footer-link">Order History</a></li>
                        <li><a href="#" class="footer-link">Wishlist</a></li>
                        <li><a href="#" class="footer-link">Track Order</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Contact Info</h6>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-phone me-2"></i>+1 (555) 123-4567</li>
                        <li><i class="fas fa-envelope me-2"></i>info@shophub.com</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>123 Shop Street, City</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: #374151;">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; 2025 ShopHub. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="footer-link me-3">Privacy Policy</a>
                    <a href="#" class="footer-link me-3">Terms of Service</a>
                    <a href="#" class="footer-link">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
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

        // Navbar background change on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            }
        });

        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Counter animation for stats
        function animateCounter(element) {
            const target = parseInt(element.dataset.target);
            const increment = target / 100;
            let current = 0;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current).toLocaleString();
            }, 20);
        }

        // Start counter animation when stats section is visible
        const statsObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('.stat-number');
                    counters.forEach(counter => {
                        if (!counter.classList.contains('animated')) {
                            counter.classList.add('animated');
                            animateCounter(counter);
                        }
                    });
                }
            });
        }, observerOptions);

        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            statsObserver.observe(statsSection);
        }

        // Newsletter form submission
        document.getElementById('newsletterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            if (email) {
                // Simulate API call
                const button = this.querySelector('button');
                const originalText = button.textContent;
                button.textContent = 'Subscribing...';
                button.disabled = true;
                
                setTimeout(() => {
                    button.textContent = 'Subscribed!';
                    button.style.background = '#10b981';
                    this.querySelector('input').value = '';
                    
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.disabled = false;
                        button.style.background = '';
                    }, 2000);
                }, 1000);
            }
        });

        // Add to cart functionality
        document.querySelectorAll('.product-card .btn-primary-custom').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Animation effect
                this.innerHTML = '<i class="fas fa-check"></i>';
                this.style.background = '#10b981';
                
                // Update cart badge
                const cartBadge = document.querySelector('.badge');
                const currentCount = parseInt(cartBadge.textContent);
                cartBadge.textContent = currentCount + 1;
                
                // Add shake animation to cart icon
                const cartIcon = document.querySelector('.fa-shopping-cart').parentElement;
                cartIcon.style.animation = 'none';
                setTimeout(() => {
                    cartIcon.style.animation = 'shake 0.5s ease-in-out';
                }, 10);
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-cart-plus"></i>';
                    this.style.background = '';
                }, 2000);
            });
        });

        // Category card click effects
        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', function() {
                // Remove active class from all cards
                document.querySelectorAll('.category-card').forEach(c => c.classList.remove('active'));
                // Add active class to clicked card
                this.classList.add('active');
                
                // You could add navigation logic here
                console.log('Category selected:', this.querySelector('h5').textContent);
            });
        });

        // Search functionality (basic implementation)
        document.querySelector('.fa-search').parentElement.addEventListener('click', function(e) {
            e.preventDefault();
            const searchQuery = prompt('What are you looking for?');
            if (searchQuery) {
                console.log('Searching for:', searchQuery);
                // You could implement actual search functionality here
                alert(`Searching for "${searchQuery}"... (This is a demo)`);
            }
        });

        // User account dropdown simulation
        document.querySelector('.fa-user').parentElement.addEventListener('click', function(e) {
            e.preventDefault();
            // Simulate login/account access
            const isLoggedIn = Math.random() > 0.5; // Random simulation
            if (isLoggedIn) {
                alert('Welcome back! (This is a demo)');
            } else {
                alert('Please log in to access your account (This is a demo)');
            }
        });

        // Shopping cart click handler
        document.querySelector('.fa-shopping-cart').parentElement.addEventListener('click', function(e) {
            e.preventDefault();
            const itemCount = document.querySelector('.badge').textContent;
            alert(`You have ${itemCount} items in your cart (This is a demo)`);
        });

        // Add shake animation for cart
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
            
            .category-card.active {
                background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
                color: white;
                transform: translateY(-8px);
            }
            
            .category-card.active .category-icon {
                color: white;
            }
        `;
        document.head.appendChild(style);

        // Lazy loading simulation for product images
        const productImages = document.querySelectorAll('.product-image');
        productImages.forEach(img => {
            img.addEventListener('mouseenter', function() {
                // Simulate loading effect
                const icon = this.querySelector('i');
                icon.style.animation = 'pulse 1s infinite';
            });
            
            img.addEventListener('mouseleave', function() {
                const icon = this.querySelector('i');
                icon.style.animation = 'none';
            });
        });

        // Add pulse animation
        const pulseStyle = document.createElement('style');
        pulseStyle.textContent = `
            @keyframes pulse {
                0% { opacity: 1; }
                50% { opacity: 0.5; }
                100% { opacity: 1; }
            }
        `;
        document.head.appendChild(pulseStyle);

        // Initialize tooltips if needed (Bootstrap tooltips)
        // Uncomment if you want to add tooltips
        /*
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        */

        // Performance optimization: Debounce scroll events
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Apply debounce to scroll event
        const debouncedScroll = debounce(() => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            }
        }, 10);

        window.removeEventListener('scroll', window.addEventListener('scroll', debouncedScroll));
        window.addEventListener('scroll', debouncedScroll);

        console.log('ShopHub E-commerce Homepage Loaded Successfully! 🛒✨');
    </script>
</body>
</html>