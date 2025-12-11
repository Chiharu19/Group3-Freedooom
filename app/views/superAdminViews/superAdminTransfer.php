<?php require __DIR__ . '/../layouts/header.php'; ?>

<link rel="stylesheet" href="../../../public/assets/css/superAdmin.css">
</head>

<body>

    <div class="wrapper">

        <div class="sidebar">
            <h3 class="text-center mt-3 mb-4 text-white">Super Admin</h3>
            <a href="?page=super-admin">Dashboard</a>
            <a href="?page=super-admin-users">Manage Admins</a>
            <a href="#" class="active">Transfer Rights</a>
            <a href="#"
                onclick="event.preventDefault(); new bootstrap.Modal(document.getElementById('saLogoutModal')).show();">Logout</a>
        </div>

        <div class="main-content">

            <div class="topbar d-flex justify-content-between align-items-center px-4">
                <h4 class="fw-bold">Transfer Super Admin Rights</h4>
            </div>

            <div class="container mt-4">

                <div class="card p-4 shadow-sm">
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> transferring ownership will replace your current Super Admin login
                        credentials with the new ones provided below.
                        You will be logged out immediately after confirmation.
                    </div>

                    <form id="transferForm" style="max-width: 600px;">
                        <label class="form-label">New Account Name</label>
                        <input type="text" name="full_name" class="form-control mb-3" required>

                        <label class="form-label">New Email Address</label>
                        <input type="email" name="email" class="form-control mb-3" required>

                        <label class="form-label">New Password</label>
                        <input type="password" name="password" id="new-password" class="form-control mb-3" required
                            minlength="6">

                        <label class="form-label">Confirm Password</label>
                        <input type="password" id="confirm-password" class="form-control mb-4" required minlength="6">

                        <button type="submit" class="btn btn-warning fw-bold px-4">Confirm Transfer & Logout</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- SA Logout Modal -->
    <div class="modal fade" id="saLogoutModal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to logout?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="?page=super-admin-logout" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Transfer Logic
        document.getElementById('transferForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const p1 = document.getElementById('new-password').value;
            const p2 = document.getElementById('confirm-password').value;

            if (p1 !== p2) {
                alert("Passwords do not match");
                return;
            }

            if (!confirm("Are you absolutely sure? You will lose access to the current credentials immediately.")) return;

            const formData = new FormData(this);
            formData.append("action", "transferSuperAdminOwnership");

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) formData.append('csrf_token', csrfToken);

            fetch("/public/api.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.href = "/public/index.php?page=super-admin-login";
                    } else {
                        alert(data.message || "Transfer failed");
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Network error");
                });
        });
    </script>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>