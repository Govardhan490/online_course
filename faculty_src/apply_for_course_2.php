<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="faculty"))
        header("Location:../index.php");
    
    if(isset($_GET['id']))
    {
        $course_id = $_GET['id'];
        $query = $conn->prepare("INSERT INTO `faculty_applied_courses` VALUES(?,?)");
        $query->bind_param("ss",$_SESSION['id'],$course_id);
        if($query->execute())
            $_SESSION['apply_success'] = 1;
        else
            $_SESSION['apply_success'] = 0;
        header("Location:apply_for_course.php");
    }
?>