<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Booking Request</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../public/assets/css/student/dashboard.css">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <style>
        .hide-date-text::-webkit-datetime-edit {
            color: transparent;
        }

        .hide-date-text {
            color: transparent;
        }
    </style>
</head>

<body>

    <!-- Sidebar Toggle for Mobile -->
    <input type="checkbox" id="sidebar-toggle">
    <label for="sidebar-toggle" class="sidebar-toggle-label">&#9776; Menu</label>

    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <h3 class="text-center mt-3 mb-4">Student</h3>
            <a href="?page=student">Dashboard</a>
            <a href="?page=student-rooms">Rooms</a>
            <a href="?page=student-submit" class="active">Submit Booking</a>
            <a href="?page=student-requests">My Requests</a>
            <a href="#" onclick="event.preventDefault(); new bootstrap.Modal(document.getElementById('studentLogoutModal')).show();">Logout</a>
        </div>

        <div class="main-content">
            <div class="topbar">
                <h4 class="fw-bold">Submit Booking Request</h4>
            </div>

            <div class="container-fluid mt-4">
                <div class="card-custom">
                    <form id="bookingForm">
                        <div id="feedbackMsg"></div>
                        <div class="mb-3">
                            <label for="roomSelect" class="form-label">Select Room</label>
                            <select id="roomSelect" name="room_id" class="form-select" required>
                                <option value="">Loading rooms...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="facultySelect" class="form-label">Select Faculty/Staff</label>
                            <select id="facultySelect" name="faculty_id" class="form-select" required>
                                <option value="">Loading faculty...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="dateSelect" class="form-label">Select Date</label>
                            <input type="text" id="dateSelect" name="date" class="form-control"
                                placeholder="Select Date" required>
                        </div>

                        <div class="mb-3">
                            <label for="startTime" class="form-label">Start Time</label>
                            <input type="time" id="startTime" name="start_time" min="07:00" max="19:00" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration (Hours)</label>
                            <input type="number" id="duration" name="duration" min="1" max="5" value="1" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="purpose" class="form-label">Purpose / Activity</label>
                            <textarea id="purpose" name="purpose" class="form-control" rows="3" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-bsu-red">Submit Request</button>
                        <p class="mt-2 text-muted">Notice: Request needs faculty or admin approval.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

    <script src="assets/js/student.js?v=<?php echo time(); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>