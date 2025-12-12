<?php require __DIR__ . '/layouts/header.php'; ?>

<style>
    body {
        background: #1a1a2e;
        color: #fff;
    }

    .login-card {
        background: #16213e;
        border: 1px solid #0f3460;
    }

    .form-control {
        background: #0f3460;
        border: 1px solid #1a1a2e;
        color: #fff;
    }

    .form-control:focus {
        background: #1a1a2e;
        color: #fff;
        border-color: #e94560;
        box-shadow: none;
    }

    .btn-super {
        background: #e94560;
        color: #fff;
    }

    .btn-super:hover {
        background: #c33c50;
        color: #fff;
    }
</style>
</head>

<body class="d-flex justify-content-center align-items-center min-vh-100">

    <div class="login-card p-5 shadow rounded-4" style="width: 450px;">

        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color: #e94560;">SUPER ADMIN SETUP</h3>
            <p class="text-muted small">Create the first Super Admin Account</p>
        </div>

        <div id="errorMsg" class="alert alert-danger d-none"></div>

        <form id="super-register-form">

            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" class="form-control" name="full_name" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" required>
            </div>

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <button type="submit" class="btn btn-super w-100 py-2 fw-bold">
                CREATE ACCOUNT
            </button>
        </form>

    </div>

    <script>
        const form = document.getElementById("super-register-form");
        const errorDiv = document.getElementById("errorMsg");

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            errorDiv.classList.add("d-none");

            const formData = new FormData(form);
            const pass = formData.get("password");
            const confirm = formData.get("confirm_password");

            if (pass !== confirm) {
                errorDiv.textContent = "Passwords do not match";
                errorDiv.classList.remove("d-none");
                return;
            }

            formData.append("action", "registerFirstSuperAdmin");

            fetch("/public/api.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Super Admin Account Created! Please log in.");
                        window.location.reload();
                    } else {
                        // Redirect if super admin already exists (race condition or safeguard)
                        if (data.redirect_to_login) {
                            alert(data.message);
                            window.location.href = "?page=super-admin-login";
                            return;
                        }

                        errorDiv.textContent = data.message || "Registration Failed";
                        errorDiv.classList.remove("d-none");
                    }
                })
                .catch(err => console.error(err));
        });
    </script>

    <?php require __DIR__ . '/layouts/footer.php'; ?>