<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Student Requests</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/faculty/faculty_dashboard.css">
  <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
</head>
<body>
<div class="wrapper">
  <nav class="sidebar">
    <h3 class="text-center mt-3 mb-4">Faculty</h3>
    <a href="index.php?page=faculty">Dashboard</a>
    <a href="index.php?page=faculty-rooms">Rooms</a>
    <a href="index.php?page=faculty-book">Book a Room</a>
    <a href="index.php?page=faculty-my-bookings">My Bookings</a>
    <a href="index.php?page=faculty-requests" class="active">Student Requests</a>
    <a href="#" onclick="event.preventDefault(); new bootstrap.Modal(document.getElementById('logoutModal')).show();" class="mt-3">Logout</a>
  </nav>

  <main class="main-content">
    <div class="topbar d-flex justify-content-between align-items-center px-4">
      <h4 class="fw-bold m-0">Student Requests</h4>
      <div>
        <a href="index.php?page=faculty-book" class="btn btn-bsu-red btn-sm">Book Room</a>
      </div>
    </div>

    <div class="container mt-4">
      <div class="card card-custom p-3">
        <div id="requests-list">
          <!-- requests populated by JS -->
        </div>
      </div>
    </div>
  </main>
</div>

</div>

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

<!-- Bootstrap & app -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/faculty/app.js"></script>
</body>
</html>
