document.addEventListener('DOMContentLoaded', function () {

    // Check which page we are on to run appropriate logic
    const params = new URLSearchParams(window.location.search);
    const page = params.get('page');

    if (page === 'student-submit') {
        initSubmitRequest();
    } else if (page === 'student-requests') {
        initMyRequests();
    } else if (page === 'student') {
        initDashboard();
    } else if (page === 'student-rooms') {
        initRoomAvailability();
    }

});

// Helper for CSRF
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

// ==========================================
// DASHBOARD PAGE
// ==========================================
function initDashboard() {
    const noticesList = document.getElementById('noticesList');
    const requestsTableBody = document.getElementById('dashboardRequestsTableBody');

    fetch('api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: 'action=dashboard'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Render Notices
                let noticesHtml = '';
                if (data.notices && data.notices.length > 0) {
                    data.notices.forEach(notice => {
                        noticesHtml += `<li>${notice}</li>`;
                    });
                } else {
                    noticesHtml = '<li>No new notices.</li>';
                }
                noticesList.innerHTML = noticesHtml;

                // Render Requests Table
                let requestsHtml = '';
                if (data.requests && data.requests.length > 0) {
                    data.requests.forEach(req => {
                        let badgeClass = 'bg-secondary';
                        if (req.status === 'approved') badgeClass = 'bg-success';
                        else if (req.status === 'denied') badgeClass = 'bg-danger';
                        else if (req.status === 'pending') badgeClass = 'bg-warning text-dark';

                        requestsHtml += `
                    <tr>
                        <td>#${req.id}</td>
                        <td>${req.room_name}</td>
                        <td>${req.date}</td>
                        <td><span class="badge ${badgeClass}">${req.status.toUpperCase()}</span></td>
                    </tr>
                    `;
                    });
                } else {
                    requestsHtml = '<tr><td colspan="4" class="text-center">No recent requests found.</td></tr>';
                }
                requestsTableBody.innerHTML = requestsHtml;

            } else {
                if (data.message === 'User not logged in') {
                    window.location.href = '?page=login';
                } else {
                    console.error('Dashboard load error:', data.message);
                    noticesList.innerHTML = `<li class="text-danger">Error loading notices: ${data.message}</li>`;
                }
            }
        })

        .catch(err => {
            console.error('Error fetching dashboard data:', err);
            noticesList.innerHTML = `<li class="text-danger">Network Error: ${err.message}. Check console for details.</li>`;
            requestsTableBody.innerHTML = `<tr><td colspan="4" class="text-danger text-center">Network Error: ${err.message}</td></tr>`;
        });
}

// ==========================================
// SUBMIT REQUEST PAGE
// ==========================================
function initSubmitRequest() {
    const roomSelect = document.getElementById('roomSelect');
    const facultySelect = document.getElementById('facultySelect');
    const dateSelect = document.getElementById('dateSelect');
    const form = document.getElementById('bookingForm');
    const feedbackMsg = document.getElementById('feedbackMsg');

    // Date Picker Logic
    if (dateSelect) {
        const handleFocus = () => {
            dateSelect.type = 'date';
            if (!dateSelect.value) dateSelect.classList.add('hide-date-text');
            dateSelect.showPicker();
        };

        dateSelect.addEventListener('focus', handleFocus);
        dateSelect.addEventListener('click', () => {
            // If already date type, show picker (focus might not trigger if already focused)
            if (dateSelect.type === 'date') dateSelect.showPicker();
        });

        dateSelect.addEventListener('input', () => {
            if (dateSelect.value) dateSelect.classList.remove('hide-date-text');
            else dateSelect.classList.add('hide-date-text');
        });

        dateSelect.addEventListener('blur', () => {
            if (!dateSelect.value) {
                dateSelect.type = 'text';
                dateSelect.classList.remove('hide-date-text');
            }
        });
    }

    // Fetch Rooms
    fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': getCsrfToken() },
        body: 'action=getRooms'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<option value="">Select Room</option>';
                // Grouping logic (optional, for now just list them)
                // If we want optgroups we need building info. The data has it.

                const grouped = {};
                data.data.forEach(room => {
                    const b = room.building || 'Other';
                    if (!grouped[b]) grouped[b] = [];
                    grouped[b].push(room);
                });

                for (const [building, rooms] of Object.entries(grouped)) {
                    html += `<optgroup label="${building}">`;
                    rooms.forEach(r => {
                        html += `<option value="${r.id}">${r.room_name} (Cap: ${r.capacity})</option>`;
                    });
                    html += `</optgroup>`;
                }
                roomSelect.innerHTML = html;
            } else {
                console.error('Failed to load rooms:', data.message);
            }
        })

        .catch(err => {
            console.error('Error fetching rooms:', err);
            roomSelect.innerHTML = `<option value="">Error loading rooms</option>`;
        });

    // Fetch Faculty
    fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': getCsrfToken() },
        body: 'action=getFaculty'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<option value="">Select Faculty</option>';
                data.data.forEach(f => {
                    html += `<option value="${f.id}">${f.full_name} (${f.email})</option>`;
                });
                facultySelect.innerHTML = html;
            } else {
                console.error('Failed to load faculty:', data.message);
            }
        })

        .catch(err => {
            console.error('Error fetching faculty:', err);
            facultySelect.innerHTML = `<option value="">Error loading faculty</option>`;
        });

    // Handle Form Submit
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append('action', 'submitRequest');
        formData.append('csrf_token', getCsrfToken());

        fetch('api.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Request submitted successfully!');
                    window.location.href = '?page=student-requests';
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error('Error submitting request:', err);
                alert('A network error occurred.');
            });
    });
}


