<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="student"))
        header("Location:../index.php");
    
    echo "<script> var no_course = 0;var upload_success = -1; </script>";

    echo "<script> var invalid_file = 0; </script>";

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Download Materials</title>
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
                <h1 style="font-family: Amita;"><b><i>Download Materials</i></b></h1>
                <button type="button" onclick="time_out()" class="btn btn-danger" style="float: right;">Log Out</button>
                <ul class="d-flex justify-content-center row list-group list-group-horizontal" style="text-align: center;margin:auto;">
                    <li class="col-sm-3 list-group-item"><b>Name : </b><?php if(isset($_SESSION['first_name']) && isset($_SESSION['last_name'])){echo $_SESSION['first_name']." ".$_SESSION['last_name'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Email : </b><?php if(isset($_SESSION['email'])){echo $_SESSION['email'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Phone Number : </b><?php if(isset($_SESSION['phone_no'])){echo $_SESSION['phone_no'];} ?></li>
                </ul>
            </div>
            <div class="card-body row">
                <div class="col-sm-3 list-group">
                    <a href="view_registered_course.php" class="list-group-item list-group-item-action" style="color: black;">View Registered Courses</a>
                    <a href="course_registration.php" class="list-group-item list-group-item-action" style="color: black;">Register for Course</a>
                    <a href="see_admin_announcements.php" class="list-group-item list-group-item-action" style="color: black;">View Admin Announcement</a>
                    <a href="see_faculty_announcements.php" class="list-group-item list-group-item-action" style="color: black;">View Faculty Announcement</a>
                    <a href="sa_interact.php" class="list-group-item list-group-item-action"  style="color: black;">Interact with Admin</a>
                    <a href="sf_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Faclty</a>                        
                    <a href="download_materials.php" class="list-group-item list-group-item-action active">Download Materials</a>
                    <a href="take_tests.php" class="list-group-item list-group-item-action" style="color: black;">Take Tests</a>
                    <a href="test_statistics.php" class="list-group-item list-group-item-action" style="color: black;">Test Statistics</a>
                    <a href="student_home.php" class="list-group-item list-group-item-action" style="color: black;">Home</a>
                </div>
                <div class="col-sm-9">
                    <div class="alert alert-danger" id="alert" style="display: none;">
                        <strong>Oops!</strong> Some Error happened please refresh the page 
                    </div>
                    <div class="alert alert-info" id="no_course" style="display: none;">
                        You have not registered for any courses yet
                    </div>
                    <div class="alert alert-success" id="success" style="display: none;">
                        Uploaded Successfully 
                    </div>
                    <div class="alert alert-danger" id="invalid_file" style="display: none;">
                        Only Image, Pdfs and Doc files are allowed
                    </div>
                    <div class="container">
                        <h3>Courses</h3>
                        <input class="form-control" id="myInput" type="text" placeholder="Search.."><br>
                        <div id="accordion_first">                  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php

        $query = $conn->prepare("SELECT `course_id`,`course_name` FROM `course` WHERE `course_id` IN (SELECT `registered`.`course_id` FROM `registered` WHERE `registered`.`usn`=?)");
        $query->bind_param("s",$_SESSION['id']);
        if($query->execute())
        {
            $courses = array();
            $query->store_result();  
            $query->bind_result($course_id,$course_name);
            $rows = $query->num_rows;
            if($rows > 0)
            {    
                echo "<script> $(document).ready(function(){\n";
                for($i = 0;$i<$rows;$i++)
                {   
                    $query->fetch();
                    $course_name = replace_newline($course_name);
                    $course_id = replace_newline($course_id);
                    $courses["$course_id"] = 0;
                    echo "$('#accordion_first').append(\"<div id='$course_id' class='course_value'>$course_name ($course_id)</div><div><ul id='$course_id"."_materials' class='list-group'></ul></div>\");\n";
                }
                echo "\n});</script>";
                $query2 = $conn->prepare("SELECT * FROM `materials` WHERE `course_id` IN (SELECT `registered`.`course_id` FROM `registered` WHERE `registered`.`usn`=?) ORDER BY `time_Stamp` DESC");
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
                            $file_name = $data['file_name'];
                            $course_id = $data['course_id'];
                            $courses["$course_id"] += 1;
                            $file_type = $data['file_type'];
                            $time = date("g:i a F j, Y ", strtotime($data['time_stamp']));
                            if($file_type == "link")
                                echo "$('#$data[course_id]_materials').append(\"<li class='list-group-item'><b><a href='$file_name'>$file_name</a></b><div class='timestamp'>($time)</div></li>\");";
                            else
                            {
                                $name = substr($file_name,5,strlen($file_name)-1);
                                echo "$('#$data[course_id]_materials').append(\"<li class='list-group-item'><b><a href='../courses/$course_id/materials/$file_name' download>$name</a></b><div class='timestamp'>($time)</div></li>\");";
                            }
                        }
                        echo "\n});</script>";
                    }
                    foreach($courses as $x => $x_value) {
                        if($x_value == 0)
                        {
                            echo "<script>$(document).ready(function(){\n$('#".$x."_announcements').append(\"<div class='alert alert-info'>No Materials yet </div>\");\n}); </script>";
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

    $(document).ready(function()
    {
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