# Room Utilization System

A web-based application designed to manage room bookings and scheduling for educational institutions, specifically targeting CICS and CIT buildings. The system streamlines resource management through role-based access for Students, Faculty, and Administrators.

## 🚀 Features

### User Roles
*   **Students**: View room availability, submit booking requests, and track request status.
*   **Faculty**: View availability and book rooms directly without approval.
*   **Admins**: Manage specific building resources, approve/reject student requests, and oversee schedules.
*   **Super Admin**: Full system control, including managing other admins and global settings.

### Core Functionality
*   **Booking System**:
    *   **Direct Booking**: Immediate reservation for Faculty and Admins.
    *   **Request-Approval Flow**: Students submit requests which require Faculty or Admin approval.
    *   **Conflict Detection**: Automated checks to prevent double-booking of rooms.
*   **Resource Management**: Inventory of rooms with status tracking (Available, Maintenance, Booked).
*   **Activity Logging**: Audit trails for system actions.
*   **Notifications**: Email notifications for booking status updates (via PHPMailer).

## 🛠️ Technology Stack

*   **Backend**: Native PHP (Custom MVC Architecture)
*   **Database**: MariaDB (MySQL)
*   **Frontend**: HTML5, CSS3, Vanilla JavaScript
*   **Server**: Apache HTTP Server (via XAMPP)
*   **Dependencies**: PHPMailer (managed via Composer)

## 📦 Detailed Installation & Setup Guide

### 1. Prerequisites
Ensure you have the following installed:
*   **XAMPP**: Recommended implementation for Apache + MySQL/MariaDB. [Download XAMPP](https://www.apachefriends.org/download.html)
*   **Composer**: Dependency manager for PHP. [Download Composer](https://getcomposer.org/download/)

### 2. Project Placement
1.  Navigate to your XAMPP installation directory (default: `C:\xampp`).
2.  Open the `htdocs` folder.
3.  Place the project folder here.
    *   **Correct Path**: `C:\xampp\htdocs\Group3-Freedooom`

### 3. Database Configuration (Step-by-Step)
1.  **Start MySQL**: Open XAMPP Control Panel and start the **MySQL** module.
2.  **Access phpMyAdmin**: Open your browser and go to [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
3.  **Create Database**:
    *   Click on the **New** button in the left sidebar.
    *   **Database Name**: Enter exactly `room_utilization_db`.
    *   **Collation**: Select `utf8mb4_general_ci` (recommended).
    *   Click **Create**.
4.  **Import Data**:
    *   Select the newly created `room_utilization_db` from the sidebar.
    *   Click on the **Import** tab in the top navigation bar.
    *   Under "File to import", click **Choose File**.
    *   Navigate to: `C:\xampp\htdocs\Group3-Freedooom\db_dump\`
    *   Select: `room_utilization_db - final.sql`
    *   Scroll down and click **Import** (or **Go**).
    *   *Success Message*: "Import has been successfully finished..."
5.  **Verify Connection Config** (Optional):
    *   If your MySQL root password is **not** empty (default is empty), you must update the config.
    *   Open `app/config/database.php`.
    *   Update `$pass = '';` to your actual password.

### 4. Email Configuration
1.  **Navigate to Config Directory**:
    *   Go to `app/config/`.
2.  **Create Configuration File**:
    *   Create a new file named `email_config.php`.
3.  **Add Configuration Settings**:
    *   Paste the following code into the file and update with your SMTP details:
    ```php
    <?php
    return [
        'host' => 'smtp.gmail.com', // or your SMTP host
        'username' => 'your_email@gmail.com',
        'password' => 'your_app_password', // App Password if using Gmail
        'encryption' => 'tls', // 'tls' or 'ssl'
        'port' => 587, // 587 for tls, 465 for ssl
        'from_address' => 'no-reply@yourdomain.com',
        'from_name' => 'Room Utilization System'
    ];
    ```
    *   *Note: This file is ignored by Git to protect your credentials.*

### 5. Install Dependencies
1.  Open Command Prompt (cmd) or PowerShell.
2.  Navigate to the project directory:
    ```bash
    cd C:\xampp\htdocs\Group3-Freedooom
    ```
3.  Install PHP libraries via Composer:
    ```bash
    composer install
    ```
    *   *This will create a `vendor` folder containing PHPMailer.*

### 6. Apache Configuration (httpd.conf)
This step points the server root directly to your project folder.

1.  **Locate Config File**:
    *   Go to `C:\xampp\apache\conf\`
    *   Open `httpd.conf` in a text editor (Notepad, VS Code, etc.).
2.  **Modify DocumentRoot**:
    *   Search for `DocumentRoot` (around line 250).
    *   Change the existing path to your project path:
        ```apache
        DocumentRoot "C:/xampp/htdocs/Group3-Freedooom"
        ```
    *   Immediately below that line, modify the `<Directory>` path as well:
        ```apache
        <Directory "C:/xampp/htdocs/Group3-Freedooom">
        ```
3.  **Verify Directory Permissions**:
    *   Ensure the block looks like this (standard XAMPP setup):
        ```apache
        <Directory "C:/xampp/htdocs/Group3-Freedooom">
            Options Indexes FollowSymLinks
            AllowOverride All
            Require all granted
        </Directory>
        ```
4.  **Save & Restart**:
    *   Save the `httpd.conf` file.
    *   In XAMPP Control Panel, **Stop** and then **Start** the **Apache** module to apply changes.

### 7. Verify Installation
1.  Open your web browser.
2.  Navigate to [http://localhost/](http://localhost/)
3.  You should be redirected to the Login page or the Landing page of the Room Utilization System.

## 📂 Project Structure Overview

*   **`app/`**: Backend Logic
    *   `controllers/`: Requests handling & page routing.
    *   `models/`: Database schema interaction & business logic.
    *   `views/`: HTML files for frontend display.
    *   `config/`: Database (`database.php`) and Email configurations.
*   **`public/`**: Assets & Entry Points
    *   `index.php`: Main entry point, handles page viewing.
    *   `api.php`: API endpoint for AJAX/Fetch requests.
    *   `js/` & `css/`: Frontend scripts and styles.
*   **`db_dump/`**: SQL files for database setup.