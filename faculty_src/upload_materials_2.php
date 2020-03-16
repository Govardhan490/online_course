<?php

    require '../core.inc.php';
    require '../connect.inc.php';
    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="faculty"))
        header("Location:../index.php");

    if(isset($_POST['filetype']) && (isset($_POST['url']) || isset($_POST['file'])) && isset($_POST['course_id']))
    {
        $file_type = "link";
        $course_id = $_POST['course_id'];
        if($_POST['filetype'] == 'url' && !empty($_POST['url']) && !empty($_POST['course_id']))
        {
            $file_name = $_POST['url'];
            $query0 = $conn->prepare("INSERT INTO `materials` (`course_id`,`file_name`,`file_type`) VALUES (?,?,?)");
            $query0->bind_param("sss",$course_id,$file_name,$file_type);
            if($query0->execute())
            {
                $_SESSION['upload_success'] = 1;
            }
            else
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
                    $dir = "../courses/".$course_id."/materials";
                    if ( !file_exists( $dir ) && !is_dir( $dir ) ) 
                    {
                        mkdir( $dir,0777,true);       
                    } 
                    $target_file = "../courses/".$course_id."/materials/".$name;
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) 
                    {
                        $interact_flag = 1;
                        $query1 = $conn->prepare("INSERT INTO `materials` (`course_id`,`file_name`,`file_type`) VALUES (?,?,?)");
                        $query1->bind_param("sss",$course_id,$name,$type);
                        if($query1->execute())
                        {
                            $_SESSION['upload_success'] = 1;
                        }
                        else
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
        header("Location:upload_materials.php");
    }

?>