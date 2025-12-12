<?php
$page = $_GET['page'] ?? 'super-admin';
?>
<div class="sidebar">
    <h3 class="text-center mt-3 mb-4 text-white">Super Admin</h3>
    <a href="?page=super-admin" class="<?= $page === 'super-admin' ? 'active' : '' ?>">Dashboard</a>
    <a href="?page=super-admin-users" class="<?= $page === 'super-admin-users' ? 'active' : '' ?>">Manage Admins</a>
    <a href="?page=super-admin-transfer" class="<?= $page === 'super-admin-transfer' ? 'active' : '' ?>">Transfer
        Rights</a>
    <a href="#"
        onclick="event.preventDefault(); new bootstrap.Modal(document.getElementById('saLogoutModal')).show();">Logout</a>
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