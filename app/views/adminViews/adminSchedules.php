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
            <a href="?page=logout">Logout</a>
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

                <!-- add booking window -->
                <div class="modal fade" id="addBookingModal">
                    <div class="modal-dialog">
                        <div class="modal-content p-3">

                            <h5 class="fw-bold mb-3">Add Booking</h5>

                            <form id="addBookingForm">

                                <label class="form-label">Room Name</label>
                                <select class="form-select mb-2" name="room-id" id="add-room-id" required>
                                    <?php foreach ($allRoomsList as $room): ?>
                                        <option value="<?= htmlspecialchars($room['id']); ?>">
                                            <?= htmlspecialchars($room['room_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <label class="form-label">Date</label>
                                <input type="date" class="form-control mb-2" name="date" id="add-date" required>

                                <label class="form-label">Start Time</label>
                                <input type="time" class="form-control mb-2" name="start-time" id="add-start-time" required>
                                
                                <label class="form-label">Duration (Hours)</label>
                                <input type="number" class="form-control mb-2" name="duration" id="add-duration" required>

                                <label class="form-label">Faculty</label>
                                <select class="form-select mb-2" name="faculty-id" id="add-faculty-name" required>
                                    <?php foreach ($allFacultyUserList as $user): ?>
                                        <option value="<?= htmlspecialchars($user['id']); ?>">
                                            <?= htmlspecialchars($user['full_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <button type="submit" class="btn btn-danger w-100 mt-2">Add Booking</button>

                            </form>
                        </div>
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
                                <input type="text" class="form-control mb-2" name="room-name" id="edit-room-name"
                                    readonly>

                                <label class="form-label">Date</label>
                                <input type="date" class="form-control mb-2" name="date" id="edit-date">

                                <label class="form-label">Start Time</label>
                                <input type="time" class="form-control mb-2" name="start-time" id="edit-start-time">

                                <label class="form-label">Duration (Hours)</label>
                                <input type="number" class="form-control mb-2" name="duration" id="edit-duration">

                                <label class="form-label">Faculty</label>
                                <select class="form-select mb-2" name="faculty-id" id="edit-faculty-name">
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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0">Room Booking Overview</h5>
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addBookingModal">Add Booking</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Room</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Duration</th>
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

        function toTimeValue(str) {
            // str example: "9:00 AM"
            const [time, meridiem] = str.split(" "); // ["9:00", "AM"]
            let [hour, minute] = time.split(":").map(Number);

            if (meridiem === "PM" && hour !== 12) {
                hour += 12;
            }

            if (meridiem === "AM" && hour === 12) {
                hour = 0;
            }

            // pad to "09" if single digit
            hour = String(hour).padStart(2, "0");

            return `${hour}:${minute.toString().padStart(2, "0")}`;
        }

        function attachEditEvent() {
            document.querySelectorAll(".edit-booking-btn").forEach(btn => {
                btn.addEventListener("click", () => {

                    document.getElementById("edit-booking-id").value = btn.dataset.id;
                    document.getElementById("edit-room-name").value = btn.dataset.roomName;
                    document.getElementById("edit-date").value = btn.dataset.date;
                    document.getElementById("edit-start-time").value = toTimeValue(btn.dataset.startTime);
                    document.getElementById("edit-duration").value = btn.dataset.duration;
                    document.getElementById("edit-faculty-name").value = btn.dataset.facultyId;

                });
            });
        }

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
                    <td>${b.duration}</td>
                    <td>${b.full_name}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary me-1 edit-booking-btn" data-bs-toggle="modal" data-bs-target="#editBookingModal" 
                            data-id="${b.id}"
                            data-room-name="${b.room_name}"
                            data-date="${b.date}"
                            data-start-time="${b.start_time}"
                            data-duration="${b.duration}"
                            data-faculty-id="${b.user_id}"
                        >
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-booking-btn" data-id="${b.id}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join("");

                    attachEditEvent();
                    attachDeleteEvent();
                });
        }

        const addBookingForm = document.getElementById("addBookingForm");
        addBookingForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(addBookingForm);
            formData.append("action", "addBooking");

            fetch("/public/api.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = "?page=admin-schedules";
                    loadBookings();
                } else {
                    console.log(data.message);
                }
            });
        });

        const editBookingForm = document.getElementById("editBookingForm");
        editBookingForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(editBookingForm);
            formData.append("action", "editBooking");

            fetch("/public/api.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = "?page=admin-schedules";
                    } else {
                        console.log(data.message);
                    }
                });
        });

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