<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="student"))
        header("Location:../index.php");
    
    if(isset($_GET['id']))
    {
        $course_id = $_GET['id'];
        $query = $conn->prepare("INSERT INTO `registered` VALUES(?,?)");
        $query->bind_param("ss",$_SESSION['id'],$course_id);
        if($query->execute())
            $_SESSION['register_success'] = 1;
        else
            $_SESSION['register_success'] = 0;
        header("Location:course_registration.php");
    }
?>