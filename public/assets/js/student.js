document.addEventListener('DOMContentLoaded', function () {

    // Check which page we are on to run appropriate logic
    const path = window.location.pathname;

    if (path.includes('submit_request.html')) {
        initSubmitRequest();
    } else if (path.includes('my_requests.html')) {
        initMyRequests();
    } else if (path.includes('dashboard.html')) {
        // initDashboard();
    }

});

// ==========================================
// SUBMIT REQUEST PAGE
// ==========================================
function initSubmitRequest() {
    const roomSelect = document.getElementById('roomSelect');
    const facultySelect = document.getElementById('facultySelect');
    const form = document.getElementById('bookingForm');
    const feedbackMsg = document.getElementById('feedbackMsg');

    // Fetch Rooms
    fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=getRooms'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<option value="">-- Select a room --</option>';
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
        .catch(err => console.error('Error fetching rooms:', err));

    // Fetch Faculty
    fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=getFaculty'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<option value="">-- Select Faculty --</option>';
                data.data.forEach(f => {
                    html += `<option value="${f.id}">${f.full_name} (${f.email})</option>`;
                });
                facultySelect.innerHTML = html;
            } else {
                console.error('Failed to load faculty:', data.message);
            }
        })
        .catch(err => console.error('Error fetching faculty:', err));

    // Handle Form Submit
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append('action', 'submitRequest');

        fetch('api.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Request submitted successfully!');
                    window.location.href = 'my_requests.html';
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
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=myRequests'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No requests found.</td></tr>';
                    return;
                }

                let html = '';
                data.data.forEach(req => {
                    let badgeClass = 'bg-secondary';
                    if (req.status === 'approved') badgeClass = 'bg-success';
                    else if (req.status === 'denied') badgeClass = 'bg-danger';
                    else if (req.status === 'pending') badgeClass = 'bg-warning text-dark';

                    html += `
                    <tr>
                        <td>#${req.id}</td>
                        <td>${req.room_name}</td>
                        <td>${req.date}</td>
                        <td>${req.start_time_formatted} - ${req.end_time_formatted}</td>
                        <td><span class="badge ${badgeClass}">${req.status.toUpperCase()}</span></td>
                        <td>${req.faculty_name || 'N/A'}</td>
                    </tr>
                `;
                });
                tableBody.innerHTML = html;
            } else {
                if (data.message === 'User not logged in') {
                    window.location.href = 'login.html';
                } else {
                    tableBody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">Error: ${data.message}</td></tr>`;
                }
            }
        })
        .catch(err => console.error('Error fetching requests:', err));
}
