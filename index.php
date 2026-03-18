<?php
require_once(__DIR__ . '/config/db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memoria | Mortuary Operations System</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> 
        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }
 
        .home-header {
            background: rgba(27, 38, 59, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 40px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
 
        .hero {
            position: relative;
            background: linear-gradient(135deg, rgba(27, 38, 59, 0.9) 0%, rgba(65, 90, 119, 0.7) 100%), url('<?php echo BASE_URL; ?>assets/img/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 85vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 0 20px;
            margin-top: 0;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: -1px;
            opacity: 0;
            animation: fadeUp 1s ease-out 0.2s forwards;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            font-weight: 300;
            max-width: 700px;
            margin: 0 auto 40px;
            color: var(--cloud-gray);
            line-height: 1.8;
            opacity: 0;
            animation: fadeUp 1s ease-out 0.4s forwards;
        }
 
        .roles-container {
            max-width: 1200px;
            margin: -80px auto 50px;  
            padding: 0 20px;
            position: relative;
            z-index: 10;
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .role-card {
            background: white;
            padding: 40px 25px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            text-align: center;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            border-top: 5px solid var(--muted-teal);
            opacity: 0;
            transform: translateY(30px);
        }
 
        .role-card:nth-child(1) { animation: fadeUp 0.8s ease-out 0.6s forwards; }
        .role-card:nth-child(2) { animation: fadeUp 0.8s ease-out 0.8s forwards; border-top-color: var(--slate-blue); }
        .role-card:nth-child(3) { animation: fadeUp 0.8s ease-out 1.0s forwards; border-top-color: var(--deep-navy); }
        .role-card:nth-child(4) { animation: fadeUp 0.8s ease-out 1.2s forwards; border-top-color: #856404; }

        .role-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .role-icon {
            width: 70px;
            height: 70px;
            background: rgba(119, 141, 169, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: var(--slate-blue);
            transition: 0.3s;
        }

        .role-card:hover .role-icon {
            background: var(--slate-blue);
            color: white;
            transform: scale(1.1);
        }

        .role-card h3 {
            color: var(--deep-navy);
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .role-card p {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.6;
        }
 
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
 
        .home-footer {
            background: var(--deep-navy);
            color: var(--cloud-gray);
            text-align: center;
            padding: 30px;
            margin-top: 50px;
        }
    </style>
</head>
<body>
 
    <header class="home-header">
        <a href="<?php echo BASE_URL; ?>index.php" class="brand" style="color: white; text-decoration: none; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <img src="<?php echo BASE_URL; ?>assets/img/logo.png" alt="Memoria Logo" style="height: 40px; border-radius: 8px;">
            MEMORIA
        </a>
        <a href="<?php echo BASE_URL; ?>auth/login.php" class="btn" style="background: white; color: var(--deep-navy); font-weight: 600; padding: 10px 25px; border-radius: 30px; transition: 0.3s;">
            <i class="fas fa-sign-in-alt"></i> Access Portal
        </a>
    </header>
 
    <div class="hero">
        <h1 class="hero-title">A Digital Blueprint for Dignified Operations</h1>
        <p class="hero-subtitle">Transforming paper-based friction into connected, seamless, and compassionate death care management for Taguig City.</p>
    </div>
 
    <div class="roles-container">
        <div class="roles-grid">
            
            <div class="role-card">
                <div class="role-icon"><i class="fas fa-user-shield"></i></div>
                <h3>Administrator</h3>
                <p>Oversees all operations, manages user accounts, monitors audit trails, and generates financial reports.</p>
            </div>

            <div class="role-card">
                <div class="role-icon"><i class="fas fa-concierge-bell"></i></div>
                <h3>Front Desk Staff</h3>
                <p>Manages client schedules, processes billing and installments, and ensures legal document compliance.</p>
            </div>

            <div class="role-card">
                <div class="role-icon"><i class="fas fa-boxes"></i></div>
                <h3>Inventory Clerk</h3>
                <p>Tracks caskets, urns, and supplies. Receives automated low-stock alerts to prevent service delays.</p>
            </div>

            <div class="role-card">
                <div class="role-icon"><i class="fas fa-route"></i></div>
                <h3>Fleet Coordinator</h3>
                <p>Assigns drivers and dispatches vehicles. Monitors real-time hearse availability and routing.</p>
            </div>

        </div>
    </div>
    
    <footer class="home-footer">
        <p>&copy; <?php echo date('Y'); ?> Memoria: Mortuary Operations & Inventory Management System</p>
        <p style="font-size: 0.8rem; opacity: 0.7; margin-top: 5px;">Technological University of the Philippines - Taguig | BSIT-NS-2A Group 1</p>
    </footer>

</body>
</html>