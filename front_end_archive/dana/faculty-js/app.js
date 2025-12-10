/* app.js
   Provides sample data, rendering, conflict detection and UI interactions.
   This is in-memory demo logic. Replace/augment with AJAX calls to your backend.
*/

// ---------- Sample data ----------
const sampleRooms = [
  { id: 'R101', building: 'Main', floor: '1', name: 'R101 - Lecture' },
  { id: 'R102', building: 'Main', floor: '1', name: 'R102 - Lab' },
  { id: 'R201', building: 'Annex', floor: '2', name: 'R201 - Seminar' },
  { id: 'R202', building: 'Annex', floor: '2', name: 'R202 - Studio' }
];

// bookings in-memory (for demo)
let bookings = [
  { id: 1, room_id: 'R101', date: '2025-12-02', start: '09:00', end: '10:30', purpose: 'Lecture', owner: 'instructor@example.com', status: 'Confirmed' },
  { id: 2, room_id: 'R102', date: '2025-12-02', start: '11:00', end: '12:00', purpose: 'Lab session', owner: 'instructor@example.com', status: 'Confirmed' },
  { id: 3, room_id: 'R201', date: '2025-12-02', start: '13:00', end: '15:00', purpose: 'Seminar', owner: 'student1@example.com', status: 'Pending' }
];

// student requests sample
let requests = [
  { id: 101, student: 'student1@example.com', room_id: 'R201', date: '2025-12-02', start: '13:00', end: '15:00', purpose: 'Group meeting', assigned_to: 'instructor@example.com', status: 'Pending', comments: '' }
];

// utility
function mkId() { return Math.floor(Math.random()*100000); }
function timeToMinutes(t) { const [h,m]=t.split(':').map(Number); return h*60+m; }
function rangesOverlap(startA,endA,startB,endB) {
  return timeToMinutes(startA) < timeToMinutes(endB) && timeToMinutes(startB) < timeToMinutes(endA);
}

// ------------------ Dashboard page ------------------
document.addEventListener('DOMContentLoaded', () => {
  populateDashboard();
  populateRoomSelect();
  bindRoomAvailability();
  bindBookingForm();
  renderRoomAvailability();
  renderMyBookings();
  renderRequestsList();
});

// Populate small dashboard widgets
function populateDashboard(){
  const today = new Date().toISOString().slice(0,10);
  const myBookingsToday = bookings.filter(b=>b.owner==='instructor@example.com' && b.date===today);
  document.getElementById('dashboard-today-count') && (document.getElementById('dashboard-today-count').textContent = myBookingsToday.length);
  const pendingCount = requests.filter(r=>r.assigned_to==='instructor@example.com' && r.status==='Pending').length;
  document.getElementById('dashboard-requests-count') && (document.getElementById('dashboard-requests-count').textContent = pendingCount);

  const scheduleEl = document.getElementById('dashboard-schedule');
  if(scheduleEl){
    scheduleEl.innerHTML = myBookingsToday.length ? myBookingsToday.map(b=>`${b.date} ${b.start}-${b.end} ${b.room_id}`).join('<br>') : 'No bookings today.';
  }
}

// ------------------ Rooms listing & filter ------------------
function bindRoomAvailability(){
  const filterBtn = document.getElementById('filter-btn');
  const clearBtn = document.getElementById('clear-filter-btn');

  filterBtn && filterBtn.addEventListener('click', (e)=>{
    e.preventDefault();
    const date = document.getElementById('filter-date').value;
    const start = document.getElementById('filter-start').value;
    const end = document.getElementById('filter-end').value;
    renderRoomAvailability({ date, start, end });
  });

  clearBtn && clearBtn.addEventListener('click', ()=>{
    document.getElementById('filter-form').reset();
    renderRoomAvailability();
  });
}

