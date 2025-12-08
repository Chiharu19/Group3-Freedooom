<?php

class StudentController
{

    public function dashboard()
    {
        // While the proper way is to use .php views, the current existing student views are .html
        // and designed to be accessed directly. However, to support the routing in index.php:
        require __DIR__ . '/../views/studentViews/dashboard.html';
    }
}