// ==========================================
// MY REQUESTS PAGE
// ==========================================
function initMyRequests() {
    const tableBody = document.querySelector('#requestsTable tbody');

    fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': getCsrfToken() },
        body: 'action=myRequests'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center">No requests found.</td></tr>';
                    return;
                }

                let html = '';
                data.data.forEach(req => {
                    let badgeClass = 'bg-secondary';
                    if (req.status === 'approved') badgeClass = 'bg-success';
                    else if (req.status === 'denied') badgeClass = 'bg-danger';
                    else if (req.status === 'pending') badgeClass = 'bg-warning text-dark';

                    let actionHtml = '-';
                    if (req.status === 'pending') {
                        // Pass safe strings
                        const safeRoom = (req.room_name || '').replace(/'/g, "\\'");
                        const safePurp = (req.purpose || '').replace(/'/g, "\\'");
                        const safeDate = req.date;
                        const safeStart = req.start_time;
                        const safeEnd = req.end_time;

                        actionHtml = `
                            <button class="btn btn-sm btn-primary me-1" 
                                onclick="openEditModal(${req.id}, ${req.room_id}, '${safeRoom}', '${safeDate}', '${safeStart}', '${safeEnd}', '${safePurp}')">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="cancelRequest(${req.id})">Cancel</button>
                        `;
                    }

                    html += `
                    <tr>
                        <td>#${req.id}</td>
                        <td>${req.room_name}</td>
                        <td>${req.date}</td>
                        <td>${req.start_time_formatted} - ${req.end_time_formatted}</td>
                        <td><span class="badge ${badgeClass}">${req.status.toUpperCase()}</span></td>
                        <td>${req.faculty_name || 'N/A'}</td>
                        <td>${actionHtml}</td>
                    </tr>
                `;
                });
                tableBody.innerHTML = html;
            } else {
                if (data.message === 'User not logged in') {
                    window.location.href = '?page=login';
                } else {
                    tableBody.innerHTML = `<tr><td colspan="7" class="text-danger text-center">Error: ${data.message}</td></tr>`;
                }
            }
        })

        .catch(err => {
            console.error('Error fetching requests:', err);
            tableBody.innerHTML = `<tr><td colspan="7" class="text-danger text-center">Network Error: ${err.message}</td></tr>`;
        });

    // Global Cancel Function
    window.cancelRequest = function (requestId) {
        if (!confirm('Are you sure you want to cancel this request?')) return;

        const formData = new FormData();
        formData.append('action', 'cancelRequest');
        formData.append('request_id', requestId);
        formData.append('csrf_token', getCsrfToken());

        fetch('api.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Request cancelled!');
                    initMyRequests(); // Reload table
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Network error while cancelling');
            });
    }

    // Edit Request Functions
    window.openEditModal = function (id, roomId, roomName, date, start, end, purpose) {
        document.getElementById('editReqId').value = id;
        document.getElementById('editRoomId').value = roomId;
        document.getElementById('editRoomName').value = roomName;
        document.getElementById('editDate').value = date;
        document.getElementById('editStart').value = start;
        document.getElementById('editEnd').value = end;
        document.getElementById('editPurpose').value = purpose;

        const modal = new bootstrap.Modal(document.getElementById('editRequestModal'));
        modal.show();
    };

    const editForm = document.getElementById('editRequestForm');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(editForm);
            formData.append('action', 'editRequest');
            formData.append('csrf_token', getCsrfToken());

            fetch('api.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Request updated successfully');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editRequestModal'));
                        modal.hide();
                        initMyRequests();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Network error during update');
                });
        });
    }
}

// ==========================================
// ROOM AVAILABILITY PAGE
// ==========================================
let allRooms = []; // Global to store fetched rooms for filtering

function initRoomAvailability() {
    const container = document.getElementById('room-display-container');
    const searchName = document.getElementById('searchRoomName');
    const searchBuilding = document.getElementById('searchBuilding');
    const searchForm = document.getElementById('roomSearchForm');
    const buildingButtonsContainer = document.getElementById('buildingButtonsContainer');

    // 1. Fetch Rooms
    fetch('api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: 'action=getRooms'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allRooms = data.data; // Store for filtering
                renderRooms(allRooms);
                generateBuildingButtons(allRooms);
            } else {
                container.innerHTML = `<div class="text-danger text-center">Error: ${data.message}</div>`;
            }
        })
        .catch(err => {
            console.error('Network Error:', err);
            container.innerHTML = `<div class="text-danger text-center">Network Error: ${err.message}</div>`;
        });

    // 2. Setup Search Listeners
    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        filterRooms();
    });

    // Real-time search (optional)
    searchName.addEventListener('keyup', filterRooms);
    searchBuilding.addEventListener('keyup', filterRooms);

    window.filterRoomsByBuilding = function (building) {
        if (building === 'ALL') {
            renderRooms(allRooms);
        } else {
            const filtered = allRooms.filter(r => r.building === building);
            renderRooms(filtered);
        }
    }
}

