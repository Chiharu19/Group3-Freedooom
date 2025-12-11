<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Availability</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../public/assets/css/student/dashboard.css">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
</head>

<body>

    <!-- Sidebar Toggle for Mobile -->
    <input type="checkbox" id="sidebar-toggle">
    <label for="sidebar-toggle" class="sidebar-toggle-label">&#9776; Menu</label>

    <div class="wrapper">
        <?php include __DIR__ . '/../layouts/student_sidebar.php'; ?>

        <div class="main-content">
            <div class="topbar">
                <h4 class="fw-bold">Room Availability</h4>
                <span class="fw-semibold">Check which rooms are free or occupied.</span>
            </div>

            <div class="container-fluid mt-4">
                <!-- Search Removed as per requirements -->


                <!-- Building Selection -->
                <div class="select-building-area" id="buildingFilterButtons">
                    <!-- Dynamic buttons will optionally go here or we keep them static if buildings are fixed -->
                    <p class="fw-bold m-0">Select Building</p>
                    <div class="d-flex gap-2 mt-2 flex-wrap" id="buildingButtonsContainer">
                        <!-- Buttons will be generated or we can leave static if specific requirements exist -->
                        <!-- For now, we'll let JS populate or filter based on data -->
                        <button class="btn btn-sm btn-outline-secondary"
                            onclick="filterRoomsByBuilding('ALL')">All</button>
                    </div>
                </div>

                <!-- Room Display Container -->
                <div id="room-display-container">
                    <div class="text-center mt-5">Loading rooms...</div>
                </div>

                <!-- Room Detail Modal -->
                <div class="modal fade" id="roomDetailModal" tabindex="-1" aria-labelledby="roomDetailModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="roomDetailModalLabel">Room Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2"><strong>Status:</strong> <span id="modalRoomStatus"
                                        class="badge"></span></p>
                                <p class="mb-3"><strong>Capacity:</strong> <span id="modalRoomCapacity"></span></p>

                                <h6 class="fw-bold mt-4">Today's Schedule</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Time</th>
                                                <th>Event / Purpose</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dynamic Schedule Rows -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/student.js"></script>
</body>

</html>