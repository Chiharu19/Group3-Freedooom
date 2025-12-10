<?php require __DIR__ . '/../layouts/header.php'; ?>

<link rel="stylesheet" href="../../../public/assets/css/admin/adminDashboard.css">

<!-- Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div class="wrapper">

        <!-- Sidebar -->
        <div class="sidebar">
            <h3 class="text-center mt-3 mb-4">Admin</h3>
            <a href="?page=admin">Dashboard</a>
            <a href="?page=admin-rooms" class="active">Rooms</a>
            <a href="?page=admin-schedules">Schedules</a>
            <a href="?page=admin-requests">Student Requests</a>
            <a href="?page=admin-users">Manage Users</a>
            <a href="?page=logout">Logout</a>
        </div>

        <!-- Main Content -->
        <div class="main-content">

            <!-- Topbar -->
            <div class="topbar d-flex align-items-center px-4">
                <h5 class="m-0 fw-bold">Manage Rooms</h5>
            </div>

            <!-- Page Content -->
            <div class="container py-4">

                <!-- Add Room Button -->
                <div class="mb-3 text-end">
                    <button class="btn btn-danger px-4" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                        <i class="fa fa-plus me-2"></i>Add Room
                    </button>
                </div>

                <!-- Add Room Window -->
                <div class="modal fade" id="addRoomModal">
                    <div class="modal-dialog">
                        <div class="modal-content p-3">
                            <h5 class="fw-bold mb-3">Add New Room</h5>

                            <form id="addRoomForm">
                                <label class="form-label">Room Name</label>
                                <input type="text" class="form-control mb-2" name="room-name" required>

                                <label class="form-label">Building</label>
                                <select class="form-select mb-2" name="building" required>
                                    <option value="CICS">CICS</option>
                                    <option value="CIT">CIT</option>
                                </select>

                                <label class="form-label">Capacity</label>
                                <input type="number" class="form-control mb-2" name="capacity" required>

                                <button type="submit" class="btn btn-danger w-100 mt-2">Add Room</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- edit room window -->
                <div class="modal fade" id="editRoomModal">
                    <div class="modal-dialog">
                        <div class="modal-content p-3">

                            <h5 class="fw-bold mb-3">Edit Room</h5>

                            <form id="editRoomForm">

                                <input type="hidden" name="room-id" id="edit-room-id">

                                <label class="form-label">Room Name</label>
                                <input type="text" class="form-control mb-2" name="room-name" id="edit-room-name"
                                    readonly>

                                <label class="form-label">Building</label>
                                <select class="form-select mb-2" name="building" id="edit-building" required>
                                    <option value="CICS">CICS</option>
                                    <option value="CIT">CIT</option>
                                </select>

                                <label class="form-label">Capacity</label>
                                <input type="number" class="form-control mb-2" name="capacity" id="edit-capacity"
                                    required>

                                <label class="form-label">Status</label>
                                <select class="form-select mb-2" name="status" id="edit-status">
                                    <option value="available">Available</option>
                                    <option value="maintenance">Under Maintenance</option>
                                    <option value="booked">Booked</option>
                                </select>

                                <button type="submit" class="btn btn-danger w-100 mt-2">Save Changes</button>

                            </form>

                        </div>
                    </div>
                </div>


                <!-- Rooms Table -->
                <div class="card p-3 shadow-sm">
                    <h5 class="fw-bold mb-3">Room List</h5>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-custom-header">
                            <thead>
                                <tr>
                                    <th>Room Number</th>
                                    <th>Capacity</th>
                                    <th>Building</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (empty($allRoomsList)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">
                                            No rooms found
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($allRoomsList as $room): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($room['room_name']); ?></td>
                                            <td><?= htmlspecialchars($room['capacity']); ?></td>
                                            <td><?= htmlspecialchars($room['building']); ?></td>

                                            <td>
                                                <?php if ($room['status'] === 'available'): ?>
                                                    <span class="badge bg-success">Available</span>
                                                <?php elseif ($room['status'] === 'maintenance'): ?>
                                                    <span class="badge bg-warning text-dark">Under Maintenance</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Booked</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-primary me-1 edit-room-btn"
                                                    data-id="<?= $room['id']; ?>"
                                                    data-name="<?= htmlspecialchars($room['room_name']); ?>"
                                                    data-capacity="<?= htmlspecialchars($room['capacity']); ?>"
                                                    data-building="<?= htmlspecialchars($room['building']); ?>"
                                                    data-status="<?= htmlspecialchars($room['status']); ?>"
                                                    data-bs-toggle="modal" data-bs-target="#editRoomModal">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger delete-room-btn"
                                                    data-id="<?= $room['id']; ?>">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <script>

        const form = document.getElementById("addRoomForm");

        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append("action", "addRoom"); // tell API which action
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if(csrfToken) formData.append("csrf_token", csrfToken);

            fetch("/public/api.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // redirect to the same page to refresh and update the rooms list
                        window.location.href = "?page=admin-rooms";
                    } else {
                        console.log(data.message);

                        /* 
                        
                            CODE HERE WHEN UNSUCCESSFUL
        
                            Note: you can do console.log(data.message) here to see why unsuccessful
                        
                        */
                    }
                })
                .catch(err => console.error(err));
        });

        // event listener to edit button per list
        document.querySelectorAll(".edit-room-btn").forEach(btn => {
            btn.addEventListener("click", () => {

                document.getElementById("edit-room-id").value = btn.dataset.id;
                document.getElementById("edit-room-name").value = btn.dataset.name;
                document.getElementById("edit-capacity").value = btn.dataset.capacity;
                document.getElementById("edit-building").value = btn.dataset.building;
                document.getElementById("edit-status").value = btn.dataset.status;

            });
        });

        const editForm = document.getElementById("editRoomForm");

        editForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(editForm);
            formData.append("action", "updateRoom");
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if(csrfToken) formData.append("csrf_token", csrfToken);

            fetch("/public/api.php", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = "?page=admin-rooms";
                    } else {
                        console.log(data.message);

                        /* 
                        
                            CODE HERE WHEN UNSUCCESSFUL
        
                            Note: you can do console.log(data.message) here to see why unsuccessful
                        
                        */
                    }
                });
        });

        // event listener to delete button per list
        document.querySelectorAll(".delete-room-btn").forEach(btn => {
            btn.addEventListener("click", () => {

                const room_id = btn.dataset.id;
                const formData = new FormData();
                formData.append("action", "deleteRoom");
                formData.append("room-id", room_id);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if(csrfToken) formData.append("csrf_token", csrfToken);

                fetch("/public/api.php", {
                    method: "POST",
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = "?page=admin-rooms";
                        } else {
                            console.log(data.message);

                            /* 
                            
                                CODE HERE WHEN UNSUCCESSFUL
        
                                Note: you can do console.log(data.message) here to see why unsuccessful
                            
                            */
                        }
                    });

            });
        });


    </script>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>