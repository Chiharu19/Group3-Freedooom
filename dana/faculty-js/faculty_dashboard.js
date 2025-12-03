/* faculty_dashboard.js
   Dashboard Calendar + Metrics (Improved & Safe)
*/

const CURRENT_INSTRUCTOR = "instructor@example.com";

// Ensure global arrays exist
window.bookings = window.bookings || [];
window.requests = window.requests || [];
window.modification_requests = window.modification_requests || [];

/* ---------------------------------------------------------
   BUILD EVENTS FOR FULLCALENDAR
--------------------------------------------------------- */
function getInstructorEvents() {
    if (!Array.isArray(bookings)) return [];

    return bookings
        .filter(b => b.owner === CURRENT_INSTRUCTOR)
        .map(b => ({
            id: String(b.id),
            title: `${b.room_id} — ${b.purpose}`,
            start: `${b.date}T${b.start}`,
            end: `${b.date}T${b.end}`,
            extendedProps: b
        }));
}

/* ---------------------------------------------------------
   UPDATE DASHBOARD STATS WIDGETS
--------------------------------------------------------- */
function updateDashboardNumbers() {
    if (!document.getElementById("dashboard-today-count")) return;

    const today = new Date().toISOString().slice(0, 10);

    const todayBookings = bookings.filter(
        b => b.owner === CURRENT_INSTRUCTOR && b.date === today
    );

    document.getElementById("dashboard-today-count").textContent =
        todayBookings.length;

    document.getElementById("dashboard-today-list").innerHTML =
        todayBookings.length
            ? todayBookings.map(b => `${b.start}-${b.end} • ${b.room_id}`).join("<br>")
            : "No bookings today.";

    const pending = requests.filter(
        r => r.assigned_to === CURRENT_INSTRUCTOR && r.status === "Pending"
    );

    document.getElementById("dashboard-requests-count").textContent =
        pending.length;

    document.getElementById("dashboard-requests-list").innerHTML =
        pending.length
            ? pending.map(r => `${r.date} • ${r.room_id}`).join("<br>")
            : "No pending requests.";
}

/* ---------------------------------------------------------
   EVENT DETAIL MODAL
--------------------------------------------------------- */
function openEventModal(b) {
    if (!b) return;

    document.getElementById("eventDetailTitle").textContent =
        `${b.room_id} • ${b.date} ${b.start}-${b.end}`;

    document.getElementById("eventDetailBody").innerHTML = `
        <div><strong>Purpose:</strong> ${b.purpose}</div>
        <div><strong>Status:</strong> ${b.status}</div>
        <div><strong>Owner:</strong> ${b.owner}</div>
    `;

    const requestBtn = document.getElementById("requestModBtn");

    // Only show if booking belongs to someone else
    requestBtn.style.display =
        b.owner === CURRENT_INSTRUCTOR ? "none" : "inline-block";

    requestBtn.onclick = () => {
        const reason = prompt("Enter reason for modification:");

        if (!reason || reason.trim() === "") {
            alert("You must provide a reason.");
            return;
        }

        modification_requests.push({
            id: "MR-" + Date.now(),
            requester: CURRENT_INSTRUCTOR,
            bookingId: b.id,
            reason: reason,
            status: "Pending",
            createdAt: new Date().toISOString()
        });

        alert("Modification request submitted!");
    };

    new bootstrap.Modal(document.getElementById("eventDetailModal")).show();
}

/* ---------------------------------------------------------
   RENDER FULLCALENDAR
--------------------------------------------------------- */
function loadCalendar() {
    const calendarEl = document.getElementById("faculty-calendar");
    if (!calendarEl) return;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        height: "auto",
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek"
        },
        events: getInstructorEvents(),

        eventClick: info => {
            // info.event.extendedProps holds the booking object
            openEventModal(info.event.extendedProps);
        }
    });

    calendar.render();
    window.dashboardCalendar = calendar;
}

/* ---------------------------------------------------------
   AUTO-REFRESH CALENDAR EVENTS
--------------------------------------------------------- */
function refreshCalendar() {
    if (!window.dashboardCalendar) return;

    window.dashboardCalendar.removeAllEvents();
    window.dashboardCalendar.addEventSource(getInstructorEvents());
}

/* ---------------------------------------------------------
   INIT
--------------------------------------------------------- */
document.addEventListener("DOMContentLoaded", () => {
    updateDashboardNumbers();
    loadCalendar();
});
