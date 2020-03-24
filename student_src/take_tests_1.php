<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="student"))
        header("Location:../index.php");
    
    if(isset($_GET["test_id"]) && !empty($_GET["test_id"]))
    {
        $value = explode("~",$_GET["test_id"]);
        $course_id = $value[0];
        $test_id = $value[1];
        $query = $conn->prepare("SELECT COUNT(`usn`) FROM `registered` WHERE `registered`.`course_id` = ? AND `registered`.`usn` = ? ");
        $query->bind_param("ss",$course_id,$_SESSION['id']);
        if($query->execute())
        {
            $query->store_result();
            $query->bind_result($count);
            $query->fetch();
            if($count == "1")
            {
                $_SESSION["take_test_course_id"] = $course_id;
                $_SESSION["take_test_test_id"] = $test_id;
                $url = "../courses/$course_id/tests/$test_id".".xml";
                if($xml=simplexml_load_file($url))
                {
                    if($xml->video["required"] == "1")
                    {
                        echo $xml->video->link;
                        header("Location: take_tests_2.php");
                    }
                    else
                    {
                        header("Location: take_tests_3.php");
                    }
                }
                else
                {
                    header("Location: take_tests.php");
                }
            }
        }
        else
        {
            header("Location: take_tests.php");
        }
    }
?>