<?php require __DIR__ . '/layouts/header.php'; ?>

<link rel="stylesheet" href="../../public/assets/css/login.css">
</head>

<body class="login-body">

    <div class="container d-flex justify-content-center align-items-center min-vh-100">

        <div class="login-card p-4 shadow rounded-4">

            <div class="text-center mb-3">
                <img src="../../public/assets/img/BSU_Logo.png" alt="School Logo" class="login-logo">
                <h4 class="fw-bold mt-2">Batangas State University</h4>
            </div>

            <div id="errorMsg" class="alert alert-danger d-none"></div>

            <form id="login-form">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" class="form-control" name="password" required>
                    <div class="text-end mt-1">
                        <a href="forgot_password.php" class="text-decoration-none small" style="color: #0d6efd;">Forgot
                            Password?</a>
                    </div>
                </div>

                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    Login
                </button>
            </form>

        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const form = document.getElementById("login-form");
        const errorDiv = document.getElementById("errorMsg");

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append("action", "logIn");

            // clear error
            if (errorDiv) errorDiv.classList.add('d-none');

            fetch("api.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        try {
                            window.location.href = `?page=${data.user.role}`;
                        } catch (e) {
                            console.error('Role redirection error', e);
                            window.location.reload();
                        }
                    } else {
                        // Show Error Div
                        errorDiv.textContent = data.message || data.error || 'Incorrect Email or Password';
                        errorDiv.classList.remove('d-none');
                    }
                })
                .catch(err => {
                    console.error('Network/Parse error:', err);
                    errorDiv.textContent = 'A network error occurred.';
                    errorDiv.classList.remove('d-none');
                });
        });
    </script>

    <?php require __DIR__ . '/layouts/footer.php'; ?>