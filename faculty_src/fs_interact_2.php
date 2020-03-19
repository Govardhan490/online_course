<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="faculty"))
        header("Location:../index.php");
    
    echo "<script> var no_course = 0; </script>";

    if(isset($_POST['interact']))
    {
        $value = explode("~",$_POST['interact']);
        $_SESSION['f_interact_course_id'] = $value[0];
        $_SESSION['f_interact_usn'] = $value[1];
        $_SESSION['f_interact_course_name'] = $value[2];
        $_SESSION['f_interact_student_name'] = $value[3];
    }
    echo "<script> var flag = 0; </script>";
    echo "<script> var invalid_file = 0; </script>";
    if(isset($_SESSION['invalid_file']))
    {
        if($_SESSION['invalid_file'] == 1)
        {
            echo "<script> var invalid_file = 1; </script>";
        }
        else if($_SESSION['invalid_file'] == 2)
        {
            echo "<script> var invalid_file = 2; </script>";
        }
        else if($_SESSION['invalid_file'] == 3)
        {
            echo "<script> var invalid_file = 3; </script>";
        }
        unset($_SESSION['invalid_file']);
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Faculty Student Interaction</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <link href='https://fonts.googleapis.com/css?family=Amita' rel='stylesheet'>
    <link rel="stylesheet" href="../jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="../jquery-ui/jquery-ui.structure.css">
    <link rel="stylesheet" href="../jquery-ui/jquery-ui.theme.css">
    <script src="../jquery-ui/jquery-ui.js"></script>
    <style>
        .timestamp{
            color: red;
            float: right;
            font-size: 15px;
        }
        #accordion_first .ui-accordion-content {
            max-height: 400px;
        }
    </style>
