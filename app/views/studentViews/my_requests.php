<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Booking Requests</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../public/assets/css/student/dashboard.css">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
</head>

<body>

    <!-- Sidebar toggle for mobile -->
    <input type="checkbox" id="sidebar-toggle" class="sidebar-toggle">
    <label for="sidebar-toggle" class="sidebar-toggle-label">☰ Menu</label>

    <div class="wrapper">

        <!-- Sidebar -->
        <div class="sidebar">
            <h3 class="text-center mt-3 mb-4">Student</h3>
            <a href="?page=student">Dashboard</a>
            <a href="?page=student-rooms">Rooms</a>
            <a href="?page=student-submit">Submit Booking</a>
            <a href="?page=student-requests" class="active">My Requests</a>
            <a href="?page=logout">Logout</a>
        </div>

        <!-- Main content -->
        <div class="main-content">
            <div class="topbar">
                <h4 class="fw-bold">My Booking Requests</h4>
            </div>

            <div class="container-fluid">
                <div class="card-custom table-responsive">
                    <table id="requestsTable" class="table table-striped table-hover table-custom">
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Room</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Faculty/Adviser</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center">Loading requests...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="editRequestModal" tabindex="-1">
      <div class="modal-dialog">
        <form id="editRequestForm" class="modal-content" onsubmit="return false;">
          <div class="modal-header">
            <h5 class="modal-title">Edit Request</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="editReqId" name="request_id">
            <input type="hidden" id="editRoomId" name="room_id"> <!-- Assuming room not changeable or hidden -->
            
            <div class="mb-2">
                <label>Room</label>
                <input type="text" id="editRoomName" class="form-control" readonly>
            </div>

            <div class="mb-2">
                <label>Date</label>
                <input type="date" id="editDate" name="date" class="form-control" required>
            </div>
            
            <div class="row g-2">
                <div class="col">
                    <label>Start Time</label>
                    <input type="time" id="editStart" name="start_time" class="form-control" required>
                </div>
                <div class="col">
                    <label>End Time</label>
                    <input type="time" id="editEnd" name="end_time" class="form-control" required>
                </div>
            </div>

            <div class="mb-2 mt-2">
                <label>Purpose</label>
                <textarea id="editPurpose" name="purpose" class="form-control" required></textarea>
            </div>

          </div>
          <div class="modal-footer">
            <button class="btn btn-bsu-red" type="submit">Save Changes</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Bootstrap Bundle with Popper (Required for Modals) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/student.js"></script>
</body>

</html>