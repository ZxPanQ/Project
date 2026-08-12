<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopFlow - Local Shop Inventory & Sales System</title>
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #2ecc71;
            --accent-hover: #27ae60;
            --bg-light: #f8f9fa;
            --text-dark: #333333;
            --text-muted: #666666;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Header / Navbar */
        header {
            background-color: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
        }

        .logo span {
            color: var(--accent);
        }

        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: var(--accent);
        }

        .btn-nav {
            background-color: var(--accent);
            color: var(--white) !important;
            padding: 8px 18px;
            border-radius: 4px;
            font-weight: bold;
        }

        .btn-nav:hover {
            background-color: var(--accent-hover);
        }

        /* Hero Section */
        .hero {
            max-width: 1200px;
            margin: 0 auto;
            padding: 5rem 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
        }

        .hero-text h1 {
            font-size: 2.8rem;
            color: var(--primary);
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .hero-text p {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        .hero-btns {
            display: flex;
            gap: 15px;
        }

        .btn-primary {
            background-color: var(--accent);
            color: var(--white);
            padding: 12px 28px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }

        .btn-primary:hover {
            background-color: var(--accent-hover);
        }

        .btn-secondary {
            background-color: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
            padding: 10px 26px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
        }

        .hero-image {
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 20px;
            border: 1px solid #eee;
        }

        /* Features Section */
        .features {
            background-color: var(--white);
            padding: 5rem 2rem;
        }

        .section-title {
            text-align: center;
            max-width: 600px;
            margin: 0 auto 3rem auto;
        }

        .section-title h2 {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .features-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .feature-card {
            background-color: var(--bg-light);
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #eef2f5;
            transition: transform 0.2s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            color: var(--primary);
            margin-bottom: 10px;
        }

        /* Footer */
        footer {
            background-color: var(--primary);
            color: var(--white);
            padding: 3rem 2rem 1.5rem 2rem;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 2rem;
        }

        .footer-bottom {
            max-width: 1200px;
            margin: 1.5rem auto 0 auto;
            text-align: center;
            font-size: 0.9rem;
            color: #bdc3c7;
        }

        /* Responsive Breakpoints */
        @media (max-width: 768px) {
            .hero {
                grid-template-columns: 1fr;
                text-align: center;
                padding: 3rem 1.5rem;
            }

            .hero-btns {
                justify-content: center;
            }

            .nav-links {
                display: none; /* Can be toggled with a mobile menu script */
            }
        }
    </style>
</head>
<body>

    <!-- Navigation Header -->
    <header>
        <div class="nav-container">
            <div class="logo">Shop<span>Flow</span></div>
            <nav class="nav-links">
                <a href="#features">Features</a>
                <a href="#about">About</a>
                <a href="login.php" class="btn-nav" style="background-color: #5e69ae;">Login</a>
                <a href="signup.php" class="btn-nav">Sign up</a>
            </nav>
        </div>
    </header>
    <section class="hero">
        <div class="hero-text">
            <h1>Complete Inventory & Sales Management for Local Shops</h1>
            <p>Track batches, calculate daily revenue, handle suppliers, and speed up POS checkout—all in one lightweight dashboard built for local retail businesses.</p>
            <div class="hero-btns">
                <a href="signup.php" class="btn-primary">Get Started</a>
                <a href="#features" class="btn-secondary">Explore Features</a>
            </div>
        </div>
        <div class="hero-image">
            <!-- Graphical Representation of System Dashboard -->
            <div style="background:#2c3e50; color:#fff; padding:15px; border-radius:4px 4px 0 0; font-weight:bold;">
                ShopFlow Dashboard
            </div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-top:15px;">
                <div style="background:#e8f8f5; padding:15px; border-radius:4px; border-left:4px solid #2ecc71;">
                    <small>Today's Sales</small>
                    <div style="font-size:1.4rem; font-weight:bold; color:#27ae60;">Rs. 12,450.00</div>
                </div>
                <div style="background:#fef9e7; padding:15px; border-radius:4px; border-left:4px solid #f1c40f;">
                    <small>Low Stock Alert</small>
                    <div style="font-size:1.4rem; font-weight:bold; color:#d35400;">3 Batches</div>
                </div>
            </div>
            <div style="margin-top:15px; background:#f8f9fa; padding:15px; border-radius:4px; border:1px solid #ddd;">
                <small style="color:#666;">Quick POS Checkout</small>
                <div style="height:8px; background:#e0e0e0; margin-top:8px; border-radius:4px;"></div>
                <div style="height:8px; background:#2ecc71; width:70%; margin-top:5px; border-radius:4px;"></div>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section class="features" id="features">
        <div class="section-title">
            <h2>Designed to Simplify Your Shop Operations</h2>
            <p>Everything you need to keep your stock aligned and transactions flowing seamlessly.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <h3>Inventory & Batch Tracking</h3>
                <p>Monitor active product batches, track remaining quantities, and ensure stock expiration dates never catch you off guard.</p>
            </div>

            <div class="feature-card">
                <h3>Fast Point of Sale (POS)</h3>
                <p>Select items by batch, auto-calculate subtotals, and process customer checkout transactions in seconds.</p>
            </div>

            <div class="feature-card">
                <h3>Supplier Management</h3>
                <p>Organize vendor contacts, edit supplier details instantly, and log stock purchases attached directly to specific suppliers.</p>
            </div>

            <div class="feature-card">
                <h3>Real-time Dashboard</h3>
                <p>Get immediate visibility into total product counts, low stock alerts, and overall daily revenue totals.</p>
            </div>
        </div>
    </section>
    <!-- About Section -->
    <section class="about" id="about" style="padding: 5rem 2rem; background-color: #f8f9fa;">
        <div class="section-title">
            <h2>Empowering Local Retailers</h2>
            <p>Building reliable, straightforward management tools designed specifically for neighborhood stores.</p>
        </div>

        <div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;">
            
            <!-- Story / Mission Text -->
            <div>
                <h3 style="font-size: 1.8rem; color: #2c3e50; margin-bottom: 15px;">Designed for Simplicity & Speed</h3>
                <p style="color: #666; margin-bottom: 15px;">
                    Managing a local retail store shouldn't require complex, bloated enterprise software. <strong>ShopFlow</strong> was created to bridge the gap between simple ledger books and modern digital management.
                </p>
                <p style="color: #666; margin-bottom: 20px;">
                    Our system provides store owners with a lightweight, intuitive platform to register batch stock, monitor product expirations, manage supplier relationships, and complete Point-of-Sale transactions without delay.
                </p>
                
                <div style="display: flex; gap: 20px; margin-top: 25px;">
                    <div style="border-left: 3px solid #2ecc71; padding-left: 15px;">
                        <h4 style="font-size: 1.5rem; color: #2c3e50;">100%</h4>
                        <small style="color: #666;">Focus on Local Retail</small>
                    </div>
                    <div style="border-left: 3px solid #2ecc71; padding-left: 15px;">
                        <h4 style="font-size: 1.5rem; color: #2c3e50;">Real-Time</h4>
                        <small style="color: #666;">Stock & Sales Sync</small>
                    </div>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eee;">
                    <p style="font-size: 0.9rem; color: #666;">Eliminate manual inventory calculation errors with real-time batch deductions.</p>
                </div>
                <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eee;">
                    <p style="font-size: 0.9rem; color: #666;">Streamlined checkout workflow to keep customer queues moving fast.</p>
                </div>
                <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eee;">
                    <p style="font-size: 0.9rem; color: #666;">Full visibility over batch numbers, expiration alerts, and supplier contacts.</p>
                </div>
                <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eee;">
                    <p style="font-size: 0.9rem; color: #666;">Instant daily sales summaries to track business performance effortlessly.</p>
                </div>
            </div>
        </div>
    </section>
    <footer>
        <div class="footer-container">
            <div>
                <div class="logo" style="color:#fff; margin-bottom:10px;">Shop<span>Flow</span></div>
                <p style="color:#bdc3c7; max-width:300px;">Streamlining daily operations for local vendors, retail outlets, and neighborhood stores.</p>
            </div>
            <div>
                <h4 style="margin-bottom:10px;">Quick Links</h4>
                <p><a href="login.php" style="color:#bdc3c7; text-decoration:none;">System Login</a></p>
                <p><a href="#features" style="color:#bdc3c7; text-decoration:none;">Features</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?php echo date('Y'); ?> Local Shop System. All rights reserved.
        </div>
    </footer>
</body>
</html>