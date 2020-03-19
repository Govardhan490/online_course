<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="faculty"))
        header("Location:../index.php");
    
    echo "<script> var no_course = 0;var cancel_success = -1; </script>";

    if(isset($_SESSION['announce_success']))
    {
        if($_SESSION['announce_success'] == 1)
        {
            echo "<script> announce_success = 1; </script>";
        }
        else if($_SESSION['announce_success'] == 0)
            echo "<script> announce_success = 0; </script>";
        
        unset($_SESSION['announce_success']);
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Faculty Announcements</title>
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
                <h1 style="font-family: Amita;"><b><i>Faculty Announcements</i></b></h1>
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
                    <a href="faculty_announcements.php" class="list-group-item list-group-item-action active">Make Announcement</a>
                    <a href="see_admin_announcements.php" class="list-group-item list-group-item-action" style="color: black;">See Admin Announcement</a>
                    <a href="fa_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Admin</a>
                    <a href="fs_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Students</a>
                    <a href="upload_materials.php" class="list-group-item list-group-item-action" style="color: black;">Upload Materials</a>
                    <a href="create_tests.php" class="list-group-item list-group-item-action" style="color: black;">Create and View Tests</a>
                    <a href="test_statistics.php" class="list-group-item list-group-item-action" style="color: black;">See Test Statistics</a>
                    <a href="see_registered.php" class="list-group-item list-group-item-action" style="color: black;">See Registered Students for Course</a>
                    <a href="faculty_home.php" class="list-group-item list-group-item-action" style="color: black;">Home</a>
                </div>
                <div class="col-sm-9">
                    <div class="alert alert-danger" id="alert" style="display: none;">
                        <strong>Oops!</strong> Some Error happened please refresh the page 
                    </div>
                    <div class="alert alert-info" id="no_course" style="display: none;">
                        You are not handling any courses yet
                    </div>
                    <div class="alert alert-success" id="success" style="display: none;">
                        Announced Successfully 
                    </div>
                    <div class="container">
                        <h2>Courses</h2>
                        <input class="form-control" id="myInput" type="text" placeholder="Search.."><br>
                        <div id="accordion_first">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php

        $query = $conn->prepare("SELECT * FROM `course` WHERE `faculty_id` = ?");
        $query->bind_param("s",$_SESSION['id']);
        if($query->execute())
        {
            $courses = array();
            $result = $query->get_result();  
            $rows = $result->num_rows;
            if($rows > 0)
            {    
                echo "<script> $(document).ready(function(){\n";
                for($i = 0;$i<$rows;$i++)
                {
                    $data = $result->fetch_assoc();
                    $admin_id = $data['admin_id'];
                    $course_name = replace_newline($data['course_name']);
                    $course_id = replace_newline($data['course_id']);
                    $courses["$course_id"] = 0;
                    $course_description = replace_newline($data['course_description']);
                    $admin_id = replace_newline($admin_id);
                    echo "$('#accordion_first').append(\"<div id='$course_id' class='course_value'>$course_name ($course_id)</div><div><form action='faculty_announcements_2.php' method='post'><textarea class='form-control' rows='2' name='announcement' maxlength='500' placeholder='New Announcement' required></textarea><br><div align='right'><button type='submit' class='btn btn-primary' name='submit' value='$course_id'>Announce</button></div></form><ul id='$course_id"."_announcements' class='list-group'></ul></div>\");\n";
                }
                echo "\n});</script>";
                $query2 = $conn->prepare("SELECT * FROM `faculty_announcement` WHERE `faculty_id` = ? ORDER BY `time_Stamp` DESC");
                $query2->bind_param("s",$_SESSION['id']);
                if($query2->execute())
                {
                    $result = $query2->get_result();
                    $rows = $result->num_rows;
                    if($rows > 0)
                    {
                        echo "<script> $(document).ready(function(){\n";
                        for($i = 0;$i<$rows;$i++)
                        {
                            $data = $result->fetch_assoc();
                            $course_id = $data['course_id'];
                            $courses["$course_id"] += 1;
                            $announcement = replace_newline($data['announcement']);
                            $time = date("g:i a F j, Y ", strtotime($data['time_stamp']));
                            echo "$('#$data[course_id]_announcements').append(\"<li class='list-group-item'><b>$announcement</b><div class='timestamp'>($time)</div></li>\");";
                        }
                        echo "\n});</script>";
                    }
                    foreach($courses as $x => $x_value) {
                        if($x_value == 0)
                        {
                            echo "<script>$(document).ready(function(){\n$('#".$x."_announcements').append(\"<div class='alert alert-info'>No announcements yet </div>\");\n}); </script>";
                        }
                    }
                }
                else
                {
                    echo "<script> flag = 1; </script>";
                }
            }
            else{
                echo "<script> var no_course = 1; </script>";
            }
        }
        else
        {
            echo "<script> flag = 1; </script>";
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

    $(document).ready(function(){
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".course_value").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                $("#accordion_first").accordion({active:false});
            });
        });

        $( "#accordion_first" ).accordion({
            collapsible:true,
            active:false,
            heightStyle:true

        });

        if(announce_success == 1)
            document.getElementById("success").style.display = "block";
        else if(announce_success == 0)
            document.getElementById("alert").style.display = "block";
    });
</script>
</html>