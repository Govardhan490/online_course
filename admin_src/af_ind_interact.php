<?php

    require '../core.inc.php';
    require '../connect.inc.php';
    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="admin"))
        header("Location:../index.php");
    

    if(isset($_POST['interact']))
    {
        $value = explode("_",$_POST['interact']);
        $_SESSION['interact_course_id'] = $value[0];
        $_SESSION['interact_faculty_id'] = $value[1];
        $_SESSION['interact_course_name'] = $value[2];
        $_SESSION['interact_faculty_name'] = $value[3];
    }

    if(isset($_POST['filetype']) && (isset($_POST['message']) || isset($_POST['file'])))
    {
        if($_POST['filetype'] == 'message')
        {
            $interact_flag = 0;
            $interact_type = "text";
            $query0 = $conn->prepare("INSERT INTO `fa_interact` (`course_id`,`admin_id`,`faculty_id`,`message`,`msg_type`,`flag`) VALUES (?,?,?,?,?,?)");
            $query0->bind_param("sssssi",$_SESSION['interact_course_id'],$_SESSION['id'],$_SESSION['interact_faculty_id'],$_POST['message'],$interact_type,$interact_flag);
            if(!$query0->execute())
            {
                echo "<script> var invalid_file = 3; </script>";
            }
        }
        else
        {
            $name = $_FILES['file']['name'];
            $type = $_FILES['file']['type'];
            $size = $_FILES['file']['size'];
            if($size < 40000000)
            {
                if(preg_match("/image/", $type) || preg_match("/pdf/",$type) || preg_match("/msword/",$type))
                {
                    $x = rand(11111,99999);
                    $name = $x.$name;
                    $dir = "../courses/".$_SESSION['interact_course_id']."/fa_interact";
                    if ( !file_exists( $dir ) && !is_dir( $dir ) ) 
                    {
                        mkdir( $dir,0777,true);       
                    } 
                    $target_file = "../courses/".$_SESSION['interact_course_id']."/fa_interact/".$name;
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) 
                    {
                        $interact_flag = 0;
                        $query1 = $conn->prepare("INSERT INTO `fa_interact` (`course_id`,`admin_id`,`faculty_id`,`message`,`msg_type`,`flag`) VALUES (?,?,?,?,?,?)");
                        $query1->bind_param("sssssi",$_SESSION['interact_course_id'],$_SESSION['id'],$_SESSION['interact_faculty_id'],$name,$type,$interact_flag);
                        if(!$query1->execute())
                        {
                            echo "<script> var invalid_file = 3; </script>";
                        }
                    } 
                    else 
                    {
                        echo "<script> var invalid_file = 3; </script>";
                    }
                }
                else
                {
                    echo "<script> var invalid_file = 2; </script>";
                }
            }
            else
            {
                echo "<script> var invalid_file = 1; </script>";
            }
        }
    }
    $_POST = array();

?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin Faculty Interaction</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <link href='https://fonts.googleapis.com/css?family=Amita' rel='stylesheet'>
    <link rel="stylesheet" href="../jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="../jquery-ui/jquery-ui.structure.css">
    <link rel="stylesheet" href="../jquery-ui/jquery-ui.theme.css">
    <script src="../jquery-ui/jquery-ui.js"></script>
