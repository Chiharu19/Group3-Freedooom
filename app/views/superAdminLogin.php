<?php require __DIR__ . '/layouts/header.php'; ?>

<link rel="stylesheet" href="../../public/assets/css/superAdminLogin.css">
</head>

<body class="d-flex justify-content-center align-items-center min-vh-100">

    <div class="login-card p-5 shadow rounded-4" style="width: 400px;">

        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color: #e94560;">SUPER ADMIN</h3>
            <p class="text-muted small">Restricted Access Only</p>
        </div>

        <div id="errorMsg" class="alert alert-danger d-none"></div>

        <form id="super-login-form">

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <button type="submit" class="btn btn-super w-100 py-2 fw-bold">
                ENTER SYSTEM
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="landing.php" class="text-decoration-none text-white small">
                <i class="bi bi-arrow-left"></i> Back to Landing
            </a>
        </div>

    </div>

    <script>
        const form = document.getElementById("super-login-form");
        const errorDiv = document.getElementById("errorMsg");

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            errorDiv.classList.add("d-none");

            const formData = new FormData(form);
            formData.append("action", "superAdminLogin");

            fetch("api.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = "?page=super-admin";
                    } else {
                        errorDiv.textContent = data.message || "Access Denied";
                        errorDiv.classList.remove("d-none");
                    }
                })
                .catch(err => console.error(err));
        });
    </script>

    <?php require __DIR__ . '/layouts/footer.php'; ?>