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

    <!-- Error Modal -->
    <div class="modal fade" id="loginErrorModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Login Failed</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <p id="modalErrorText" class="fw-bold mt-2"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const form = document.getElementById("login-form");
        const errorModalEl = document.getElementById('loginErrorModal');
        const modalErrorText = document.getElementById('modalErrorText');
        // Initialize modal if bootstrap is loaded
        // We need to ensure bootstrap is available. header.php likely has it, or we added it above.
        // If not, we added the script tag above.

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append("action", "logIn");

            // clear error
            const errDiv = document.getElementById('errorMsg');
            if(errDiv) errDiv.classList.add('d-none');

            fetch("api.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        try {
                             window.location.href = `?page=${data.user.role}`;
                        } catch(e) {
                             console.error('Role redirection error', e); 
                             window.location.reload(); 
                        }
                    } else {
                        // Show Modal
                        if (typeof bootstrap !== 'undefined') {
                            modalErrorText.textContent = data.message || data.error || 'Incorrect Email or Password';
                            let loginModal = bootstrap.Modal.getOrCreateInstance(errorModalEl);
                            loginModal.show();
                        } else {
                            // Fallback if bootstrap fails
                            alert(data.message || data.error || 'Login failed');
                        }
                    }
                })
                .catch(err => {
                    console.error('Network/Parse error:', err);
                    modalErrorText.textContent = 'A network error occurred.';
                    if (typeof bootstrap !== 'undefined') {
                        let loginModal = bootstrap.Modal.getOrCreateInstance(errorModalEl);
                        loginModal.show();
                    } else {
                        alert('Network Error');
                    }
                });
        });
    </script>

    <?php require __DIR__ . '/layouts/footer.php'; ?>