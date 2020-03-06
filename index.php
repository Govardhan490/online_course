<?php

    require 'core.inc.php';
    require 'connect.inc.php';

    if(!loggedin()){
        header("Location:http://".$server_ip."/loginform.php");
        exit();
    }
    else if($_SESSION['role'] == "admin"){
        header("Location:http://".$server_ip."/admin_home.php");
        exit();
    }
    else if($_SESSION['role'] == "faculty"){
        header("Location:http://".$server_ip."/faculty_home.php");
        exit();
    }
    else if($_SESSION['role'] == "student"){
        header("Location:http://".$server_ip."/student_home.php");
        exit();
    }

?>