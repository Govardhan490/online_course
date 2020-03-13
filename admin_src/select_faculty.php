<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="admin"))
        header("Location:../index.php");

    if(isset($_GET['id']))
    {
        $arr = explode("_",$_GET['id']);
        $course_id = $arr[0];
        $faculty_id = $arr[1];
        $query = $conn->prepare("UPDATE `course` SET `faculty_id` = ? WHERE `course_id` = ?");
        $query->bind_param("ss",$faculty_id,$course_id);
        if($query->execute())
        {
            $query1 = $conn->prepare("DELETE FROM `faculty_applied_courses` WHERE `course_id` = ?");
            $query1->bind_param("s",$course_id);
            if($query1->execute())
            {
                $_SESSION['faculty_assign'] = 1;
                header("Location:assign_faculty.php");
            }
        }
        else
        {
            $_SESSION['faculty_assign'] = 0;
            header("Location:assign_faculty.php");
            exit();
        }
    }

?>