<?php

    require 'core.inc.php';
    require 'connect.inc.php';

    if(!loggedin()){
        header("Location:loginform.php");
        exit();
    }
    else if($_SESSION['role'] == "admin"){
        header("Location:/admin_src/admin_home.php");
        exit();
    }
    else if($_SESSION['role'] == "faculty"){
        header("Location:/faculty_src/faculty_home.php");
        exit();
    }
    else if($_SESSION['role'] == "student"){
        header("Location:/student_src/student_home.php");
        exit();
    }
?>