<?php
session_start();

// If user is already logged in, redirect to their appropriate dashboard
if(isset($_SESSION['user_id']) && isset($_SESSION['role'])){
    if($_SESSION['role'] == 'admin'){
        header("Location: admin_dashboard.php");
        exit;
    } elseif($_SESSION['role'] == 'staff'){
        header("Location: staff_dashboard.php");
        exit;
    } elseif($_SESSION['role'] == 'client'){
        header("Location: client_dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allergen Checker - Food Ordering & Allergy Management System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }

        .navbar-links {
            display: flex;
            gap: 15px;
        }

        .navbar-links a {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .navbar-links a.login-btn {
            background: #667eea;
            color: white;
        }

        .navbar-links a.login-btn:hover {
            background: #764ba2;
        }

        .navbar-links a.register-btn {
            background: #28a745;
            color: white;
        }

        .navbar-links a.register-btn:hover {
            background: #218838;
        }

        .hero-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            text-align: center;
            color: white;
        }

        .hero-content {
            max-width: 800px;
        }

        .hero-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .hero-content h1 {
            font-size: 48px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .hero-content p {
            font-size: 20px;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-btn {
            padding: 15px 40px;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
            font-weight: bold;
            display: inline-block;
        }

        .cta-btn-primary {
            background: #28a745;
            color: white;
        }

        .cta-btn-primary:hover {
            background: #218838;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .cta-btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .cta-btn-secondary:hover {
            background: #667eea;
            color: white;
            transform: translateY(-3px);
        }

        .features-section {
            background: white;
            padding: 80px 20px;
            margin-top: 40px;
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 36px;
            color: #333;
            margin-bottom: 50px;
            font-weight: bold;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .feature-card {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 22px;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        .user-types {
            background: #f5f5f5;
            padding: 80px 20px;
        }

        .user-types-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .user-types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .user-type-card {
            background: white;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            border-top: 4px solid #667eea;
            transition: 0.3s;
        }

        .user-type-card:hover {
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
        }

        .user-type-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .user-type-card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 24px;
        }

        .user-type-card ul {
            list-style: none;
            color: #666;
            line-height: 2;
        }

        .user-type-card ul li:before {
            content: "✓ ";
            color: #28a745;
            font-weight: bold;
            margin-right: 10px;
        }

        .footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 30px 20px;
            margin-top: auto;
        }

        .footer p {
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
            }

            .navbar-links {
                width: 100%;
                justify-content: center;
            }

            .hero-content h1 {
                font-size: 36px;
            }

            .hero-content p {
                font-size: 16px;
            }

            .cta-btn {
                padding: 12px 30px;
                font-size: 16px;
            }

            .section-title {
                font-size: 28px;
            }

            .cta-buttons {
                flex-direction: column;
            }

            .cta-buttons a {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-brand">🍔 Allergen Checker</div>
        <div class="navbar-links">
            <a href="login.php" class="login-btn">Login</a>
            <a href="client_register.php" class="register-btn">Register as Client</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <div class="hero-icon">🍽️</div>
            <h1>Allergen Checker System</h1>
            <p>Safe Food Ordering with Real-time Allergen Detection</p>
            <p style="font-size: 16px; margin-bottom: 40px; opacity: 0.9;">Order food with confidence knowing which items are safe for your allergies</p>
            
            <div class="cta-buttons">
                <a href="login.php" class="cta-btn cta-btn-primary">Login</a>
                <a href="client_register.php" class="cta-btn cta-btn-secondary">Create Account</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="features-container">
            <h2 class="section-title">Key Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">⚠️</div>
                    <h3>Real-time Allergen Detection</h3>
                    <p>Foods containing your allergies are automatically highlighted in red, keeping you safe</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👤</div>
                    <h3>Complete Profile Management</h3>
                    <p>Manage your personal information, contact details, and allergy list easily</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🛒</div>
                    <h3>Easy Food Ordering</h3>
                    <p>Browse menu, select multiple items, and place orders with just a few clicks</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📦</div>
                    <h3>Order Tracking</h3>
                    <p>Track your orders in real-time and see the status at every step</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Payment & Completion</h3>
                    <p>Staff handle payments and mark orders complete for seamless experience</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Admin Dashboard</h3>
                    <p>Complete system management with sales reports and user administration</p>
                </div>
            </div>
        </div>
    </section>

    <!-- User Types Section -->
    <section class="user-types">
        <div class="user-types-container">
            <h2 class="section-title">For Everyone</h2>
            <div class="user-types-grid">
                <div class="user-type-card">
                    <div class="user-type-icon">👥</div>
                    <h3>Clients/Food Seekers</h3>
                    <ul>
                        <li>Register and create account</li>
                        <li>Manage allergies</li>
                        <li>Browse safe food options</li>
                        <li>Place & track orders</li>
                        <li>View order history</li>
                    </ul>
                </div>

                <div class="user-type-card">
                    <div class="user-type-icon">👨‍💼</div>
                    <h3>Staff/Cashiers</h3>
                    <ul>
                        <li>View pending orders</li>
                        <li>See customer allergies</li>
                        <li>Accept orders</li>
                        <li>Process payments</li>
                        <li>Mark orders complete</li>
                    </ul>
                </div>

                <div class="user-type-card">
                    <div class="user-type-icon">⚙️</div>
                    <h3>Administrators</h3>
                    <ul>
                        <li>Manage food menu</li>
                        <li>Upload food images</li>
                        <li>Manage user accounts</li>
                        <li>View sales reports</li>
                        <li>Monitor all orders</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p><strong>Allergen Checker System</strong> - Version 1.0</p>
        <p>Safe Food Ordering with Allergy Management</p>
        <p style="font-size: 12px; margin-top: 15px; opacity: 0.8;">© 2025 All Rights Reserved | Developed by: Dominic Mari Luis A. Con-ui, Cris Allen Oroc, Ian Bristan M. Pana, Kim Joshua T. Lopez, Jeramae Pace</p>
    </footer>
</body>
</html>