</head>
<body  style="background-color: rgb(255, 255, 128);">
    <div class="container-fluid pt-1">
        <div class="card">
            <div class="card-header p-3" style="text-align:center;display:inline;">
                <h1 style="font-family: Amita;"><b><i>Faculty Interaction</i></b></h1>
                <button type="button" onclick="time_out()" class="btn btn-danger" style="float: right;">Log Out</button>
                <ul class="d-flex justify-content-center row list-group list-group-horizontal" style="text-align: center;margin:auto;">
                    <li class="col-sm-3 list-group-item"><b>Name : </b><?php if(isset($_SESSION['first_name']) && isset($_SESSION['last_name'])){echo $_SESSION['first_name']." ".$_SESSION['last_name'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Email : </b><?php if(isset($_SESSION['email'])){echo $_SESSION['email'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Phone Number : </b><?php if(isset($_SESSION['phone_no'])){echo $_SESSION['phone_no'];} ?></li>
                </ul>
            </div>
            <div class="card-body row" style="height: 610px;">
                <div class="col-sm-3 list-group">
                    <a href="create_course.php" class="list-group-item list-group-item-action" style="color: black;">Create Course</a>
                    <a href="view_created_course.php" class="list-group-item list-group-item-action" style="color: black;">View Created Course</a>
                    <a href="assign_faculty.php" class="list-group-item list-group-item-action" style="color: black;">Assign Faculty for pending courses</a>
                    <a href="admin_announcements.php" class="list-group-item list-group-item-action" style="color: black;">Make Announcements</a>
                    <a href="af_interact.php" class="list-group-item list-group-item-action active">Interact with Faculty</a>
                    <a href="as_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Students</a>
                    <a href="student_registered.php" class="list-group-item list-group-item-action" style="color: black;">List of Students registered for a course</a>
                    <a href="test_statistics.php" class="list-group-item list-group-item-action" style="color: black;">Test Statistics</a>
                    <a href="admin_home.php" class="list-group-item list-group-item-action" style="color: black;">Home</a>
                </div>
                <div class="col-sm-9">
                    <div class="alert alert-danger" id="alert" style="display: none;">
                        <strong>Oops!</strong> Some Error happened please refresh the page; 
                    </div>
                    <ul class="d-flex justify-content-center row list-group list-group-horizontal" style="text-align: center;margin:auto;">
                        <li class="col-sm-4 list-group-item"><b>Faculty Name : </b><?php if(isset($_SESSION['interact_faculty_name'])){echo $_SESSION['interact_faculty_name'];} ?></li>
                        <li class="col-sm-5 list-group-item"><b>Course Name : </b><?php if(isset($_SESSION['interact_course_name'])){echo $_SESSION['interact_course_name'];} ?></li>
                    </ul>
                    <div class="card" style="overflow:scroll;height: 65%;background-color:rgb(255, 255, 210);" id="chats">
                    </div>
                    <br>
                    <form action="af_ind_interact.php" method="post" enctype="multipart/form-data">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input onclick="showForm(0)" checked type="radio" class="custom-control-input" id="customRadio" name="filetype" value="message" required>
                            <label class="custom-control-label" for="customRadio">Text Message</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input onclick="showForm(1)" type="radio" class="custom-control-input" id="customRadio2" name="filetype" value="file" required>
                            <label class="custom-control-label" for="customRadio2">File Upload</label>
                        </div>
                        <br>
                        <br>
                        <div class="row" style="margin: auto;">
                            <textarea name="message" rows="1" class="form-control col-sm-11" placeholder="New Message" id="message"></textarea>
                            <input title="Images/PDFs/DOCs" class="form-control col-sm-11" type="file" name="file" id="file" style="display: none;">   
                            <button align="right" type="submit" class="btn btn-success col-sm-1">Send</button>                     
                        </div>
                    </form>
                    <div class="alert alert-danger" id="invalid_file" style="display: none;">Only Image, Pdfs and Doc files are allowed</div>
                </div>
            </div>
        </div>
    </div>

<?php

    $query3 = $conn->prepare("SELECT * FROM `fa_interact` WHERE `course_id` = ? ORDER BY `time_stamp`");
    $query3->bind_param("s",$_SESSION['interact_course_id']);
    if($query3->execute())
    {
        $result = $query3->get_result();
        if($result->num_rows > 0)
        {
            echo "<script> $(document).ready(function(){\n";
            while($data = $result->fetch_assoc())
            {
                $time = date("g:i a F j, Y ", strtotime($data['time_stamp']));
                $msg = substr($data['message'],5,strlen($data['message'])-5);
                $message = replace_newline($data['message']);
                if($data['flag'] == 1)
                {
                    if($data['msg_type'] == 'text')
                    {
                        echo "$('#chats').append(\"<div style='margin:10px;'><span style='padding:10px;background-color:white;margin:2px;font-size:20px;'>$message <span style='color:red;font-size:13px;'>($time)</span></span></div>\");\n";
                    }
                    else
                    {
                        echo "$('#chats').append(\"<div style='margin:10px;'><span style='padding:10px;background-color:white;margin:2px;font-size:20px;'><a href='../courses/$data[course_id]/fa_interact/$data[message]' download>$msg</a><span style='color:red;font-size:13px;'>($time)</span></span></div>\");\n";
                    }
                }
                else if($data['flag'] == 0)
                {
                    if($data['msg_type'] == 'text')
                    {
                        echo "$('#chats').append(\"<div align='right' style='margin:10px;'><span style='padding:10px;background-color:rgb(252, 206, 255);margin:2px;font-size:20px;'>$message <span style='color:red;font-size:13px;'>($time)</span></span></div>\");\n";                    }
                    else
                    {
                        echo "$('#chats').append(\"<div align='right' style='margin:10px;'><span style='padding:10px;background-color:rgb(252, 206, 255);margin:2px;font-size:20px;'><a href='../courses/$data[course_id]/fa_interact/$data[message]' download>$msg</a><span style='color:red;font-size:13px;'>($time)</span></span></div>\");\n";
                    }
                }
            }
            echo "\n});</script>";
        }
    }
    else
    {
        echo "<script> var invalid_file = 1; </script>";
    }
    
?>

<script>
    function time_out()
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/logout.php'; 
    }

    function showForm(x)
    {
        if(x == 0)
        {
            document.getElementById("file").style.display = "none";
            document.getElementById("file").removeAttribute("required");
            document.getElementById("message").style.display = "block";
            document.getElementById("message").setAttribute("required","");
        }
        if(x == 1)
        {
            document.getElementById("message").style.display = "none";
            document.getElementById("message").removeAttribute("required");
            document.getElementById("file").style.display = "block";
            document.getElementById("file").setAttribute("required","");
        }
    }

    $(document).ready(function(){
        $("#chats").scrollTop($(document).height());
    }
    )

    if(flag == 1)
        document.getElementById("alert").style.display = "block";
    
    if(invalid_file == 1)
    {
        document.getElementById("invalid_file").style.display = "block";
        document.getElementById("invalid_file").innerHTML = "File size should be less than 20MB";
    }
    else if(invalid_file == 2)
    {
        document.getElementById("invalid_file").style.display = "block";
        document.getElementById("invalid_file").innerHTML = "Only Image, Pdfs and Doc files are allowed";
    }
    else if(invalid_file == 2)
    {
        document.getElementById("invalid_file").style.display = "block";
        document.getElementById("invalid_file").innerHTML = "Sorry Some error occurred try again";
    }

</script>
</body>