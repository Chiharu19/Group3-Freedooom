<?php

class FacultyController
{

    public function dashboard()
    {
        // While the proper way is to use .php views, the current existing views might be .html
        // and designed to be accessed directly. However, to support the routing in index.php:

        // Check if html file exists, otherwise fallback or error
        if (file_exists(__DIR__ . '/../views/facultyViews/dashboard.html')) {
            require __DIR__ . '/../views/facultyViews/dashboard.html';
        } else {
            // Fallback or just let it fail naturally/create empty
            echo "Faculty Dashboard not found.";
        }
    }
}
