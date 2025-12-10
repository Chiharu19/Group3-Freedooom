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
                </div>

                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    Login
                </button>
            </form>

        </div>
    </div>

    <script>

        const form = document.getElementById("login-form");

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append("action", "logIn"); // tell API which action

            // clear error
            const errDiv = document.getElementById('errorMsg');
            errDiv.classList.add('d-none');
            errDiv.textContent = '';

            fetch("api.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        console.log('Login successful', data.user);
                        // take user to the dashboard page of their role
                        window.location.href = `?page=${data.user.role}`;
                    } else {
                        errDiv.textContent = data.message || data.error || 'Login failed';
                        errDiv.classList.remove('d-none');
                        console.error('Login error:', data);
                    }
                })
                .catch(err => {
                    console.error('Network/Parse error:', err);
                    errDiv.textContent = 'An error occurred. check console.';
                    errDiv.classList.remove('d-none');
                });
        });

    </script>

    <?php require __DIR__ . '/layouts/footer.php'; ?>