<?php

    require '../core.inc.php';
    require '../connect.inc.php';
    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="student"))
        header("Location:../index.php");

    if(isset($_POST['filetype']) && (isset($_POST['message']) || isset($_POST['file'])))
    {
        if($_POST['filetype'] == 'message')
        {
            $interact_flag = 1;
            $interact_type = "text";
            $query0 = $conn->prepare("INSERT INTO `sa_interact` (`course_id`,`admin_id`,`usn`,`message`,`msg_type`,`flag`) VALUES (?,?,?,?,?,?)");
            $query0->bind_param("sssssi",$_SESSION['s_interact_course_id'],$_SESSION['s_interact_admin_id'],$_SESSION['id'],$_POST['message'],$interact_type,$interact_flag);
            if(!$query0->execute())
            {
                $_SESSION['invalid_file'] = 3;
            }
        }
        else
        {
            $name = $_FILES['file']['name'];
            $type = $_FILES['file']['type'];
            $size = $_FILES['file']['size'];
            if($size < 40000000)
            {
                if(preg_match("/image/", $type) || preg_match("/pdf/",$type) || preg_match("/msword/",$type) || preg_match("/audio/",$type) || preg_match("/video/",$type))
                {
                    $x = rand(11111,99999);
                    $name = $x.$name;
                    $dir = "../courses/".$_SESSION['s_interact_course_id']."/sa_interact";
                    if ( !file_exists( $dir ) && !is_dir( $dir ) ) 
                    {
                        mkdir( $dir,0777,true);       
                    } 
                    $target_file = "../courses/".$_SESSION['s_interact_course_id']."/sa_interact/".$name;
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) 
                    {
                        $interact_flag = 1;
                        $query1 = $conn->prepare("INSERT INTO `sa_interact` (`course_id`,`admin_id`,`usn`,`message`,`msg_type`,`flag`) VALUES (?,?,?,?,?,?)");
                        $query1->bind_param("sssssi",$_SESSION['s_interact_course_id'],$_SESSION['s_interact_admin_id'],$_SESSION['id'],$name,$type,$interact_flag);
                        if(!$query1->execute())
                        {
                            $_SESSION['invalid_file'] = 3;
                        }
                    } 
                    else 
                    {
                        $_SESSION['invalid_file'] = 3;
                    }
                }
                else
                {
                    $_SESSION['invalid_file'] = 2;
                }
            }
            else
            {
                $_SESSION['invalid_file'] = 1;
            }
        }
        header("Location:sa_interact_2.php");
    }

?>