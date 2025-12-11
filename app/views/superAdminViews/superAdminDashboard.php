<?php require __DIR__ . '/../layouts/header.php'; ?>

<link rel="stylesheet" href="../../../public/assets/css/superAdmin.css">
</head>

<body>

    <div class="wrapper">

        <div class="sidebar">
            <h3 class="text-center mt-3 mb-4 text-white">Super Admin</h3>
            <a href="#" class="active">Dashboard</a>
            <a href="?page=super-admin-users">Manage Admins</a>
            <a href="?page=super-admin-transfer">Transfer Rights</a>
            <a href="#"
                onclick="event.preventDefault(); new bootstrap.Modal(document.getElementById('saLogoutModal')).show();">Logout</a>
        </div>

        <div class="main-content">

            <div class="topbar d-flex justify-content-between align-items-center px-4">
                <h4 class="fw-bold">Super Admin Dashboard</h4>
                <span class="fw-semibold">Welcome,
                    <?= htmlspecialchars($_SESSION['user']['full_name'] ?? 'Super Admin') ?></span>
            </div>

            <div class="container mt-4">

                <div class="row g-3">
                    <!-- Total Admins -->
                    <div class="col-md-4">
                        <div
                            class="card shadow-sm p-3 text-center card-custom h-100 d-flex flex-column justify-content-center">
                            <h5>Total Admins</h5>
                            <h3><?= $totalAdmins ?></h3>
                        </div>
                    </div>

                    <!-- Total Users -->
                    <div class="col-md-4">
                        <div
                            class="card shadow-sm p-3 text-center card-custom h-100 d-flex flex-column justify-content-center">
                            <h5>Total System Users</h5>
                            <h3><?= $totalUsers ?></h3>
                        </div>
                    </div>

                    <!-- Total Rooms -->
                    <div class="col-md-4">
                        <div
                            class="card shadow-sm p-3 text-center card-custom h-100 d-flex flex-column justify-content-center">
                            <h5>Total Rooms</h5>
                            <h3><?= $totalRooms ?></h3>
                        </div>
                    </div>

                    <!-- Total Bookings -->
                    <div class="col-md-6 mt-3">
                        <div
                            class="card shadow-sm p-3 text-center card-custom h-100 d-flex flex-column justify-content-center">
                            <h5>Total Bookings</h5>
                            <h3><?= $totalBookings ?></h3>
                        </div>
                    </div>

                    <!-- Pending Requests -->
                    <div class="col-md-6 mt-3">
                        <div
                            class="card shadow-sm p-3 text-center card-custom h-100 d-flex flex-column justify-content-center">
                            <h5>Pending Requests</h5>
                            <h3><?= $pendingRequests ?></h3>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="alert alert-info">
                        <strong>System Notice:</strong> As a Super Admin, you have full control over Administrator
                        accounts.
                    </div>
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

    <?php require __DIR__ . '/../layouts/footer.php'; ?>