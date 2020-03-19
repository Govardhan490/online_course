<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="faculty"))
        header("Location:../index.php");
    
    echo "<script> no_course = 0; </script>";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test Statistics</title>
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
                <h1 style="font-family: Amita;"><b><i>Test Statistics</i></b></h1>
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
                    <a href="create_tests.php" class="list-group-item list-group-item-action" style="color: black;">Create and View Tests</a>
                    <a href="test_statistics.php" class="list-group-item list-group-item-action active">See Test Statistics</a>
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
        $query1 = $conn->prepare("SELECT `course_id`,`course_name`,`no_of_tests` FROM `course` WHERE `faculty_id` = ?");
        $query1->bind_param("s",$_SESSION['id']);
        if($query1->execute())
        {
            $tests = array();
            $query1->store_result();
            $query1->bind_result($course_id,$course_name,$no_of_tests);
            if($query1->num_rows > 0)
            {
                echo "<script> $(document).ready(function(){\n";
                while($query1->fetch())
                {
                    $course_name = replace_newline($course_name);
                    echo "$('#accordion_first').append(\"<div id='$course_id' class='course_value'>$course_name ($course_id)</div><div id='$course_id"."_tests' class='tests'></div>\");";
                    for($i = 1;$i<=$no_of_tests;$i++)
                    {
                        if($i<10)
                            $testid = "T0$i";
                        else
                            $testid = "T$i";
                        $tests[$course_id."_".$testid] = 0;
                        echo "$('#$course_id"."_tests').append(\"<div>$testid</div><div id='$course_id"."_$testid'><input class='form-control' id='myInput_$course_id"."_$testid' type='text' placeholder='Search Students'><br><table class='table table-bordered'><thead><tr><th>USN</th><th>Name</th><th>Result</th></tr></thead><tbody></tbody></table></div>\");\n";
                        echo "$('#myInput_'+'$course_id'+'_$testid').attr('onkeyup', 'search_student(\"$course_id\"+\"_$testid\")')\n";
                    }
                }
                echo "\n});</script>";
                $query1->close();
                $query2 = $conn->prepare("SELECT `tests`.`usn`,`tests`.`course_id`,`tests`.`test_id`,`student`.`first_name`,`student`.`last_name`,`tests`.`result` FROM ((`tests` INNER JOIN `course` ON `tests`.`course_id`=`course`.`course_id` AND `course`.`faculty_id` = ?) INNER JOIN `student` ON `tests`.`usn` = `student`.`usn`)");
                $query2->bind_param("s",$_SESSION['id']);
                if($query2->execute())
                {
                    $query2->store_result();
                    $query2->bind_result($usn,$course_id,$testid,$first_name,$last_name,$result);
                    echo "<script> $(document).ready(function(){\n";
                    while($query2->fetch())
                    {
                        $name = $first_name." ".$last_name;
                        $tests[$course_id."_".$testid] = 1;
                        echo "$('#$course_id'+'_$testid table tbody').append(\"<tr><td>$usn</td><td>$name</td><td>$result</td></tr>\");";
                        
                    }
                    echo "\n});</script>";
                    $query2->close();
                    foreach($tests as $x => $x_value) {
                        if($x_value == 0)
                        {
                            echo "<script>$(document).ready(function(){\n$('#$x table').remove();\n$(\" <br><div class='alert alert-info'>No Students taken test yet </div>\").insertAfter('#myInput_".$x."');\n}); </script>";
                        }
                    }
                }
                else
                {
                    echo "<script> flag = 1; </script>";
                }
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

    function time_out()
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/logout.php'; 
    }

    function search_student(id)
    {
        var value = $("#myInput_"+id).val().toLowerCase();
        $("#"+id+" table tbody tr").filter(function() {
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

        $( ".tests" ).accordion({
            collapsible:true,
            active:false,
            heightStyle:true

        });
    });
</script>
</html>