</head>
<body style="background-color: rgb(255, 255, 128);">
    <div class="container-fluid pt-1">
        <div class="card">
            <div class="card-header p-3" style="text-align:center;display:inline;">
                <h1 style="font-family: Amita;"><b><i>Faculty Student Interaction</i></b></h1>
                <button type="button" onclick="time_out()" class="btn btn-danger" style="float: right;">Log Out</button>
                <ul class="d-flex justify-content-center row list-group list-group-horizontal" style="text-align: center;margin:auto;">
                    <li class="col-sm-3 list-group-item"><b>Name : </b><?php if(isset($_SESSION['first_name']) && isset($_SESSION['last_name'])){echo $_SESSION['first_name']." ".$_SESSION['last_name'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Email : </b><?php if(isset($_SESSION['email'])){echo $_SESSION['email'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Phone Number : </b><?php if(isset($_SESSION['phone_no'])){echo $_SESSION['phone_no'];} ?></li>
                </ul>
            </div>
            <div class="card-body row">
                <div class="col-sm-3 list-group">
                    <a href="view_handling_course.php" class="list-group-item list-group-item-action" style="color: black">View Handling Courses</a>
                    <a href="apply_for_course.php" class="list-group-item list-group-item-action" style="color: black;">Apply for Courses</a>
                    <a href="view_applied_course.php" class="list-group-item list-group-item-action" style="color: black">View Applied Courses</a>
                    <a href="faculty_announcements.php" class="list-group-item list-group-item-action" style="color: black;">Make Announcement</a>
                    <a href="see_admin_announcements.php" class="list-group-item list-group-item-action" style="color: black;">See Admin Announcement</a>
                    <a href="fa_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Admin</a>
                    <a href="fs_interact.php" class="list-group-item list-group-item-action active">Interact with Students</a>
                    <a href="upload_materials.php" class="list-group-item list-group-item-action" style="color: black;">Upload Materials</a>
                    <a href="create_tests.php" class="list-group-item list-group-item-action" style="color: black;">Create and View Tests</a>
                    <a href="test_statistics.php" class="list-group-item list-group-item-action" style="color: black;">See Test Statistics</a>
                    <a href="see_registered.php" class="list-group-item list-group-item-action" style="color: black;">See Registered Students for Course</a>
                    <a href="faculty_home.php" class="list-group-item list-group-item-action" style="color: black;">Home</a>
                </div>
                <div class="col-sm-9">
                    <div class="alert alert-danger" id="alert" style="display: none;">
                        <strong>Oops!</strong> Some Error happened please refresh the page; 
                    </div>
                    <ul class="d-flex justify-content-center row list-group list-group-horizontal" style="text-align: center;margin:auto;">
                        <li class="col-sm-4 list-group-item"><b>Student Name : </b><?php if(isset($_SESSION['f_interact_student_name'])){echo $_SESSION['f_interact_student_name'];} ?></li>
                        <li class="col-sm-5 list-group-item"><b>Course Name : </b><?php if(isset($_SESSION['f_interact_course_name'])){echo $_SESSION['f_interact_course_name'];} ?></li>
                    </ul>
                    <div class="card" style="overflow:scroll;height:400px;background-color:rgb(255, 255, 210);" id="chats">
                    </div>
                    <br>
                    <form action="fs_interact_3.php" method="post" onsubmit="return filesize_validate()" enctype="multipart/form-data">
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

        $query3 = $conn->prepare("SELECT * FROM `sf_interact` WHERE `course_id` = ? AND `usn` = ? ORDER BY `time_stamp`");
        $query3->bind_param("ss",$_SESSION['f_interact_course_id'],$_SESSION['f_interact_usn']);
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
                            echo "$('#chats').append(\"<div style='margin:10px;'><span style='padding:10px;background-color:white;margin:2px;font-size:20px;'><a href='../courses/$data[course_id]/fs_interact/$data[message]' download>$msg</a><span style='color:red;font-size:13px;'>($time)</span></span></div>\");\n";
                        }
                    }
                    else if($data['flag'] == 0)
                    {
                        if($data['msg_type'] == 'text')
                        {
                            echo "$('#chats').append(\"<div align='right' style='margin:10px;'><span style='padding:10px;background-color:rgb(252, 206, 255);margin:2px;font-size:20px;'>$message <span style='color:red;font-size:13px;'>($time)</span></span></div>\");\n";                    }
                        else
                        {
                            echo "$('#chats').append(\"<div align='right' style='margin:10px;'><span style='padding:10px;background-color:rgb(252, 206, 255);margin:2px;font-size:20px;'><a href='../courses/$data[course_id]/fs_interact/$data[message]' download>$msg</a><span style='color:red;font-size:13px;'>($time)</span></span></div>\");\n";
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

</body>

<script>
    if(flag == 1)
    {
        document.getElementById("alert").style.display = "block";
    }

    if(no_course == 1)
    {
        document.getElementById("no_course").style.display = "block";
    }

    function time_out()
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/logout.php'; 
    }

    function filesize_validate()
    {
        const fi = document.getElementById('file'); 
        if (fi.files.length > 0) 
        { 
            for (const i = 0; i <= fi.files.length - 1; i++) 
            { 
                const fsize = fi.files.item(i).size; 
                const file = Math.round((fsize / 1024));  
                if (file >= 40000) 
                { 
                    document.getElementById("invalid_file").style.display = "block";
                    document.getElementById("invalid_file").innerHTML = "File size should be less than 40MB";
                    return false;
                } 
                else 
                { 
                    return true;
                } 
            } 
        } 
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
    })

    if(flag == 1)
        document.getElementById("alert").style.display = "block";
    
    if(invalid_file == 1)
    {
        document.getElementById("invalid_file").style.display = "block";
        document.getElementById("invalid_file").innerHTML = "File size should be less than 35MB";
    }
    else if(invalid_file == 2)
    {
        document.getElementById("invalid_file").style.display = "block";
        document.getElementById("invalid_file").innerHTML = "Only Image,Pdfs,Doc,Audio and Video files are allowed";
    }
    else if(invalid_file == 3)
    {
        document.getElementById("invalid_file").style.display = "block";
        document.getElementById("invalid_file").innerHTML = "Sorry Some error occurred try again";
    }
</script>
</html>