function filterRooms() {
    const nameVal = document.getElementById('searchRoomName').value.toLowerCase();
    const buildVal = document.getElementById('searchBuilding').value.toLowerCase();

    const filtered = allRooms.filter(r => {
        const matchName = r.room_name.toLowerCase().includes(nameVal);
        // checking building text or checking if building property includes text
        const matchBuild = (r.building || '').toLowerCase().includes(buildVal);
        return matchName && matchBuild;
    });

    renderRooms(filtered);
}

function renderRooms(rooms) {
    const container = document.getElementById('room-display-container');
    if (rooms.length === 0) {
        container.innerHTML = '<div class="text-center mt-5">No rooms found matching your criteria.</div>';
        return;
    }

    // Group by Building
    const distinctBuildings = [...new Set(rooms.map(r => r.building))];
    let html = '';

    distinctBuildings.forEach(building => {
        const buildingRooms = rooms.filter(r => r.building === building);

        html += `
        <div class="building room-section mt-4" id="${building}-section">
            <h5 class="fw-bold border-bottom pb-2">${building || 'Other'} Building</h5>
            <div class="rooms d-flex flex-wrap gap-3 mt-3">
        `;

        buildingRooms.forEach(r => {
            // Determine class based on status
            let statusClass = 'status-free'; // Default green
            const status = r.status || 'available'; // Default to available if missing

            if (status !== 'available') {
                statusClass = 'status-faulty';
            }

            // Create Room Box
            html += `
            <div class="room-box ${statusClass} p-3 border rounded text-center" 
                 style="width: 120px; min-width: 120px; cursor: pointer; position:relative;"
                 onclick="showRoomDetails('${r.id}', '${r.room_name}', '${r.status}', ${r.capacity})">
                <div class="fw-bold">${r.room_name}</div>
                <div class="small">${(r.status || 'unknown').toUpperCase()}</div>
            </div>
            `;
        });

        html += `
            </div>
        </div>
        `;
    });

    container.innerHTML = html;
}

function generateBuildingButtons(rooms) {
    const container = document.getElementById('buildingButtonsContainer');
    const buildings = [...new Set(rooms.map(r => r.building))].filter(b => b); // remove nulls

    let html = '<button class="btn btn-sm btn-outline-secondary" onclick="filterRoomsByBuilding(\'ALL\')">All</button>';
    buildings.forEach(b => {
        html += `<button class="btn btn-sm btn-bsu-red" onclick="filterRoomsByBuilding('${b}')">${b}</button> `;
    });

    container.innerHTML = html;
}

function showRoomDetails(id, name, status, capacity) {
    const modalStart = new bootstrap.Modal(document.getElementById('roomDetailModal'));
    document.getElementById('roomDetailModalLabel').innerText = `Room: ${name}`;

    // Status Badge
    const statusEl = document.getElementById('modalRoomStatus');
    statusEl.innerText = (status || 'UNKNOWN').toUpperCase();
    statusEl.className = `badge ${status === 'available' ? 'bg-success' : 'bg-danger'}`;

    document.getElementById('modalRoomCapacity').innerText = capacity;

    // Load Schedule
    const today = new Date().toISOString().split('T')[0];
    const tbody = document.querySelector('#roomDetailModal tbody');
    tbody.innerHTML = '<tr><td colspan="3" class="text-center">Loading schedule...</td></tr>';

    modalStart.show();

    fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': getCsrfToken() },
        body: `action=getRoomSchedule&room_id=${id}&date=${today}`
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderScheduleTable(data.data, tbody);
            } else {
                tbody.innerHTML = `<tr><td colspan="3" class="text-danger text-center">${data.message}</td></tr>`;
            }
        })
        .catch(err => {
            console.error(err);
            tbody.innerHTML = `<tr><td colspan="3" class="text-danger text-center">Failed to load schedule</td></tr>`;
        });
}

function renderScheduleTable(bookings, tbody) {
    if (!bookings || bookings.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="3" class="text-center text-muted">
                    Room is free all day (no bookings found).
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    // Simple list of bookings. 
    // Ideally we would merge this with a full 8am-5pm grid, but for now list formatted is fine as per requirement "Show who is regularly schedules"

    bookings.forEach(b => {
        html += `
            <tr>
                <td>${b.start_time_formatted} - ${b.end_time_formatted}</td>
                <td>${b.purpose || 'Reserved'}</td>
                <td><span class="badge bg-danger">Booked</span></td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}
