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

        <?php include __DIR__ . '/../layouts/student_sidebar.php'; ?>

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
                            <input type="time" id="startTime" name="start_time" min="07:00" max="19:00"
                                class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="duration" class="form-label">Duration (Hours)</label>
                            <select id="duration" name="duration" class="form-select" required>
                                <option value="1" selected>1 Hour</option>
                                <option value="5">5 Hours</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="purpose" class="form-label">Purpose / Activity</label>
                            <textarea id="purpose" name="purpose" class="form-control" rows="3" required></textarea>
                        </div>

                        <script>
                            // Set minimum date to tomorrow
                            const dateSelect = document.getElementById('dateSelect');
                            const tomorrow = new Date();
                            tomorrow.setDate(tomorrow.getDate() + 1);
                            const minDate = tomorrow.toISOString().split('T')[0];

                            dateSelect.min = minDate;

                            // Also ensure if it switches type dynamically, min is preserved or re-applied if needed
                            dateSelect.addEventListener('focus', () => {
                                dateSelect.min = minDate;
                            });
                        </script>

                        <button type="submit" class="btn btn-bsu-red">Submit Request</button>
                        <p class="mt-2 text-muted">Notice: Request needs faculty or admin approval.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </div>



    <script src="assets/js/student.js?v=<?php echo time(); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>