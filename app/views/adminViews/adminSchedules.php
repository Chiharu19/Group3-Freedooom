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
                        <select class="form-select" name="filter-room">
                            <option value="">All</option>

                            <?php foreach ($allRoomsList as $room): ?>
                                <option value="<?= htmlspecialchars($room['id']); ?>">
                                    <?= htmlspecialchars($room['room_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Faculty</label>
                        <select class="form-select" name="filter-faculty">
                            <option value="">All</option>
                            
                            <?php foreach ($allFacultyUserList as $user): ?>
                                <option value="<?= htmlspecialchars($user['id']); ?>">
                                    <?= htmlspecialchars($user['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>

                <div class="text-end mt-3">
                    <button class="btn btn-secondary px-4 me-2" id="reset-btn">
                        <i class="fa fa-rotate-left me-2"></i>Reset
                    </button>
                    <button class="btn btn-danger px-4" id="filter-btn">
                        <i class="fa fa-search me-2"></i>Apply Filters
                    </button>
                </div>
            </div>

            <!-- edit booking window -->
            <div class="modal fade" id="editBookingModal">
                <div class="modal-dialog">
                    <div class="modal-content p-3">

                        <h5 class="fw-bold mb-3">Edit Booking</h5>

                        <form id="editBookingForm">

                            <input type="hidden" name="booking-id" id="edit-booking-id">

                            <label class="form-label">Room Name</label>
                            <input type="text" class="form-control mb-2" name="room-name" id="edit-room-name" readonly>

                            <label class="form-label">Date</label>
                            <input type="date" class="form-control mb-2" name="date" id="edit-date">

                            <label class="form-label">Start Time</label>
                            <input type="time" class="form-control mb-2" name="start-time" id="edit-start-time">
                            
                            <label class="form-label">Duration (Hours)</label>
                            <input type="number" class="form-control mb-2" name="duration" id="edit-duration">

                            <label class="form-label">Faculty</label>
                            <select class="form-select mb-2" name="faculty-name" id="edit-faculty-name">
                                <?php foreach ($allFacultyUserList as $user): ?>
                                    <option value="<?= htmlspecialchars($user['id']); ?>">
                                        <?= htmlspecialchars($user['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <button type="submit" class="btn btn-danger w-100 mt-2">Save Changes</button>

                        </form>

                    </div>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="card p-3 shadow-sm" style="height: 500px;">
                <h5 class="fw-bold mb-3">Room Booking Overview</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th>Room</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Faculty</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="booking-table-body">
                            <tr><td colspan="5" class="text-center py-3 text-muted">Loading...</td></tr>
                        </tbody>

                    </table>
                </div>

            </div>

        </div>

    </div>
</div>

<script>

    function attachDeleteEvent() {
        document.querySelectorAll(".delete-booking-btn").forEach(btn => {
            btn.addEventListener("click", () => {
                const booking_id = btn.dataset.id;

                const formData = new FormData();
                formData.append("action", "deleteBooking");
                formData.append("booking-id", booking_id);

                fetch("/public/api.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        loadBookings(); // refresh table only
                    } else {
                        console.log(data.message);
                    }
                });
            });
        });
    }


    function loadBookings() {
        const room = document.querySelector("[name='filter-room']").value;
        const date = document.querySelector("input[type='date']").value;
        const faculty = document.querySelector("select[name='filter-faculty']")?.value ?? "";

        const formData = new FormData();
        formData.append("action", "getBookingList");
        formData.append("room", room);
        formData.append("date", date);
        formData.append("faculty", faculty);

        fetch("/public/api.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById("booking-table-body");

            if (!data.success || data.data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-3 text-muted">No bookings found</td>
                    </tr>
                `;
                return;
            }

            // Build rows dynamically
            tbody.innerHTML = data.data.map(b => `
                <tr>
                    <td>${b.room_name}</td>
                    <td>${b.date}</td>
                    <td>${b.start_time} - ${b.end_time}</td>
                    <td>${b.full_name}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary me-1 edit-booking-btn" data-bs-toggle="modal" data-bs-target="#editBookingModal" data-id="${b.id}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-booking-btn" data-id="${b.id}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join("");

            attachDeleteEvent();
        });
    }

    document.getElementById("filter-btn").addEventListener('click', () => {
        loadBookings();
    });

    document.getElementById("reset-btn").addEventListener("click", () => {
        document.querySelector("[name='filter-room']").value = "";
        document.querySelector("input[type='date']").value = "";

        const facultySelect = document.querySelector("select[name='filter-faculty']");
        if (facultySelect) facultySelect.value = "";

        loadBookings();
    });

    // load on page open
    loadBookings(); 


</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>