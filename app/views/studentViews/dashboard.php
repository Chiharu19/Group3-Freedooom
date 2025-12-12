<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/../../../public/assets/css/student/dashboard.css">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
</head>

<body>

    <!-- Sidebar toggle for mobile -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle">
    <label for="sidebar-toggle" class="sidebar-toggle-label">☰ Menu</label>

    <div class="wrapper">

        <!-- Sidebar -->
        <!-- Sidebar -->
        <?php include __DIR__ . '/../layouts/student_sidebar.php'; ?>

        <!-- Main content -->
        <div class="main-content">
            <div class="topbar">
                <h4 class="fw-bold">Dashboard</h4>
                <span class="fw-semibold">Welcome,
                    <?= htmlspecialchars($_SESSION['user']['full_name'] ?? 'Student') ?></span>
            </div>

            <div class="container-fluid">
                <!-- Notices Card -->
                <div class="card-custom">
                    <h5>Notices</h5>
                    <ul id="noticesList">
                        <li>Loading notices...</li>
                    </ul>
                </div>

                <!-- Booking Requests Card -->
                <div class="card-custom">
                    <h5>Your Booking Requests</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-custom mt-3">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Room</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="dashboardRequestsTableBody">
                                <tr>
                                    <td colspan="4" class="text-center">Loading requests...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- New Button -->
                    <div class="mt-3 text-end">
                        <a href="?page=student-rooms" class="btn btn-bsu-red">Room Availability</a>
                    </div>
                </div>

            </div>


        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/student.js"></script>
</body>

</html>