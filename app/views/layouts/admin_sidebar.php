<?php
$page = $_GET['page'] ?? 'admin';
?>
<div class="sidebar">
    <h3 class="text-center mt-3 mb-4">Admin</h3>
    <a href="?page=admin" class="<?= $page === 'admin' ? 'active' : '' ?>">Dashboard</a>
    <a href="?page=admin-rooms" class="<?= $page === 'admin-rooms' ? 'active' : '' ?>">Rooms</a>
    <a href="?page=admin-schedules" class="<?= $page === 'admin-schedules' ? 'active' : '' ?>">Schedules</a>
    <a href="?page=admin-requests" class="<?= $page === 'admin-requests' ? 'active' : '' ?>">Student Requests</a>
    <a href="?page=admin-users" class="<?= $page === 'admin-users' ? 'active' : '' ?>">Manage Users</a>
    <a href="#"
        onclick="event.preventDefault(); new bootstrap.Modal(document.getElementById('logoutModal')).show();">Logout</a>
</div>