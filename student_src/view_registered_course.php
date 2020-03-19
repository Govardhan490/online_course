<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="student"))
        header("Location:../index.php");

    echo "<script> var no_course = 0; </script>";
?>


<!DOCTYPE html>
<html>
<head>
    <title>Registered Courses</title>
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
                <h1 style="font-family: Amita;"><b><i>Registered Courses</i></b></h1>
                <button type="button" onclick="time_out()" class="btn btn-danger" style="float: right;">Log Out</button>
                <ul class="d-flex justify-content-center row list-group list-group-horizontal" style="text-align: center;margin:auto;">
                    <li class="col-sm-3 list-group-item"><b>Name : </b><?php if(isset($_SESSION['first_name']) && isset($_SESSION['last_name'])){echo $_SESSION['first_name']." ".$_SESSION['last_name'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Email : </b><?php if(isset($_SESSION['email'])){echo $_SESSION['email'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Phone Number : </b><?php if(isset($_SESSION['phone_no'])){echo $_SESSION['phone_no'];} ?></li>
                </ul>
            </div>
            <div class="card-body row">
                <div class="col-sm-3 list-group">
                    <a href="view_registered_course.php" class="list-group-item list-group-item-action active">View Registered Courses</a>
                    <a href="course_registration.php" class="list-group-item list-group-item-action" style="color: black;">Register for Course</a>
                    <a href="see_admin_announcements.php" class="list-group-item list-group-item-action" style="color: black;">View Admin Announcement</a>
                    <a href="see_faculty_announcements.php" class="list-group-item list-group-item-action" style="color: black;">View Faculty Announcement</a>
                    <a href="sa_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Admin</a>
                    <a href="sf_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Faclty</a>                        
                    <a href="download_materials.php" class="list-group-item list-group-item-action" style="color: black;">Download Materials</a>
                    <a href="take_tests.php" class="list-group-item list-group-item-action" style="color: black;">Take Tests</a>
                    <a href="test_statistics.php" class="list-group-item list-group-item-action" style="color: black;">Test Statistics</a>
                    <a href="student_home.php" class="list-group-item list-group-item-action" style="color: black;">Home</a>
                </div>
                <div class="col-sm-9">
                    <div class="alert alert-danger" id="alert" style="display: none;">
                        <strong>Oops!</strong> Some Error happened please refresh the page; 
                    </div>
                    <div class="container">
                        <h2>Courses</h2>
                        <div class="alert alert-info" id="no_course" style="display: none;">
                            You have not registered to any course yet. <a href="course_registration.php">Register Now!</a>
                        </div>
                        <input class="form-control" id="myInput" type="text" placeholder="Search.."><br>
                        <div id="accordion_first">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</body>

<?php

    $query = $conn->prepare("SELECT `registered`.`course_id`,`course`.`course_name`,`course`.`course_description`,`course`.`admin_id`,`administrator`.`first_name`,`administrator`.`last_name`,`administrator`.`email`,`administrator`.`phone_no` FROM ((`registered` INNER JOIN `course` ON `registered`.`course_id` = `course`.`course_id` AND `registered`.`usn`=?) INNER JOIN `administrator` ON `course`.`admin_id` = `administrator`.`admin_id`)");
    $query->bind_param("s",$_SESSION['id']);
    $rows = -1;
    if($query->execute())
    {
        $query->store_result();
        $query->bind_result($course_id,$course_name,$course_description,$admin_id,$admin_first_name,$admin_last_name,$admin_email,$admin_phone_no);
        $rows = $query->num_rows;
        if($rows > 0)
        {    
            echo "<script> $(document).ready(function(){\n";
            for($i = 0;$i<$rows;$i++)
            {
                $query->fetch();
                $course_name = replace_newline($course_name);
                $course_id = replace_newline($course_id);
                $course_description = replace_newline($course_description);
                $name = replace_newline($admin_first_name." ".$admin_last_name);
                echo "$('#accordion_first').append(\"<div id='$course_id' class='course_value'>$course_name ($course_id)</div><div><ul class='list-group'><li class='list-group-item'>Description : $course_description</li><li class='list-group-item'>Admin ID : $admin_id</li><li class='list-group-item'>Admin Name : $name</li><li class='list-group-item'>Admin Email : $admin_email</li><li class='list-group-item'>Admin Phone No : $admin_phone_no </li></ul></div>\");\n"; 
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

<script>
    function time_out()
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/logout.php'; 
    }

    if(flag == 1)
        document.getElementById("alert").style.display = "block";
    
    if(no_course == 1)
        document.getElementById("no_course").style.display = "block";

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
