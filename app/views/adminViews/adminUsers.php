<?php require __DIR__ . '/../layouts/header.php'; ?>

<link rel="stylesheet" href="../../../public/assets/css/admin/adminDashboard.css">
</head>

<body>

    <div class="wrapper">

        <div class="sidebar">
            <h3 class="text-center mt-3 mb-4">Admin</h3>
            <a href="?page=admin">Dashboard</a>
            <a href="?page=admin-rooms">Rooms</a>
            <a href="?page=admin-schedules">Schedules</a>
            <a href="?page=admin-requests">Student Requests</a>
            <a href="#" class="active">Manage Users</a>
            <a href="#" onclick="event.preventDefault(); new bootstrap.Modal(document.getElementById('logoutModal')).show();">Logout</a>
        </div>

        <div class="main-content">

            <div class="topbar d-flex justify-content-between align-items-center px-4">
                <h4 class="fw-bold">Manage Users</h4>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    + Add User
                </button>
            </div>

            <div class="container mt-4">

                <div class="card p-3 shadow-sm">
                    <h5 class="fw-bold mb-3">Faculty/Staff Accounts</h5>

                    <table class="table table-bordered table-striped table-custom-header">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th width="160">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <!-- JS will inject rows here -->
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="addUserModal">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <h5 class="fw-bold mb-3">Add New User</h5>

                <form name="addUserForm" id="addUserForm">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full-name" class="form-control mb-2">

                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control mb-2">

                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control mb-2">

                    <label class="form-label">Role</label>
                    <select class="form-select mb-2" name="role" required>
                        <option value="faculty">Faculty/Staff</option>
                        <option value="student">Student</option>
                    </select>

                    <button type="submit" class="btn btn-danger w-100 mt-2">Add User</button>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <h5 class="fw-bold mb-3">Edit User</h5>

                <form name="editUserForm" id="editUserForm">
                    <input type="hidden" name="user-id" id="edit-user-id">
                    
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full-name" id="edit-full-name" class="form-control mb-2" required>

                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="edit-email" class="form-control mb-2" required>

                    <label class="form-label">Role</label>
                    <select class="form-select mb-2" name="role" id="edit-role">
                        <option value="faculty">Faculty</option>
                        <option value="student">Student</option>
                        <option value="admin">Admin</option>
                    </select>

                    <button type="submit" class="btn btn-danger w-100 mt-2">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <script>

    function loadUsers() {

        const formData = new FormData();
        formData.append("action", "getUsersList");
        
        // CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if(csrfToken) formData.append('csrf_token', csrfToken);

        fetch("/public/api.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {

            // If API structure expected is { status: true/false, data: [...] }
            if (!data || !data.data) {
                renderEmpty();
                return;
            }

            const users = data.data;

            if (users.length === 0) {
                renderEmpty();
                return;
            }

            renderUsers(users);
        })
        .catch(err => {
            console.error(err);
            renderError();
        });
    }

    function renderUsers(users) {
        const tbody = document.getElementById("userTableBody");
        tbody.innerHTML = "";

        users.forEach(user => {

            const safeName = escapeHTML(user.full_name);
            const safeEmail = escapeHTML(user.email);
            const safeRole = escapeHTML(user.role);

            const statusBadge = user.status === "active"
                ? `<span class="badge bg-success">Active</span>`
                : `<span class="badge bg-secondary">Inactive</span>`;

            tbody.innerHTML += `
                <tr>
                    <td>${safeName}</td>
                    <td>${safeEmail}</td>
                    <td>${safeRole}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="openEditUserModal(${user.id}, '${safeName}', '${safeEmail}', '${safeRole}')">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="toggleStatus(${user.id}, '${user.status}')">
                            ${user.status === "active" ? "Deactivate" : "Activate"}
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    function renderEmpty() {
        document.getElementById("userTableBody").innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted py-3">
                    No users found.
                </td>
            </tr>
        `;
    }

    function renderError() {
        document.getElementById("userTableBody").innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-danger py-3">
                    Failed to load users.
                </td>
            </tr>
        `;
    }

    // Basic HTML escaping to prevent XSS
    function escapeHTML(str) {
        return str?.replace(/[&<>"']/g, char => ({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#39;"
        }[char])) || "";
    }

    function toggleStatus(userId, newStatus){
        
        if(!confirm("Are you sure?")) return;
        const formData = new FormData();
        formData.append("action", "changeUserStatus");
        formData.append("user-id", userId);
        formData.append("new-status", newStatus);

        // CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if(csrfToken) formData.append('csrf_token', csrfToken);

        fetch("/public/api.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                window.location.href = "?page=admin-users";
            }else{
                console.log(data.message);
            }
        })
        .catch(err => {
            console.error(err);
        });
        
    }

    function attachEditListeners() {
        // We need to attach listeners to the edit buttons dynamically or via delegation.
        // Or simpler: just updating the onclick in renderUsers to pass all data or select row.
    }

    // Attach to window so we can call it from HTML onclick
    window.openEditUserModal = function(id, name, email, role) {
        document.getElementById('edit-user-id').value = id;
        document.getElementById('edit-full-name').value = name;
        document.getElementById('edit-email').value = email;
        document.getElementById('edit-role').value = role;
        
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }

    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(editUserForm);
            formData.append("action", "editUser");

            // CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if(csrfToken) formData.append('csrf_token', csrfToken);

            fetch("/public/api.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    alert("User updated successfully");
                    location.reload(); // or just loadUsers()
                }else{
                    alert(data.message || "Failed to update user");
                }
            })
            .catch(err => {
                console.error(err);
                alert("An error occurred");
            });
        });
    }

    const addUserForm = document.getElementById('addUserForm');
    addUserForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(addUserForm);
        formData.append("action", "addUser");
        
        // CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if(csrfToken) formData.append('csrf_token', csrfToken);

        fetch("/public/api.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                window.location.href = "?page=admin-users";
            }else{
                console.log(data.message);
            }
        })
        .catch(err => {
            console.error(err);
        });
    });

    loadUsers();

    </script>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>