function renderRoomAvailability(filter){
  const container = document.getElementById('rooms-container');
  if(!container) return;

  container.innerHTML = '';

  // group rooms by building
  const grouped = sampleRooms.reduce((acc, r) => {
    acc[r.building] = acc[r.building] || [];
    acc[r.building].push(r);
    return acc;
  }, {});

  for(const building in grouped){
    const bEl = document.createElement('div');
    bEl.className = 'building';

    bEl.innerHTML = `<h5>${building}</h5>`;
    const floors = grouped[building].reduce((acc, r)=>{
      acc[r.floor] = acc[r.floor] || [];
      acc[r.floor].push(r);
      return acc;
    }, {});
    for(const floor in floors){
      const floorEl = document.createElement('div');
      floorEl.className = 'floor';
      floorEl.innerHTML = `<div class="floor-label">Floor ${floor}</div><div class="rooms"></div>`;
      const roomsDiv = floorEl.querySelector('.rooms');

      floors[floor].forEach(r=>{
        let statusClass = 'status-free';
        let statusText = 'Available';

        // Check bookings for filter date/time
        if(filter && filter.date){
          const busy = bookings.some(b=>{
            if(b.room_id !== r.id) return false;
            if(b.date !== filter.date) return false;
            if(!filter.start || !filter.end) {
              // if only date is provided, any booking makes it occupied
              return true;
            }
            return rangesOverlap(filter.start, filter.end, b.start, b.end);
          });
          if(busy){
            statusClass = 'status-occupied';
            statusText = 'Occupied';
          }
        } else {
          // Without filter: check if today & now overlapped
          const today = new Date().toISOString().slice(0,10);
          const now = new Date();
          const nowStr = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
          const busyNow = bookings.some(b => b.room_id===r.id && b.date===today && rangesOverlap(b.start,b.end, nowStr, nowStr));
          if(busyNow) {
            statusClass = 'status-occupied';
            statusText = 'Occupied now';
          }
        }

        const roomBox = document.createElement('div');
        roomBox.className = `room-box ${statusClass}`;
        roomBox.innerHTML = `<div>${r.name}</div><small>${r.id}</small><div class="mt-1"><small>${statusText}</small></div>`;
        roomBox.addEventListener('click', ()=> openRoomSchedule(r));
        roomsDiv.appendChild(roomBox);
      });

      bEl.appendChild(floorEl);
    }

    container.appendChild(bEl);
  }
}

// Show modal schedule for a room
function openRoomSchedule(room){
  const modalEl = document.getElementById('roomScheduleModal');
  if(!modalEl) return;

  const modal = new bootstrap.Modal(modalEl);
  document.getElementById('modal-room-title').textContent = `${room.name} (${room.id})`;
  document.getElementById('modal-book-link').href = `book_room.html?room=${room.id}`;

  // build schedule table
  const scheduleBody = document.getElementById('modal-schedule-body');
  const roomBookings = bookings.filter(b=>b.room_id===room.id).sort((a,b)=> a.date.localeCompare(b.date) || a.start.localeCompare(b.start));
  if(roomBookings.length===0){
    scheduleBody.innerHTML = '<p>No bookings yet for this room.</p>';
  } else {
    const html = ['<div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th>Time</th><th>Purpose</th><th>Owner</th><th>Status</th></tr></thead><tbody>'];
    roomBookings.forEach(b=>{
      html.push(`<tr><td>${b.date}</td><td>${b.start} - ${b.end}</td><td>${b.purpose}</td><td>${b.owner}</td><td>${b.status}</td></tr>`);
    });
    html.push('</tbody></table></div>');
    scheduleBody.innerHTML = html.join('');
  }

  modal.show();
}

// ------------------ Booking form & conflict detection ------------------
function populateRoomSelect(){
  const sel = document.getElementById('room-select');
  if(!sel) return;
  sel.innerHTML = '<option value="">-- Select --</option>';
  sampleRooms.forEach(r=>{
    const opt = document.createElement('option');
    opt.value = r.id;
    opt.textContent = `${r.name} (${r.id})`;
    sel.appendChild(opt);
  });

  // If URL contains room param, preselect
  const params = new URLSearchParams(location.search);
  const roomParam = params.get('room');
  if(roomParam) sel.value = roomParam;
}

