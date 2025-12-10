<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>My Bookings</title>

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
    <a href="index.php?page=faculty-my-bookings" class="active">My Bookings</a>
    <a href="index.php?page=faculty-requests">Student Requests</a>
    <a href="#" onclick="event.preventDefault(); new bootstrap.Modal(document.getElementById('logoutModal')).show();" class="mt-3">Logout</a>
  </nav>

  <main class="main-content">
    <div class="topbar d-flex justify-content-between align-items-center px-4">
      <h4 class="fw-bold m-0">My Bookings</h4>
      <div>
        <a href="index.php?page=faculty-book" class="btn btn-bsu-red btn-sm">New Booking</a>
      </div>
    </div>

    <div class="container mt-4">
      <div class="card card-custom p-3">
        <div class="table-responsive">
          <table class="table table-hover" id="bookings-table">
            <thead class="table-light">
              <tr>
                <th>Room</th>
                <th>Date</th>
                <th>Time</th>
                <th>Purpose</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <!-- populated by JS -->
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </main>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editBookingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editBookingForm" class="modal-content" onsubmit="return false;">
      <div class="modal-header">
        <h5 class="modal-title">Edit Booking</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editBookingId" name="booking_id" />
        <div class="mb-2">
          <label class="form-label">Date</label>
          <input id="editDate" name="edit_date" data-field="edit_date" type="date" class="form-control" required />
        </div>
        <div class="row g-2">
          <div class="col">
            <label class="form-label">Start</label>
            <input id="editStartTime" name="edit_start" data-field="edit_start" type="time" class="form-control" required />
          </div>
          <div class="col">
            <label class="form-label">Duration (Hours)</label>
            <input id="editDuration" name="duration" type="number" class="form-control" value="1" required />
          </div>
        </div>
        <div class="mb-2 mt-2">
          <label class="form-label">Purpose</label>
          <textarea id="editPurpose" name="edit_purpose" data-field="edit_purpose" rows="3" class="form-control"></textarea>
        </div>
        <div id="edit-feedback" role="status" aria-live="polite"></div>
      </div>
      <div class="modal-footer">
        <button id="save-edit" class="btn btn-bsu-red">Save changes</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </form>
  </div>
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
