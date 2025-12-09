/* faculty_dashboard.js
   Note: Main data loading is handled by app.js initDashboard().
   This file handles the FullCalendar specific logic using data fetched via API.
   
   To integrate properly, we need to fetch bookings for the calendar.
   app.js doesn't export the bookings list to global scope necessarily.
   
   We will reimplement loadCalendar to fetch myBookings.
*/

async function loadFacultyCalendar() {
    const calendarEl = document.getElementById('faculty-calendar');
    if (!calendarEl) return;

    // Fetch bookings directly
    // Re-using apiCall from app.js if possible, but it's not exported as a module.
    // Since app.js is loaded before, `apiCall` should be available globally.
    if (typeof apiCall === 'undefined') return;

    const res = await apiCall('facultyMyBookings');
    const events = [];

    if (res.success) {
        res.data.forEach(b => {
            // bookings: id, date, start_time, end_time, room_name, purpose
            events.push({
                title: `${b.room_name} - ${b.purpose}`,
                start: `${b.date}T${b.start_time}`,
                end: `${b.date}T${b.end_time}`,
                backgroundColor: b.status === 'Confirmed' ? '#28a745' : '#ffc107',
                borderColor: b.status === 'Confirmed' ? '#28a745' : '#ffc107'
            });
        });
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listWeek'
        },
        events: events
    });

    calendar.render();
}

document.addEventListener('DOMContentLoaded', () => {
    loadFacultyCalendar();
});
