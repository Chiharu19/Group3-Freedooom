<?php require __DIR__ . '/layouts/header.php'; ?>

<link rel="stylesheet" href="../../public/assets/css/landing.css">
</head>

<body class="landing-body">

    <div class="landing-hero">
        <div class="landing-content">
            <img src="../../public/assets/img/BSU_Logo.png" alt="BatStateU Logo" class="landing-logo">
            <h1 class="landing-title">Resource Booking System</h1>
            <p class="landing-subtitle">Streamlining facility reservations for Faculty and Students of Batangas State
                University.</p>

            <a href="index.php?page=login" class="btn-landing-login">
                Get Started
            </a>
        </div>
    </div>

    <!-- Simple Footer (Optional, can be removed if cleaner look desired) -->
    <footer
        style="position: absolute; bottom: 0; width: 100%; text-align: center; color: rgba(255,255,255,0.6); padding: 10px; font-size: 0.8rem;">
        &copy; <?= date('Y') ?> Batangas State University. All rights reserved.
    </footer>

    <!-- Map Section -->
    <!-- Contact & Map Section -->
    <section class="contact-section" id="contact">
        <div class="container py-5">
            <div class="row align-items-center g-5">
                <!-- Contact Info -->
                <div class="col-lg-5">
                    <h2 class="fw-bold mb-4 text-danger">Visit Us</h2>
                    <p class="mb-4 text-muted">
                        Feel free to drop by for inquiries or assistance regarding facility reservations.
                    </p>
                    
                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3 text-danger">
                            <i class="bi bi-geo-alt-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Address</h6>
                            <p class="mb-0 text-muted">Batangas State University - JPLPC Malvar Campus<br>G. Leviste St., Poblacion, Malvar Batangas</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3 text-danger">
                            <i class="bi bi-envelope-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Email</h6>
                            <p class="mb-0 text-muted">resource.booking@g.batstate-u.edu.ph</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start">
                        <div class="me-3 text-danger">
                            <i class="bi bi-telephone-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Phone</h6>
                            <p class="mb-0 text-muted">(043) 980-0385 loc. 1234</p>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="col-lg-7">
                    <div class="map-container shadow-lg rounded-4 overflow-hidden">
                        <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4631.550868348818!2d121.15337827576698!3d14.044949990437589!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd6ed9735068d7%3A0x97fd25b226e150e7!2sBatangas%20State%20University%20Jose%20P.%20Laurel%20Polytechnic%20College!5e1!3m2!1sen!2sph!4v1765456270052!5m2!1sen!2sph"
                        width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap Icons (if not already included) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <?php // require __DIR__ . '/layouts/footer.php'; // Footer might interfere with full height hero, keeping it simple manually ?>
</body>

</html>