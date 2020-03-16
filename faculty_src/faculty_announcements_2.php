<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="faculty"))
        header("Location:../index.php");

    if(isset($_POST['announcement']) && isset($_POST['submit']))
    {
        $data = $_POST['announcement'];
        $course_id = $_POST['submit'];

        $query = $conn->prepare("INSERT INTO `faculty_announcement` (`course_id`,`faculty_id`,`announcement`) VALUES(?,?,?)");
        $query->bind_param("sss",$course_id,$_SESSION['id'],$data);
        if($query->execute())
        {
            $_SESSION['announce_success'] = 1;
        }
        else
        {
            $_SESSION['announce_success'] = 0;
        }
        header("Location:faculty_announcements.php");
    }
?>