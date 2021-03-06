<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="admin"))
        header("Location:../index.php");

    echo "<script> var no_course = 0; </script>";
?>


<!DOCTYPE html>
<html>
<head>
    <title>Registered Students</title>
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
    <style>
        #accordion_first .ui-accordion-content {
            max-height: 400px;
        }
    </style>
</head>
<body  style="background-color: rgb(255, 255, 128);">
    <div class="container-fluid pt-1">
        <div class="card">
            <div class="card-header p-3" style="text-align:center;display:inline;">
                <h1 style="font-family: Amita;"><b><i>Registered Students</i></b></h1>
                <button type="button" onclick="time_out()" class="btn btn-danger" style="float: right;">Log Out</button>
                <ul class="d-flex justify-content-center row list-group list-group-horizontal" style="text-align: center;margin:auto;">
                    <li class="col-sm-3 list-group-item"><b>Name : </b><?php if(isset($_SESSION['first_name']) && isset($_SESSION['last_name'])){echo $_SESSION['first_name']." ".$_SESSION['last_name'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Email : </b><?php if(isset($_SESSION['email'])){echo $_SESSION['email'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Phone Number : </b><?php if(isset($_SESSION['phone_no'])){echo $_SESSION['phone_no'];} ?></li>
                </ul>
            </div>
            <div class="card-body row">
                <div class="col-sm-3 list-group">
                    <a href="create_course.php" class="list-group-item list-group-item-action" style="color: black;">Create Course</a>
                    <a href="view_created_course.php" class="list-group-item list-group-item-action" style="color: black;">View Created Course</a>
                    <a href="assign_faculty.php" class="list-group-item list-group-item-action" style="color: black;">Assign Faculty for pending courses</a>
                    <a href="admin_announcements.php" class="list-group-item list-group-item-action" style="color: black;">Make Announcements</a>
                    <a href="af_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Faculty</a>
                    <a href="as_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Students</a>
                    <a href="student_registered.php" class="list-group-item list-group-item-action active">List of Students registered for a course</a>
                    <a href="test_statistics.php" class="list-group-item list-group-item-action" style="color: black;">Test Statistics</a>
                    <a href="admin_home.php" class="list-group-item list-group-item-action" style="color: black;">Home</a>
                </div>
                <div class="col-sm-9">
                    <div class="alert alert-danger" id="alert" style="display: none;">
                        <strong>Oops!</strong> Some Error happened please refresh the page; 
                    </div>
                    <div class="container">
                        <h2>Courses</h2>
                        <div class="alert alert-info" id="no_course" style="display: none;">
                            <strong>Oops!</strong> You have not created any course yet. <a href="create_course.php">Create Now!</a>
                        </div>
                        <input class="form-control" id="myInput" type="text" placeholder="Search.."><br>
                        <div id="accordion_first">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
    $query1 = $conn->prepare("SELECT `course_id`,`course_name` FROM `course` WHERE `admin_id` = ?");
    $query1->bind_param("s",$_SESSION['id']);
    $courses = array();
    if($query1->execute())
    {
        $query1->store_result();
        if($query1->num_rows>0)
        {
            $query1->bind_result($course_id,$course_name);
            echo "<script> $(document).ready(function(){\n";
            while($query1->fetch())
            {
                $courses[$course_id] = 0;
                $course_name = replace_newline($course_name);
                echo "$('#accordion_first').append(\"<div id='$course_id' class='course_value'>$course_name ($course_id)</div><div><input class='form-control' id='myInput_$course_id' type='text' placeholder='Search Students'><br><div><table id='$course_id"."_details' class='table table-bordered'><thead><tr><th>USN</th><th>Name</th><th>Email</th><th>Phone No</th></tr></thead><tbody></tbody></table></div></div>\");\n";
                echo "$('#myInput_'+'$course_id').attr('onkeyup', 'search_student(\"$course_id\")')\n";
            }
            echo "\n});</script>";
            $query1->close();
            $query2 = $conn->prepare("SELECT `student`.`usn`,`course`.`course_id`,`student`.`first_name`,`student`.`last_name`,`student`.`email`,`student`.`phone_no` FROM ((`course` INNER JOIN `registered` ON `registered`.`course_id`=`course`.`course_id` AND `course`.`admin_id` = ?)INNER JOIN `student` ON `registered`.`usn`=`student`.`usn`) ORDER BY `course`.`course_id`");
            $query2->bind_param("s",$_SESSION['id']);
            if($query2->execute())
            {
                $query2->store_result();
                if($query2->num_rows>0)
                {
                    $query2->bind_result($usn,$course_id,$first_name,$last_name,$email,$phone);
                    echo "<script> $(document).ready(function(){\n";
                    while($query2->fetch())
                    {
                        $name = $first_name." ".$last_name;
                        $courses[$course_id] = 1;
                        echo "$('#$course_id"."_details tbody').append(\"<tr><td>$usn</td><td>$name</td><td>$email</td><td>$phone</td></tr>\");\n";
                    }
                    echo "\n});</script>";
                    foreach($courses as $x => $x_value) {
                        if($x_value == 0)
                        {
                            echo "<script>$(document).ready(function(){\n$('#".$x."_details').remove();\n$(\" <br><div class='alert alert-info'>No Students Registered yet </div>\").insertAfter('#myInput_".$x."');\n}); </script>";
                        }
                    }
                }
            }
            else
            {
                echo "<script>flag = 1;</script";
            }
        }
        else
        {
            echo "<script>no_course = 1;</script>";
        }
    }
    else
    {
        echo "<script>flag = 1;</script";
    }
?>
<script>
    function time_out()
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/logout.php'; 
    }

    if(flag == 1)
    {
        $("#alert").css("display","block");
    }

    if(no_course == 1)
    {
        $("#no_course").css("display","block");
    }

    function search_student(course_id)
    {
        var value = $("#myInput_"+course_id).val().toLowerCase();
        $("#"+course_id+"_details tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
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
    });
</script>
</body>
</html>