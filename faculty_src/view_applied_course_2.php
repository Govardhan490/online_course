<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="faculty"))
        header("Location:../index.php");
    
    if(isset($_GET['id']))
    {
        $course_id = $_GET['id'];
        $query = $conn->prepare("DELETE FROM `faculty_applied_courses` WHERE `faculty_id` = ? AND `course_id` = ?");
        $query->bind_param("ss",$_SESSION['id'],$course_id);
        if($query->execute())
            $_SESSION['cancel_success'] = 1;
        else
            $_SESSION['cancel_success'] = 0;
        header("Location:view_applied_course.php");
    }
?>