<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="faculty"))
        header("Location:../index.php");
    
    echo "<script> var no_course = 0;var cancel_success = -1; </script>";

    if(isset($_SESSION['cancel_success']))
    {
        if($_SESSION['cancel_success'] == 1)
            echo "<script> cancel_success = 1; </script>";
        else if($_SESSION['cancel_success'] == 0)
            echo "<script> cancel_success = 0; </script>";
        unset($_SESSION['cancel_success']);
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Applied Courses</title>
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
<body style="background-color: rgb(255, 255, 128);">
    <div class="container-fluid pt-1">
        <div class="card">
            <div class="card-header p-3" style="text-align:center;display:inline;">
                <h1 style="font-family: Amita;"><b><i>Applied Courses</i></b></h1>
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
                    <a href="view_applied_course.php" class="list-group-item list-group-item-action active">View Applied Courses</a>
                    <a href="faculty_announcements.php" class="list-group-item list-group-item-action" style="color: black;">Make Announcement</a>
                    <a href="see_admin_announcements.php" class="list-group-item list-group-item-action" style="color: black;">See Admin Announcement</a>
                    <a href="fa_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Admin</a>
                    <a href="fs_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Students</a>
                    <a href="upload_materials.php" class="list-group-item list-group-item-action" style="color: black;">Upload Materials</a>
                    <a href="create_tests.php" class="list-group-item list-group-item-action" style="color: black;">Create Tests</a>
                    <a href="test_statistics.php" class="list-group-item list-group-item-action" style="color: black;">See Test Statistics</a>
                    <a href="see_registered.php" class="list-group-item list-group-item-action" style="color: black;">See Registered Students for Course</a>
                    <a href="faculty_home.php" class="list-group-item list-group-item-action" style="color: black;">Home</a>
                </div>
                <div class="col-sm-9">
                    <div class="alert alert-info">
                        Note : If you have applied for a course and it is not visible here means that the course has been assigned with a faculty. If the faculty assigned is you then the course will have appeared <a href="view_handling_course.php">here!</a>
                    </div>
                    <div class="alert alert-danger" id="alert" style="display: none;">
                        <strong>Oops!</strong> Some Error happened please refresh the page 
                    </div>
                    <div class="alert alert-info" id="no_course" style="display: none;">
                        You are not handling any courses yet
                    </div>
                    <div class="alert alert-success" id="cancel_success" style="display: none;">
                        Successfully Removes Application from Course
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

        $query1 = $conn->prepare("SELECT `course`.`course_id`,`course`.`admin_id`,`course`.`course_name`,`course`.`course_description`,`administrator`.`first_name`,`administrator`.`last_name`,`administrator`.`email`,`administrator`.`phone_no` FROM ((`faculty_applied_courses` INNER JOIN `course` ON `faculty_applied_courses`.`course_id` = `course`.`course_id` AND `faculty_applied_courses`.`faculty_id` = ?)INNER JOIN `administrator` ON `administrator`.`admin_id` = `course`.`admin_id`)");
        $query1->bind_param("s",$_SESSION['id']);
        if($query1->execute())
        {
            $query1->store_result();
            $query1->bind_result($course_id,$admin_id,$course_name,$course_description,$first_name,$last_name,$email,$phone_no);
            if($query1->num_rows > 0)
            {
                echo "<script> $(document).ready(function(){\n";
                while($query1->fetch())
                {
                    $course_description = replace_newline($course_description);
                    $course_name = replace_newline("$course_name");
                    $name = $first_name." ".$last_name;
                    echo "$('#accordion_first').append(\"<div id='$course_id' class='course_value'>$course_name ($course_id)</div><div><ul class='list-group'><li class='list-group-item'>Course Description : $course_description</li><li class='list-group-item'>Admin Name : $name ($admin_id)</li><li class='list-group-item'>Admin Email : $email</li><li class='list-group-item'>Admin Ph No : $phone_no</li></ul><br><button type='button' onclick=cancel_course('$course_id') class='btn btn-danger' style='float:right;'>Remove</button></div>\");\n";
                }
                echo "\n});</script>";
            }
            else
            {
                echo "<script> no_course = 1; </script>";
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

    if(cancel_success == 1)
    {
        document.getElementById("cancel_success").style.display = "block";
    }
    else if(cancel_success == 0)
    {
        document.getElementById("alert").style.display = "block";
        document.getElementById("alert").innerHTML = "Some Error Occurred please try again";
    }

    function time_out()
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/logout.php'; 
    }

    function cancel_course(id)
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/faculty_src/view_applied_course_2.php?id='+id;
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
</html>