function bindBookingForm(){
  const form = document.getElementById('booking-form');
  if(!form) return;
  form.addEventListener('submit', (e)=>{
    e.preventDefault();
    submitBooking();
  });
}

function submitBooking(){
  const room = document.getElementById('room-select').value;
  const date = document.getElementById('booking-date').value;
  const start = document.getElementById('booking-start').value;
  const end = document.getElementById('booking-end').value;
  const purpose = document.getElementById('booking-purpose').value;
  const feedback = document.getElementById('booking-feedback');

  feedback.className = '';
  feedback.textContent = '';

  if(!room || !date || !start || !end || !purpose){
    feedback.className = 'text-danger';
    feedback.textContent = 'Please complete all required fields.';
    return;
  }
  if(timeToMinutes(end) <= timeToMinutes(start)){
    feedback.className = 'text-danger';
    feedback.textContent = 'End time must be after start time.';
    return;
  }

  // conflict detection
  const conflict = bookings.some(b=>{
    return b.room_id === room && b.date === date && rangesOverlap(start, end, b.start, b.end);
  });

  if(conflict){
    feedback.className = 'text-danger';
    feedback.innerHTML = 'Booking conflict detected for the selected room/time. <strong>Choose another time or room.</strong>';
    return;
  }

  // create booking (in-memory demo). status auto-confirm for instructor.
  const newBooking = {
    id: mkId(),
    room_id: room,
    date,
    start,
    end,
    purpose,
    owner: 'instructor@example.com',
    status: 'Confirmed'
  };
  bookings.push(newBooking);

  feedback.className = 'text-success';
  feedback.textContent = 'Booking created successfully.';

  // refresh relevant views if present
  renderRoomAvailability();
  renderMyBookings();
  populateDashboard();

  // optionally, redirect to My Bookings
  setTimeout(()=> { location.href = 'my_bookings.html'; }, 700);
}

