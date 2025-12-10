<?php require __DIR__ . '/layouts/header.php'; ?>

<link rel="stylesheet" href="../../public/assets/css/landing.css">
</head>

<body class="landing-body">

    <div class="landing-hero">
        <div class="landing-content">
            <img src="../../public/assets/img/BSU_Logo.png" alt="BatStateU Logo" class="landing-logo">
            <h1 class="landing-title">Resource Booking System</h1>
            <p class="landing-subtitle">Streamlining facility reservations for Faculty and Students of Batangas State University.</p>
            
            <a href="index.php?page=login" class="btn-landing-login">
                Get Started
            </a>
        </div>
    </div>

    <!-- Simple Footer (Optional, can be removed if cleaner look desired) -->
    <footer style="position: absolute; bottom: 0; width: 100%; text-align: center; color: rgba(255,255,255,0.6); padding: 10px; font-size: 0.8rem;">
        &copy; <?= date('Y') ?> Batangas State University. All rights reserved.
    </footer>

<?php // require __DIR__ . '/layouts/footer.php'; // Footer might interfere with full height hero, keeping it simple manually ?> 
</body>
</html>
