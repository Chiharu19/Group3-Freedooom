<?php require __DIR__ . '/../layouts/header.php'; ?>

<link rel="stylesheet" href="../../../public/assets/css/superAdmin.css">
</head>

<body>

    <div class="wrapper">

        <div class="sidebar">
            <h3 class="text-center mt-3 mb-4 text-white">Super Admin</h3>
            <a href="?page=super-admin">Dashboard</a>
            <a href="#" class="active">Manage Admins</a>
            <a href="#" onclick="event.preventDefault(); new bootstrap.Modal(document.getElementById('saLogoutModal')).show();">Logout</a>
        </div>

        <div class="main-content">

            <div class="topbar d-flex justify-content-between align-items-center px-4">
                <h4 class="fw-bold">Manage Administrators</h4>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                    + Add New Admin
                </button>
            </div>

            <div class="container mt-4">

                <div class="card p-3 shadow-sm">
                    <h5 class="fw-bold mb-3">Admin Accounts</h5>

                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th width="160">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="adminTableBody">
                            <!-- JS will inject rows here -->
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>


    <!-- ADD ADMIN MODAL -->
    <div class="modal fade" id="addAdminModal">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <h5 class="fw-bold mb-3">Add New Admin</h5>

                <form id="addAdminForm">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control mb-2" name="full_name" required>

                    <label class="form-label">Email</label>
                    <input type="email" class="form-control mb-2" name="email" required>

                    <label class="form-label">Password</label>
                    <input type="password" class="form-control mb-2" name="password" required>

                    <button type="submit" class="btn btn-danger w-100 mt-2">Create Admin</button>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT ADMIN MODAL -->
    <div class="modal fade" id="editAdminModal">
        <div class="modal-dialog">
            <div class="modal-content p-3">
                <h5 class="fw-bold mb-3">Edit Admin</h5>

                <form id="editAdminForm">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control mb-2" name="full_name" id="edit_name" required>

                    <label class="form-label">Email</label>
                    <input type="email" class="form-control mb-2" name="email" id="edit_email" required>

                    <button type="submit" class="btn btn-primary w-100 mt-2">Save Changes</button>
                </form>
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
    document.addEventListener("DOMContentLoaded", function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        loadAdmins();

        // ADD
        document.getElementById("addAdminForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append("action", "addAdmin");
            formData.append("csrf_token", csrfToken);

            fetch("/public/api.php", { method: "POST", body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        alert("Admin added successfully!");
                        location.reload();
                    } else {
                        alert(data.message || "Error adding admin");
                    }
                });
        });

        // EDIT
        document.getElementById("editAdminForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append("action", "editAdmin");
            formData.append("csrf_token", csrfToken);

            fetch("/public/api.php", { method: "POST", body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        alert("Admin updated successfully!");
                        location.reload();
                    } else {
                        alert(data.message || "Error updating admin");
                    }
                });
        });
    });

    function loadAdmins() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const formData = new FormData();
        formData.append("action", "getAdminsList");
        formData.append("csrf_token", csrfToken);

        fetch("/public/api.php", { method: "POST", body: formData })
        .then(res => res.json())
        .then(data => {
            if (!data.data || data.data.length === 0) {
                document.getElementById("adminTableBody").innerHTML = `<tr><td colspan="4" class="text-center">No admins found</td></tr>`;
                return;
            }
            renderAdmins(data.data);
        });
    }

    function renderAdmins(users) {
        const tbody = document.getElementById("adminTableBody");
        tbody.innerHTML = "";
        
        users.forEach(user => {
            const statusBadge = user.status === "active" 
                ? `<span class="badge bg-success">Active</span>` 
                : `<span class="badge bg-secondary">Inactive</span>`;

            // Escaping for safety
            const safeName = user.full_name.replace(/"/g, '&quot;');
            const safeEmail = user.email.replace(/"/g, '&quot;');

            tbody.innerHTML += `
                <tr>
                    <td>${user.full_name}</td>
                    <td>${user.email}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick='openEdit(${user.id}, "${safeName}", "${safeEmail}")'>Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="toggleStatus(${user.id})">
                            ${user.status === "active" ? "Deactivate" : "Activate"}
                        </button>
                    </td>
                </tr>
            `;
        });
    }

    function openEdit(id, name, email) {
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_name").value = name;
        document.getElementById("edit_email").value = email;
        const modal = new bootstrap.Modal(document.getElementById("editAdminModal"));
        modal.show();
    }

    function toggleStatus(id) {
        if(!confirm("Are you sure?")) return;

        const formData = new FormData();
        formData.append("action", "toggleAdminStatus");
        formData.append("id", id);
        formData.append("csrf_token", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        fetch("/public/api.php", { method: "POST", body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                loadAdmins();
            } else {
                alert(data.message || "Error");
            }
        });
    }
    </script>

    <?php require __DIR__ . '/../layouts/footer.php'; ?>
