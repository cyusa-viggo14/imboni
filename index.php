<?php
// index.php
// Single-file PHP implementation of the IMBONI School Management homepage.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMBONI - Smart School Management</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- CSS VARIABLES & RESET --- */
        :root {
            --primary-blue: #0b57d0;
            --dark-blue: #1a1f36;
            --text-gray: #555;
            --bg-color: #f0f6ff;
            --white: #ffffff;
            --font-main: 'Inter', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-main);
            background: linear-gradient(135deg, #eef5ff 0%, #dceaff 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            color: var(--dark-blue);
        }

        /* --- BACKGROUND ABSTRACT SHAPES --- */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.6;
        }

        .shape-1 {
            top: -100px;
            right: -100px;
            width: 500px;
            height: 500px;
            background: #b8d4ff;
        }

        .shape-2 {
            bottom: 0;
            left: -100px;
            width: 400px;
            height: 400px;
            background: #d4e4ff;
        }

        /* --- HEADER / NAVBAR --- */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            font-weight: 800;
            color: var(--primary-blue);
        }

        .logo i {
            font-size: 28px;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 40px;
        }

        nav a {
            text-decoration: none;
            color: var(--text-gray);
            font-weight: 500;
            font-size: 15px;
            transition: color 0.3s;
        }

        nav a:hover, nav a.active {
            color: var(--primary-blue);
        }

        nav a.active {
            border-bottom: 2px solid var(--primary-blue);
            padding-bottom: 5px;
        }

        .btn-signin {
            background-color: var(--primary-blue);
            color: var(--white);
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(11, 87, 208, 0.3);
            transition: transform 0.2s;
        }

        .btn-signin:hover {
            transform: translateY(-2px);
        }

        /* --- HERO SECTION --- */
        .hero-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 5%;
            gap: 50px;
        }

        .hero-content {
            flex: 1;
            max-width: 600px;
        }

        .badge {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.8);
            color: var(--primary-blue);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .badge i {
            background: var(--primary-blue);
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }

        .hero-content h1 {
            font-size: 3.8rem;
            line-height: 1.1;
            font-weight: 800;
            color: var(--dark-blue);
            margin-bottom: 25px;
            letter-spacing: -1px;
        }

        .hero-content h1 .highlight {
            color: var(--primary-blue);
            font-style: italic;
            font-family: 'Georgia', serif;
            font-weight: 600;
        }

        .description {
            font-size: 1.1rem;
            color: var(--text-gray);
            line-height: 1.6;
            margin-bottom: 35px;
            max-width: 500px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 15px;
            background: linear-gradient(90deg, #0b57d0 0%, #1a73e8 100%);
            color: white;
            text-decoration: none;
            padding: 8px 30px 8px 8px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            box-shadow: 0 10px 20px rgba(11, 87, 208, 0.3);
            transition: transform 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
        }

        .btn-primary .icon-circle {
            background: white;
            color: var(--primary-blue);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        /* --- HERO IMAGE SECTION (VIGGO.PNG) --- */
        .hero-image-wrapper {
            flex: 1;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .image-container {
            position: relative;
            width: 100%;
            max-width: 650px;
            height: 480px; /* Fixed height for exact framing */
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            /* Setting viggo.png as the background exactly as requested */
            background-image: url('viggo.png'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border: 4px solid rgba(255, 255, 255, 0.6); /* Slight white border to blend with design */
        }

        /* Curved connecting line (SVG overlay) */
        .connector-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        /* Floating Glassmorphism Cards (Exact positioning from original) */
        .floating-card {
            position: absolute;
            background: rgba(255, 255, 255, 0.25); /* Very transparent */
            backdrop-filter: blur(15px); /* Strong blur for glass effect */
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            padding: 12px 18px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            font-size: 13px;
            font-weight: 600;
            color: var(--dark-blue);
            line-height: 1.3;
            animation: float 6s ease-in-out infinite;
            z-index: 2;
            white-space: nowrap;
        }

        /* Top Left - Permissions */
        .card-permissions {
            top: 12%;
            left: 6%;
        }

        /* Top Right - Parents Notified */
        .card-parents {
            top: 15%;
            right: 6%;
            animation-delay: 2s;
        }

        /* Bottom Left - Discipline */
        .card-discipline {
            bottom: 22%;
            left: 15%;
            animation-delay: 4s;
        }

        /* Icon Circles inside cards */
        .icon-circle-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            flex-shrink: 0;
            border: 1px solid rgba(255,255,255,0.4);
        }

        .icon-circle-sm.green { background: #34d399; }
        .icon-circle-sm.blue { background: #3b82f6; }
        .icon-circle-sm.light-blue { background: #60a5fa; }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }

        /* --- FEATURES SECTION --- */
        .features-section {
            max-width: 1200px;
            margin: 40px auto 80px;
            padding: 0 5%;
            position: relative;
            z-index: 2;
        }

        .features-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .features-header .line {
            height: 2px;
            width: 60px;
            background: #cbd5e1;
        }

        .features-header h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark-blue);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid white;
            border-radius: 20px;
            padding: 25px 20px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            background: white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: #e0f2fe;
            color: var(--primary-blue);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin: 0 auto 15px auto;
        }

        .feature-card h4 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--dark-blue);
        }

        .feature-card p {
            font-size: 0.8rem;
            color: var(--text-gray);
            line-height: 1.4;
        }

        /* --- FOOTER WAVE --- */
        .footer-wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: var(--primary-blue);
            clip-path: ellipse(80% 100% at 50% 100%);
            z-index: -1;
            opacity: 0.8;
        }

        /* --- RESPONSIVE DESIGN --- */
        @media (max-width: 1024px) {
            .hero-content h1 { font-size: 3rem; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .image-container { height: 400px; }
            
            .card-permissions { left: 2%; }
            .card-parents { right: 2%; }
            .card-discipline { left: 10%; bottom: 10%; }
        }

        @media (max-width: 768px) {
            header { flex-direction: column; gap: 20px; }
            nav ul { gap: 20px; }
            
            .hero-container {
                flex-direction: column;
                text-align: center;
                margin-top: 20px;
            }

            .hero-content h1 { font-size: 2.5rem; }
            .hero-content p { margin: 0 auto 30px auto; }
            
            .features-grid { grid-template-columns: 1fr; }
            
            .image-container { 
                height: 350px; 
                margin-top: 30px;
            }
            
            .floating-card { padding: 10px 15px; font-size: 12px; }
            .card-permissions { top: 5%; left: 5%; }
            .card-parents { top: 20%; right: 5%; }
            .card-discipline { bottom: 10%; left: 15%; }
            
            .footer-wave { height: 50px; }
        }
    </style>
</head>
<body>

    <!-- Background Decorative Shapes -->
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <!-- Header Navigation -->
    <header>
        <div class="logo">
            <i class="fa-solid fa-graduation-cap"></i>
            <span>IMBONI</span>
        </div>
        
        <nav>
            <ul>
                <li><a href="#" class="active">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Features</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>

        <a href="#" class="btn-signin">
            <i class="fa-solid fa-lock"></i> Sign In
        </a>
    </header>

    <!-- Main Hero Section -->
    <main class="hero-container">
        
        <!-- Hero Text Content -->
        <div class="hero-content">
            <div class="badge">
                <i class="fa-solid fa-check"></i> School Management System
            </div>

            <h1>Smart School Management <br><span class="highlight">Made Simple</span></h1>
            
            <p class="description">
                IMBONI helps you manage student permissions, send parent messages, track return dates and keep discipline records — all in one place.
            </p>

            <a href="#" class="btn-primary">
                <div class="icon-circle"><i class="fa-solid fa-arrow-right"></i></div>
                Access Your Dashboard
            </a>
        </div>

        <!-- Hero Image Container using viggo.png -->
        <div class="hero-image-wrapper">
            <div class="image-container">
                
                <!-- SVG for the thin curved connector line -->
                <svg class="connector-line" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M 25 20 Q 50 10 75 25" fill="none" stroke="rgba(255,255,255,0.7)" stroke-width="0.5" stroke-dasharray="2, 1"/>
                </svg>

                <!-- Floating Card 1: Permissions -->
                <div class="floating-card card-permissions">
                    <div class="icon-circle-sm green"><i class="fa-solid fa-check"></i></div>
                    <span>Permissions<br>Managed</span>
                </div>

                <!-- Floating Card 2: Parents -->
                <div class="floating-card card-parents">
                    <div class="icon-circle-sm blue"><i class="fa-solid fa-bell"></i></div>
                    <span>Parents Notified</span>
                </div>

                <!-- Floating Card 3: Discipline -->
                <div class="floating-card card-discipline">
                    <div class="icon-circle-sm light-blue"><i class="fa-solid fa-chart-column"></i></div>
                    <span>Discipline<br>Tracked</span>
                </div>
                
            </div>
        </div>
    </main>

    <!-- Features Section -->
    <section class="features-section">
        <div class="features-header">
            <span class="line"></span>
            <h3>Our Key Features</h3>
            <span class="line"></span>
        </div>

        <div class="features-grid">
            <!-- Feature 1 -->
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-user-group"></i></div>
                <h4>Student<br>Permissions</h4>
                <p>Manage student movements</p>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-comment-dots"></i></div>
                <h4>Parent Messages</h4>
                <p>Keep parents informed</p>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-regular fa-calendar"></i></div>
                <h4>Return Tracking</h4>
                <p>Know when students come back</p>
            </div>

            <!-- Feature 4 -->
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <h4>Discipline Records</h4>
                <p>Record and monitor marks</p>
            </div>
        </div>
    </section>

    <!-- Bottom Decorative Wave -->
    <div class="footer-wave"></div>

</body>
</html>