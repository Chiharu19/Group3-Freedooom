<?php
// Helper to set active class
$page = $_GET['page'] ?? 'faculty';
?>
<nav class="sidebar">
    <h3 class="text-center mt-3 mb-4">Faculty</h3>
    <a href="index.php?page=faculty" class="<?= $page === 'faculty' ? 'active' : '' ?>">Dashboard</a>
    <a href="index.php?page=faculty-rooms" class="<?= $page === 'faculty-rooms' ? 'active' : '' ?>">Rooms</a>
    <a href="index.php?page=faculty-book" class="<?= $page === 'faculty-book' ? 'active' : '' ?>">Book a Room</a>
    <a href="index.php?page=faculty-my-bookings" class="<?= $page === 'faculty-my-bookings' ? 'active' : '' ?>">My
        Bookings</a>
    <a href="index.php?page=faculty-requests" class="<?= $page === 'faculty-requests' ? 'active' : '' ?>">Student
        Requests</a>
    <a href="#" onclick="event.preventDefault(); new bootstrap.Modal(document.getElementById('logoutModal')).show();"
        class="mt-3">Logout</a>
</nav>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1">
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
                <a href="index.php?page=logout" class="btn btn-primary">Logout</a>
            </div>
        </div>
    </div>
</div>