
# 🛒 PHP Product Management System

A lightweight, modular **Product Management System** built with PHP and MySQL. This project provides **user authentication**, **admin dashboard capabilities**, and **CRUD operations** for managing product data. It is ideal for learning or rapid prototyping.

---

## 🚀 Features

- 🔐 **User & Admin Authentication**
- 👤 **User Registration, Login, Logout**
- 📦 **Admin Dashboard for Product CRUD**
- 📤 **Product Image Uploads**
- 🧩 Modular Codebase (Separation of Concerns)
- 🛡️ Basic Session Management & Access Control

---

## 🛠️ Tech Stack

| Layer        | Technology |
|--------------|------------|
| Backend      | PHP 7.x+   |
| Database     | MySQL / MariaDB |
| Frontend     | HTML5, CSS3 |
| Web Server   | Apache / PHP Built-in Server |

---

## ⚙️ Getting Started

### 1. Clone the Repository

```bash
git clone [https://github.com/yourusername/php-product-management.git](https://github.com/Yashitha12/PHP-Product-Management-System.git)
cd php-product-management
```

### 2. Import Database

Import the `user_auth.sql` file into your MySQL server:

```bash
mysql -u root -p
CREATE DATABASE product_system;
USE product_system;
SOURCE user_auth.sql;
```

Or use phpMyAdmin to upload the SQL file.

### 3. Configure Database Connection

Update the `db.php` file with your MySQL credentials:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "product_system";
```

### 4. Start the Server

Use PHP’s built-in server for local testing:

```bash
php -S localhost:8000
```

Navigate to `http://localhost:8000` in your browser.

---

## 📁 Project Structure

```
php-product-management/
│
├── .qodo/                     # System metadata (ignore)
├── uploads/                   # Uploaded product images
├── db.php                     # DB connection logic
├── login.php                  # User login logic
├── register.php               # User registration logic
├── logout.php                 # Session termination
├── home.php                   # Post-login landing page
├── profile.php                # User profile view
├── welcome.php                # Welcome screen
│
├── admin_login.php            # Admin login interface
├── admin_dashboard.php        # Admin-only dashboard
├── add_product.php            # Form to add a product
├── edit_product.php           # Form to update product details
├── delete_product.php         # Logic to delete a product
│
└── user_auth.sql              # SQL dump of user/product tables
```

---

## 🧾 Database Schema Overview

The provided `user_auth.sql` includes:

- `users` table: Handles user credentials and roles
- `products` table: Stores product metadata (name, image, price, etc.)

> Ensure image upload permissions are correctly set for the `uploads/` directory.

---

## 👤 Roles & Access

- **User**
  - Register, Login, View Profile
  - Access `home.php`, `profile.php`
- **Admin**
  - Login via `admin_login.php`
  - Access to CRUD operations via `admin_dashboard.php`

---

## ✅ To-Do / Future Enhancements

- [ ] Add CSRF protection
- [ ] Input validation & sanitization
- [ ] Pagination for product list
- [ ] Add product categories & tags
- [ ] Role-based permissions

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).

---

## ✍️ Author

Yashitha Dissanayaka   
GitHub: https://github.com/Yashitha12
Email: yashithadissanayaka6@gmail.com

---
