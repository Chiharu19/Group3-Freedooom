<?php require __DIR__ . '/../layouts/header.php'; ?>

<link rel="stylesheet" href="../../../public/assets/css/admin/adminDashboard.css">
<!-- Using same CSS as dashboard/schedules -->
</head>

<body>

    <div class="wrapper">

        <!-- Sidebar -->
        <div class="sidebar">
            <h3 class="text-center mt-3 mb-4">Admin</h3>
            <a href="?page=admin">Dashboard</a>
            <a href="?page=admin-rooms">Rooms</a>
            <a href="?page=admin-schedules">Schedules</a>
            <a href="#" class="active">Student Requests</a>
            <a href="?page=admin-users">Manage Users</a>
            <a href="#" onclick="event.preventDefault(); new bootstrap.Modal(document.getElementById('logoutModal')).show();">Logout</a>
        </div>

        <div class="main-content">

            <div class="topbar d-flex align-items-center px-4">
                <h5 class="m-0 fw-bold">Student Booking Requests</h5>
            </div>

            <div class="container py-4">

                <div class="card p-3 shadow-sm">
                    <h5 class="fw-bold mb-3">Pending Requests</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-custom-header">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Room</th>
                                    <th>Student</th>
                                    <th>Purpose</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="requests-body">
                                <tr><td colspan="6" class="text-center text-muted">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        // CSRF Token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        document.addEventListener('DOMContentLoaded', loadRequests);

        function loadRequests() {
            const formData = new FormData();
            formData.append('action', 'getStudentRequests');
            formData.append('status', 'pending');
            if (csrfToken) formData.append('csrf_token', csrfToken); // POST Body

            fetch('/public/api.php', {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('requests-body');
                if (!data.success || data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No pending requests.</td></tr>';
                    return;
                }

                tbody.innerHTML = data.data.map(r => `
                    <tr>
                        <td>${r.date}</td>
                        <td>${r.start_time} - ${r.duration}h</td>
                        <td>${r.room_name}</td>
                        <td>${r.student_name}</td>
                        <td>${r.purpose}</td>
                        <td>
                            <button class="btn btn-sm btn-success me-1" onclick="handleRequest(${r.id}, 'approve')">Approve</button>
                            <button class="btn btn-sm btn-danger" onclick="handleRequest(${r.id}, 'reject')">Deny</button>
                        </td>
                    </tr>
                `).join('');
            })
            .catch(err => console.error(err));
        }

        function handleRequest(id, action) {
            let comments = '';
            if (action === 'reject') {
                comments = prompt("Reason for denial:");
                if (comments === null) return; // Cancelled
                if (!comments.trim()) {
                    alert("Reason is required for denial.");
                    return;
                }
            }

            if (!confirm(`Are you sure you want to ${action} this request?`)) return;

            const formData = new FormData();
            formData.append('action', 'adminActionRequest');
            formData.append('request_id', id);
            formData.append('req_action', action);
            formData.append('comments', comments || '');
            if (csrfToken) formData.append('csrf_token', csrfToken);

            fetch('/public/api.php', {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Success!');
                    loadRequests();
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    </script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
