<?php require __DIR__ . '/../layouts/header.php'; ?>

<style>
    /* Slight variation to distinguish from regular admin */
    .sidebar { background: #1a1a2e; } 
    .card-custom { border-left: 5px solid #e94560; }
</style>
</head>

<body>

<div class="wrapper">

        <div class="sidebar">
            <h3 class="text-center mt-3 mb-4 text-white">Super Admin</h3>
            <a href="#" class="active">Dashboard</a>
            <a href="?page=super-admin-users">Manage Admins</a>
            <a href="?page=logout">Logout</a>
        </div>

        <div class="main-content">

            <div class="topbar d-flex justify-content-between align-items-center px-4">
                <h4 class="fw-bold">Super Admin Dashboard</h4>
                <span class="fw-semibold">Welcome, Overlord</span>
            </div>

            <div class="container mt-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card shadow-sm p-3 text-center card-custom">
                            <h5>Total Admins</h5>
                            <h3><?= $totalAdmins ?></h3>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm p-3 text-center card-custom">
                            <h5>Total System Users</h5>
                            <h3><?= $totalUsers ?></h3>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="alert alert-info">
                        <strong>System Notice:</strong> As a Super Admin, you have full control over Administrator accounts.
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>
