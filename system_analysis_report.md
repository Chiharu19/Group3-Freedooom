# System Analysis Report: Room Utilization System

## 1. System Overview
The Room Utilization System is a web-based application designed to manage room bookings and scheduling for an educational institution (specifically targeting CICS and CIT buildings). It serves three primary user roles: **Students**, **Faculty**, and **Administrators** (including Super Admins). The system facilitates room resource management through a request-approval workflow for students and direct booking capabilities for faculty and admins.

### Technology Stack
- **Backend**: Native PHP (Model-View-Controller architecture)
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Database**: MySQL (MariaDB)
- **Server**: Apache (via XAMPP)

## 2. Architecture Analysis

### MVC Pattern
The application follows a custom MVC structure:
- **Controllers** (`app/controllers/`): Primarily responsible for loading Views. They handle session verification and role-based access control (RBAC) before rendering a page.
  - *Example*: `FacultyController` checks if the user is a 'faculty' before loading `faculty_dashboard.php`.
- **Models** (`app/models/`): Contain the business logic and database interactions. They are instantiated by Controllers or API handlers.
  - *Example*: `Faculty.php` contains logic for `createBooking`, `checkConflict`, etc.
- **Views** (`app/views/`): PHP files that render the HTML. They often contain inline PHP for basic display logic but rely on JavaScript to fetch dynamic data via the API.
- **API** (`public/api.php` & `app/api/`): Acts as the bridge for AJAX requests. It routes actions (e.g., `submitRequest`, `approveRequest`) to specific API classes which then interact with Models.

### Routing
- **Page Routing**: Handled by `public/index.php`. It uses a simple query parameter `?page=name` to route to Controllers.
- **API Routing**: Handled by `public/api.php`. It looks for an `action` POST parameter to route to specific API classes (`AdminApi`, `FacultyApi`, etc.).

## 3. Database Schema (`room_utilization_db`)

The database consists of 5 main tables:

1.  **`users`**
    - Stores all user accounts.
    - **Key Columns**: `id`, `email`, `password` (hashed), `role` ('admin', 'faculty', 'student', 'super'), `status` ('active', 'inactive').

2.  **`rooms`**
    - Inventory of available rooms.
    - **Key Columns**: `id`, `room_name`, `building` (Enum: 'CICS', 'CIT'), `capacity`, `status` ('available', 'maintenance', 'booked').

3.  **`bookings`**
    - Represents *confirmed* time slots taken in a room.
    - **Key Columns**: `id`, `room_id`, `user_id`, `date`, `start_time`, `duration`, `end_time` (Generated Column).
    - **Note**: This table is the source of truth for availability.

4.  **`student_booking_requests`**
    - Stores requests made by students that need approval.
    - **Key Columns**: `id`, `student_id`, `faculty_id` (assigned approver), `status` ('pending', 'approved', 'denied', 'cancelled').
    - **Workflow Link**: When a request here is `approved`, a corresponding record is inserted into `bookings`.

5.  **`activity_logs`**
    - Audit trail for user actions (currently basic structure).

## 4. Key Workflows & Logic

### Booking Logic
- **Direct Booking (Faculty/Admin)**:
    - User selects room, date, and time.
    - System checks for conflicts in `bookings` table.
    - If free, record is inserted directly into `bookings`.
- **Request Flow (Student)**:
    - Student selects room/time and a *Faculty member* to review the request.
    - Request is saved to `student_booking_requests` with status 'pending'.
    - Faculty sees the request in their dashboard.

### Conflict Detection
- The system prevents double-booking using overlap logic:
    - `Existing_Start < New_End AND Existing_End > New_Start`
- **Implementation Variance**:
    - `Faculty` model uses a helper method `checkConflict` with PHP-based timestamp comparison.
    - `Admin` model often uses raw SQL queries to detect overlaps directly in the database.

### Approval Process
- When a Faculty/Admin approves a request:
    1.  **Re-validation**: The system checks *again* if the slot is still free in `bookings`.
    2.  **Creation**: Inserts a new record into `bookings`.
    3.  **Update**: Updates `student_booking_requests` status to 'approved'.

## 5. Code Quality & Recommendations

### Observations
1.  **Code Duplication**: Significant logic duplication exists between `Admin` and `Faculty` models, particularly for booking creation and request approval.
2.  **Logic Consistency**: Conflict checking is implemented in multiple ways (PHP looping vs SQL queries). SQL-based checking is generally more efficient and race-condition resistant.
3.  **Generated Columns**: The `bookings` table uses a generated column for `end_time`. This is a good practice for data consistency.

### Recommendations
1.  **Refactor Models**: Create a shared `BookingService` or `BookingModel` that handles core booking logic (create, conflict check, cancel) to be used by both `Admin` and `Faculty` classes.
2.  **Unify API**: Consolidate similar API endpoints. For example, `getDashboard` logic is very similar across roles.
3.  **Security**: Ensure all ID-based operations (cancel, edit) strictly enforce ownership checks (IDOR protection), which is already partially implemented but should be standardized.
4.  **Transaction Management**: Wrap the "Approve Request" flow (Booking Insert + Request Update) in a Database Transaction to ensure atomicity.

## 6. Conclusion
The system functions with a clear, logical separation of concerns. The database schema supports the core requirements effectively. The primary area for improvement is code maintenance through refactoring shared logic, specifically regarding booking management.
