<?php require __DIR__ . '/../layouts/header.php'; ?>

    <link rel="stylesheet" href="../../../public/assets/css/admin/adminSchedules.css">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="wrapper">

    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="text-center mt-3 mb-4">Admin</h3>
        <a href="?page=admin">Dashboard</a>
        <a href="?page=admin-rooms">Rooms</a>
        <a href="#" class="active">Schedules</a>
        <a href="?page=admin-users">Manage Users</a>
        <a href="#">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">

        <!-- Topbar -->
        <div class="topbar d-flex align-items-center px-4">
            <h5 class="m-0 fw-bold">All Schedules (Room Booking Overview)</h5>
        </div>

        <!-- Page Content -->
        <div class="container py-4">

            <!-- Filters -->
            <div class="card p-3 shadow-sm mb-4">
                <h5 class="fw-bold mb-3">Filter Bookings</h5>

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Room</label>
                        <select class="form-select">
                            <option>All</option>
                            <option>Room 101</option>
                            <option>Room 204</option>
                            <option>Lab 3</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Faculty</label>
                        <select class="form-select">
                            <option>All</option>
                            <option>Dr. Santos</option>
                            <option>Prof. Dela Cruz</option>
                            <option>Mr. Reyes</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select">
                            <option>All</option>
                            <option>Confirmed</option>
                            <option>Pending</option>
                            <option>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button class="btn btn-danger px-4"><i class="fa fa-search me-2"></i>Apply Filters</button>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="card p-3 shadow-sm">
                <h5 class="fw-bold mb-3">Room Booking Overview</h5>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th>Room</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Faculty</th>
                                <th>Status</th>
                                <th>Conflict</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>Room 101</td>
                                <td>2025-11-30</td>
                                <td>08:00 - 10:00</td>
                                <td>Dr. Santos</td>
                                <td><span class="badge bg-success">Confirmed</span></td>
                                <td><span class="badge bg-secondary">None</span></td>
                                <td class="text-center">
                                    <a href="edit_booking.html">
                                    <button class="btn btn-sm btn-outline-primary me-1"><i class="fa fa-edit"></i></button>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa fa-times"></i></button>
                                </td>
                            </tr>

                            <tr>
                                <td>Room 204</td>
                                <td>2025-11-30</td>
                                <td>09:00 - 11:00</td>
                                <td>Prof. Dela Cruz</td>
                                <td><span class="badge bg-warning text-dark">Pending</span></td>
                                <td>
                                    <span class="badge bg-danger">
                                        Conflict
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="edit_booking.html">
                                    <button class="btn btn-sm btn-outline-primary me-1"><i class="fa fa-edit"></i></button>
                                    </a>                                   
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa fa-times"></i></button>
                                </td>
                            </tr>

                            <tr>
                                <td>Lab 3</td>
                                <td>2025-12-01</td>
                                <td>14:00 - 16:00</td>
                                <td>Mr. Reyes</td>
                                <td><span class="badge bg-danger">Cancelled</span></td>
                                <td><span class="badge bg-secondary">None</span></td>
                                <td class="text-center">
                                    <a href="edit_booking.html">
                                    <button class="btn btn-sm btn-outline-primary me-1"><i class="fa fa-edit"></i></button>
                                    </a>                                    
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa fa-times"></i></button>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            </div>

        </div>

    </div>
</div>

<script>

</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>