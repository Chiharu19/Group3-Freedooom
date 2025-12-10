<?php require __DIR__ . '/../layouts/header.php'; ?>

<link rel="stylesheet" href="../../../public/assets/css/admin/adminDashboard.css">

<!-- for calendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
</head>

<body>

<div class="wrapper">

        <div class="sidebar">
            <h3 class="text-center mt-3 mb-4">Admin</h3>
            <a href="#" class="active">Dashboard</a>
            <a href="?page=admin-rooms">Rooms</a>
            <a href="?page=admin-schedules">Schedules</a>
            <a href="?page=admin-requests">Student Requests</a>
            <a href="?page=admin-users">Manage Users</a>
            <a href="#" onclick="event.preventDefault(); new bootstrap.Modal(document.getElementById('logoutModal')).show();">Logout</a>
        </div>

        <div class="main-content">

            <div class="topbar d-flex justify-content-between align-items-center px-4">
                <h4 class="fw-bold">Dashboard</h4>
                <span class="fw-semibold">Welcome, <?= htmlspecialchars($_SESSION['user']['full_name'] ?? 'Administrator') ?></span>
            </div>

            <div class="container mt-4">

                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card shadow-sm p-3 text-center card-custom h-100 d-flex flex-column justify-content-center">
                            <h5>Total Rooms</h5>
                            <h3><?= $totalRooms ?></h3>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card shadow-sm p-3 text-center card-custom h-100 d-flex flex-column justify-content-center">
                            <h5>Total Faculty/Staff</h5>
                            <h3><?= $totalFacultyStaff ?></h3>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card shadow-sm p-3 text-center card-custom h-100 d-flex flex-column justify-content-center">
                            <h5>Pending Student Requests</h5>
                            <a href="?page=admin-requests" class="text-decoration-none text-dark">
                                <h3><?= $totalPendingStudentRequests ?></h3>
                            </a>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card shadow-sm p-3 text-center card-custom h-100 d-flex flex-column justify-content-center">
                            <h5>Today's Bookings</h5>
                            <h3><?= $totalTodaysBookings ?></h3>
                        </div>
                    </div>
                </div>

                <div class="mt-4 mb-3">
                    <h5 class="fw-bold">Calendar / Timeline</h5>
                    <div class="card card-custom p-4 text-center" style="height: 600px;">
                        <!-- <p>Calendar or timeline view placeholder</p> -->

                        <div id="calendar"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- for calendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>

        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth', // shows the full month
                headerToolbar: {
                    left: 'prev,next today', // buttons on the left
                    center: 'title',         // month/year title
                    right: 'dayGridMonth,timeGridWeek,timeGridDay' // optional view switcher
                },
                events: [
                    { title: 'Meeting', date: '2025-12-10' },
                    { title: 'lablab', date: '2025-12-10' },
                ]
            });
            calendar.render();
        });

    </script>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>