// ------------------ My bookings rendering and actions ------------------
function renderMyBookings(){
  const tbody = document.querySelector('#bookings-table tbody');
  if(!tbody) return;
  tbody.innerHTML = '';
  const mine = bookings.filter(b=>b.owner==='instructor@example.com').sort((a,b)=> a.date.localeCompare(b.date) || a.start.localeCompare(b.start));
  mine.forEach(b=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${b.room_id}</td><td>${b.date}</td><td>${b.start} - ${b.end}</td><td>${b.purpose}</td><td>${b.status}</td>
      <td>
        <button class="btn btn-sm btn-outline-primary me-1" data-action="edit" data-id="${b.id}">Edit</button>
        <button class="btn btn-sm btn-outline-danger" data-action="cancel" data-id="${b.id}">Cancel</button>
      </td>`;
    tbody.appendChild(tr);
  });

  // bind actions
  tbody.querySelectorAll('button[data-action]').forEach(btn=>{
    btn.addEventListener('click', (e)=>{
      const id = Number(e.currentTarget.dataset.id);
      const action = e.currentTarget.dataset.action;
      if(action==='edit') openEditBooking(id);
      if(action==='cancel') cancelBooking(id);
    });
  });
}

function openEditBooking(id){
  const b = bookings.find(x=>x.id===id);
  if(!b) return;
  const modalEl = document.getElementById('editBookingModal');
  const modal = new bootstrap.Modal(modalEl);
  document.getElementById('edit-booking-id').value = b.id;
  document.getElementById('edit-date').value = b.date;
  document.getElementById('edit-start').value = b.start;
  document.getElementById('edit-end').value = b.end;
  document.getElementById('edit-purpose').value = b.purpose;
  document.getElementById('edit-feedback').textContent = '';

  // show modal
  modal.show();

  // attach save
  const saveBtn = document.getElementById('save-edit');
  saveBtn.onclick = () => {
    const id = Number(document.getElementById('edit-booking-id').value);
    const date = document.getElementById('edit-date').value;
    const start = document.getElementById('edit-start').value;
    const end = document.getElementById('edit-end').value;
    const purpose = document.getElementById('edit-purpose').value;
    const feedback = document.getElementById('edit-feedback');

    feedback.className = '';
    feedback.textContent = '';
    if(timeToMinutes(end) <= timeToMinutes(start)){
      feedback.className = 'text-danger';
      feedback.textContent = 'End must be after start.';
      return;
    }

    // conflict check vs other bookings
    const conflict = bookings.some(b=>{
      if(b.id === id) return false;
      return b.room_id === bookings.find(x=>x.id===id).room_id && b.date === date && rangesOverlap(start,end,b.start,b.end);
    });
    if(conflict){
      feedback.className = 'text-danger';
      feedback.textContent = 'Conflict found with another booking.';
      return;
    }

    const idx = bookings.findIndex(x=>x.id===id);
    if(idx>=0){
      bookings[idx].date = date;
      bookings[idx].start = start;
      bookings[idx].end = end;
      bookings[idx].purpose = purpose;
    }
    feedback.className = 'text-success';
    feedback.textContent = 'Saved.';
    renderMyBookings();
    renderRoomAvailability();
    populateDashboard();

    setTimeout(()=> modal.hide(), 600);
  };
}

function cancelBooking(id){
  if(!confirm('Cancel this booking?')) return;
  bookings = bookings.filter(b=>b.id!==id);
  renderMyBookings();
  renderRoomAvailability();
  populateDashboard();
}

// ------------------ Student requests ------------------
function renderRequestsList(){
  const el = document.getElementById('requests-list');
  if(!el) return;
  el.innerHTML = '';
  requests.forEach(r=>{
    const card = document.createElement('div');
    card.className = 'card mb-2';
    card.innerHTML = `<div class="card-body">
      <div class="d-flex justify-content-between">
        <div>
          <h6 class="mb-1">${r.student} — ${r.purpose}</h6>
          <div><small>Room ${r.room_id} • ${r.date} • ${r.start} - ${r.end}</small></div>
          <div><small>Comments: ${r.comments || '—'}</small></div>
        </div>
        <div class="text-end">
          <div class="mb-2"><strong>Status: ${r.status}</strong></div>
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-success" data-req-action="approve" data-id="${r.id}">Approve</button>
            <button class="btn btn-sm btn-danger" data-req-action="reject" data-id="${r.id}">Reject</button>
            <button class="btn btn-sm btn-outline-secondary" data-req-action="view" data-id="${r.id}">View</button>
          </div>
        </div>
      </div>
    </div>`;
    el.appendChild(card);
  });

  el.querySelectorAll('[data-req-action]').forEach(btn=>{
    btn.addEventListener('click', (ev)=>{
      const id = Number(ev.currentTarget.dataset.id);
      const action = ev.currentTarget.dataset.reqAction;
      if(action==='approve') handleRequestApprove(id);
      if(action==='reject') handleRequestReject(id);
      if(action==='view') alert(JSON.stringify(requests.find(x=>x.id===id), null, 2));
    });
  });

  // update dashboard count
  populateDashboard();
}

function handleRequestApprove(id){
  const req = requests.find(r=>r.id===id);
  if(!req) return;
  // simple conflict check on approval
  const conflict = bookings.some(b=> b.room_id===req.room_id && b.date===req.date && rangesOverlap(req.start, req.end, b.start, b.end));
  if(conflict){
    alert('Cannot approve — conflict with existing booking.');
    return;
  }
  // convert request into booking
  const nb = { id: mkId(), room_id: req.room_id, date: req.date, start: req.start, end: req.end, purpose: req.purpose, owner: req.assigned_to, status: 'Confirmed' };
  bookings.push(nb);
  req.status = 'Approved';
  renderRequestsList();
  renderRoomAvailability();
  renderMyBookings();
}

function handleRequestReject(id){
  const req = requests.find(r=>r.id===id);
  if(!req) return;
  req.status = 'Rejected';
  renderRequestsList();
}


// ------------------ small helper to refresh views when pages load ------------------
