<?php
// Helper to set active class
$page = $_GET['page'] ?? 'student';
?>
<div class="sidebar">
    <h3 class="text-center mt-3 mb-4">Student</h3>
    <a href="?page=student" class="<?= $page === 'student' ? 'active' : '' ?>">Dashboard</a>
    <a href="?page=student-rooms" class="<?= $page === 'student-rooms' ? 'active' : '' ?>">Rooms</a>
    <a href="?page=student-submit" class="<?= $page === 'student-submit' ? 'active' : '' ?>">Submit Booking</a>
    <a href="?page=student-requests" class="<?= $page === 'student-requests' ? 'active' : '' ?>">My Requests</a>
    <a href="#"
        onclick="event.preventDefault(); new bootstrap.Modal(document.getElementById('studentLogoutModal')).show();">Logout</a>
</div>

<!-- Student Logout Modal -->
<div class="modal fade" id="studentLogoutModal" tabindex="-1">
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
                <a href="?page=logout" class="btn btn-primary">Logout</a>
            </div>
        </div>
    </div>
</div>