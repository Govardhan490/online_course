<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="faculty"))
        header("Location:../index.php");
    
    echo "<script> var no_course = 0;</script>";

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
                    <a href="create_tests.php" class="list-group-item list-group-item-action" style="color: black;">Create Tests</a>
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

        $query2 = $conn->prepare("SELECT `course`.`course_id`,`course`.`course_name`,`student`.`usn`,`student`.`first_name`,`student`.`last_name`,`student`.`email`,`student`.`phone_no` FROM ((`registered` INNER JOIN `course` ON `course`.`course_id` = `registered`.`course_id` AND `course`.`faculty_id` = ? ) INNER JOIN `student` ON `registered`.`usn`=`student`.`usn`) ORDER BY `course`.`course_id`");
        $query2->bind_param("s",$_SESSION['id']);
        if($query2->execute())
        {
            $query2->store_result();
            if($query2->num_rows > 0)
            {
                $courses = array();
                $query2->bind_result($course_id,$course_name,$usn,$student_first_name,$student_last_name,$student_email,$student_phone_no);
                echo "<script> $(document).ready(function(){\n";
                while($query2->fetch())
                {
                    $course_id = replace_newline($course_id);
                    $usn = replace_newline($usn);
                    $course_name = replace_newline($course_name);
                    $student_first_name = replace_newline($student_first_name);
                    $student_last_name = replace_newline($student_last_name);
                    $student_email = replace_newline($student_email);
                    $student_phone_no = replace_newline($student_phone_no);
                    $submit_value = $course_id."_".$usn."_".$course_name."_".$student_first_name." ".$student_last_name;
                    if(!isset($courses[$course_id]))
                        $courses[$course_id] = 1;
                    if($courses[$course_id] == 1)
                    {
                        echo "$('#accordion_first').append(\"<div id='$course_id' class='course_value'>$course_name ($course_id)</div><div><input class='form-control' id='myInput_"."$course_id' type='text' placeholder='Search Students'><br><div id='$course_id"."_students' class='students'><div id='$usn' class='$course_id"."_names'>$student_first_name $student_last_name ($usn)</div><div><ul class='list-group'><li class='list-group-item'>Student Email : $student_email</li><li class='list-group-item'>Student Phone No : $student_phone_no</li></ul><div align='right'><br><form method='POST' action='fs_interact_2.php'><button type='submit' class='btn btn-success' name='interact' value='$submit_value'>Interact</button></form></div></div></div></div>\");\n";
                        echo "$('#myInput_'+'$course_id').attr('onkeyup', 'search_student(\"$course_id\")')\n";
                        $courses[$course_id] = 2;
                    }
                    else
                    {
                        echo "$('#$course_id"."_students').append(\"<div id='$usn' class='$course_id"."_names'>$student_first_name $student_last_name ($usn)</div><div><ul class='list-group'><li class='list-group-item'>Student Email : $student_email</li><li class='list-group-item'>Student Phone No : $student_phone_no</li></ul><div align='right'><br><form method='POST' action='fs_interactas_interact_2.php'><button type='submit' class='btn btn-success' name='interact' value='$submit_value'>Interact</button></form></div></div>\");\n";
                    }
                }
                echo "\n});</script>";
            }
            else
            {
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

    function search_student(course_id)
    {
        var value = $("#myInput_"+course_id).val().toLowerCase();
        $("."+course_id+"_names").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            $(".students").accordion({active:false});
        });
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

        $( ".students" ).accordion({
            collapsible:true,
            active:false,
            heightStyle:true

        });
    });
</script>
</html>