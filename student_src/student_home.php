<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="student"))
        header("Location:../index.php");
    
    echo "<script> var success_flag = 0;</script>";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php if(isset($_SESSION['first_name']) && isset($_SESSION['last_name'])){echo $_SESSION['first_name']." ".$_SESSION['last_name'];} ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <link href='https://fonts.googleapis.com/css?family=Amita' rel='stylesheet'>
</head>
<body style="background-color: rgb(255, 255, 128);">
<div class="container-fluid pt-1">
        <div class="card">
            <div class="card-header p-3" style="text-align:center;display:inline;">
                <h1 style="font-family: Amita;"><b><i>Student Home Page</i></b></h1>
                <button type="button" onclick="time_out()" class="btn btn-danger" style="float: right;">Log Out</button>
                <ul class="d-flex justify-content-center row list-group list-group-horizontal" style="text-align: center;margin:auto;">
                    <li class="col-sm-3 list-group-item"><b>Name : </b><?php if(isset($_SESSION['first_name']) && isset($_SESSION['last_name'])){echo $_SESSION['first_name']." ".$_SESSION['last_name'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Email : </b><?php if(isset($_SESSION['email'])){echo $_SESSION['email'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Phone Number : </b><?php if(isset($_SESSION['phone_no'])){echo $_SESSION['phone_no'];} ?></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="alert alert-success" id="success" style="display: none;">
                    <strong>Oops!</strong> Some Error happened please refresh the page; 
                </div>
                <div class="alert alert-danger" id="alert" style="display: none;">
                    <strong>Oops!</strong> Some Error happened please refresh the page; 
                </div>
                    <br>
                    <div class="list-group">
                        <a href="view_registered_course.php" class="list-group-item list-group-item-action" style="color: black;">View Registered Courses</a>
                        <a href="course_registration.php" class="list-group-item list-group-item-action" style="color: black;">Register for Course</a>
                        <a href="see_admin_announcements.php" class="list-group-item list-group-item-action" style="color: black;">View Admin Announcement</a>
                        <a href="see_faculty_announcements.php" class="list-group-item list-group-item-action" style="color: black;">View Faculty Announcement</a>
                        <a href="sa_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Admin</a>
                        <a href="sf_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Faclty</a>
                        <a href="download_materials.php" class="list-group-item list-group-item-action" style="color: black;">Download Materials</a>
                        <a href="take_tests.php" class="list-group-item list-group-item-action" style="color: black;">Take Tests</a>
                        <a href="test_statistics.php" class="list-group-item list-group-item-action" style="color: black;">Test Statistics</a>
                    </div>
            </div>
        </div>
    </div>
</body>

<script>
if(flag == 1){
        document.getElementById("alert").style.display = "block";
    }

    function time_out()
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/logout.php'; 
    }
</script>
</html>