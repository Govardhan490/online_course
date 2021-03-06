<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="faculty"))
        header("Location:../index.php");
    
    echo "<script> var no_course = 0;var create_success = -1;var xml_fail = 0;  </script>";

    if(isset($_SESSION['create_success']))
    {
        if($_SESSION['create_success'] == 1)
        {
            echo "<script> var create_success = 1;  </script>";
        }
        else if($_SESSION['create_success'] == 0)
        {
            echo "<script> var create_success = 0;  </script>";
        }
        unset($_SESSION['create_success']);
    }

    if(isset($_SESSION['xml_fail']))
    {
        echo "<script> var xml_fail = 1;  </script>";
        unset($_SESSION['xml_fail']);
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create and View Tests</title>
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
                <h1 style="font-family: Amita;"><b><i>Create and View Tests</i></b></h1>
                <button type="button" onclick="time_out()" class="btn btn-danger" style="float: right;">Log Out</button>
                <ul class="d-flex justify-content-center row list-group list-group-horizontal" style="text-align: center;margin:auto;">
                    <li class="col-sm-3 list-group-item"><b>Name : </b><?php if(isset($_SESSION['first_name']) && isset($_SESSION['last_name'])){echo $_SESSION['first_name']." ".$_SESSION['last_name'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Email : </b><?php if(isset($_SESSION['email'])){echo $_SESSION['email'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Phone Number : </b><?php if(isset($_SESSION['phone_no'])){echo $_SESSION['phone_no'];} ?></li>
                </ul>
            </div>
            <div class="card-body row">
                <div class="col-sm-3 list-group">
                    <a href="view_handling_course.php" class="list-group-item list-group-item-action" style="color: black;">View Handling Courses</a>
                    <a href="apply_for_course.php" class="list-group-item list-group-item-action" style="color: black;">Apply for Courses</a>
                    <a href="view_applied_course.php" class="list-group-item list-group-item-action" style="color: black;">View Applied Courses</a>
                    <a href="faculty_announcements.php" class="list-group-item list-group-item-action" style="color: black;">Make Announcement</a>
                    <a href="see_admin_announcements.php" class="list-group-item list-group-item-action" style="color: black;">See Admin Announcement</a>
                    <a href="fa_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Admin</a>
                    <a href="fs_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Students</a>
                    <a href="upload_materials.php" class="list-group-item list-group-item-action" style="color: black;">Upload Materials</a>
                    <a href="create_tests.php" class="list-group-item list-group-item-action active">Create and View Tests</a>
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
                    <div class="alert alert-success" id="create_success" style="display: none;">
                        Test Creation Successful
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

        $query1 = $conn->prepare("SELECT `course`.`course_id`,`course`.`no_of_tests`,`course`.`course_name` FROM `course` WHERE`faculty_id` = ?");
        $query1->bind_param("s",$_SESSION['id']);
        if($query1->execute())
        {
            $query1->store_result();
            $query1->bind_result($course_id,$no_of_tests,$course_name);
            if($query1->num_rows > 0)
            {
                echo "<script> $(document).ready(function(){\n";
                while($query1->fetch())
                {
                    $course_name = replace_newline("$course_name");
                    echo "$('#accordion_first').append(\"<div id='$course_id' class='course_value'>$course_name ($course_id)</div><div id='$course_id"."_test_details'><ul class='list-group'></ul></div>\");\n";
                    for($i=1;$i<=$no_of_tests+1;$i++)
                    {
                        if($i<10)
                            $test_id = "T0$i";
                        else
                            $test_id = "T$i";
                        if($i!=$no_of_tests+1)
                            echo "$('#$course_id"."_test_details ul').append(\"<a href='view_tests.php?test_id=$course_id"."~$test_id'><li class='list-group-item'>$test_id</li></a>\");\n";
                        else
                            echo "$('#$course_id"."_test_details').append(\"<form action='create_tests_2.php' method='get'><br><div align='right' class='form-group'> <button class='btn btn-success text-right' type='submit' name='test_id' value='$course_id"."~$test_id"."~$course_name'> Create Test </button></div></form>\");\n";
                    }
                    if($no_of_tests == 0)
                    {
                        echo "$('#$course_id"."_test_details ul').append(\" <br><div class='alert alert-info'>No tests Created Yet </div>\");\n";
                    }
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

    if(create_success == 1)
    {
        document.getElementById("create_success").style.display = "block";
    }
    else if(create_success == 0)
    {
        document.getElementById("alert").style.display = "block";
        document.getElementById("alert").innerHTML = "Test Creation failed due to some error please try again later";
    }

    if(xml_fail == 1)
    {
        document.getElementById("alert").style.display = "block";
        document.getElementById("alert").innerHTML = "Cannot View Test Please try again later";
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
    });
</script>
</html>