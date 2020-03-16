<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="admin"))
        header("Location:../index.php");
    
    if(isset($_POST['course_id']) && isset($_POST['course_name']) && isset($_POST['course_description']))
    {
        $course_id = $_POST['course_id'];
        $course_name = $_POST['course_name'];
        $course_description = $_POST['course_description'];
        
        $query = $conn->prepare("SELECT `course_id` FROM `course` WHERE LOWER(`course_id`) = ?");
        $course_id_lower = strtolower($course_id);
        $query->bind_param("s",$course_id_lower);
        if($query->execute())
        {
            $query->store_result();
            if($query->num_rows() == 0)
            {
                $query->close();
                $query1 = $conn->prepare("INSERT INTO `course` (`course_id`,`admin_id`,`course_name`,`course_description`) VALUES(?,?,?,?)");
                $query1->bind_param("ssss",$course_id,$_SESSION['id'],$course_name,$course_description);
                if($query1->execute())
                {
                    if ( !file_exists( "../courses" ) && !is_dir( "../courses" ) ) 
                    {
                        mkdir( $dir,0777,true);
                        chmod("../courses",0777);       
                    }
                    mkdir("../courses/$course_id",0777,true);
                    chmod("../courses/$course_id",0777);
                    $_SESSION['course_creation_success'] = 1;
                    header("Location:admin_home.php");
                }
                else
                {
                    echo "<script> flag = 2; </script>";
                }
            }
            else
            {
                echo "<script> flag = 1; </script>";
            }
        }
        else
        {
            echo "<script> flag = 2; </script>";
        }
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Course</title>
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
                <h1 style="font-family: Amita;"><b><i>Create Course</i></b></h1>
                <button type="button" onclick="time_out()" class="btn btn-danger" style="float: right;">Log Out</button>
                <ul class="d-flex justify-content-center row list-group list-group-horizontal" style="text-align: center;margin:auto;">
                    <li class="col-sm-3 list-group-item"><b>Name : </b><?php if(isset($_SESSION['first_name']) && isset($_SESSION['last_name'])){echo $_SESSION['first_name']." ".$_SESSION['last_name'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Email : </b><?php if(isset($_SESSION['email'])){echo $_SESSION['email'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Phone Number : </b><?php if(isset($_SESSION['phone_no'])){echo $_SESSION['phone_no'];} ?></li>
                </ul>
            </div>
            <div class="card-body row">
                <div class="col-sm-3 list-group">
                    <a href="create_course.php" class="list-group-item list-group-item-action active">Create Course</a>
                    <a href="view_created_course.php" class="list-group-item list-group-item-action" style="color: black;">View Created Course</a>
                    <a href="assign_faculty.php" class="list-group-item list-group-item-action" style="color: black;">Assign Faculty for pending courses</a>
                    <a href="admin_announcements.php" class="list-group-item list-group-item-action" style="color: black;">Make Announcements</a>
                    <a href="af_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Faculty</a>
                    <a href="as_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Students</a>
                    <a href="student_registered.php" class="list-group-item list-group-item-action" style="color: black;">List of Students registered for a course</a>
                    <a href="test_statistics.php" class="list-group-item list-group-item-action" style="color: black;">Test Statistics</a>
                    <a href="admin_home.php" class="list-group-item list-group-item-action" style="color: black;">Home</a>
                </div>
                <div class="col-sm-9">
                    <div class="alert alert-danger" id="alert" style="display: none;">
                        <strong>Oops!</strong> Some Error happened please refresh the page; 
                    </div>
                    <form action="<?php echo $current_file; ?>" class="needs-validation" novalidate method="POST">
                        <div class="row">
                            <div class="col">
                                <input type="text" class="form-control" id="course_id" placeholder="Course Id" name="course_id" required>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control" id="course_name" placeholder="Course Name" name="course_name" required>
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <textarea name="course_description" class="form-control" rows="8" maxlength="500" placeholder="Course Description (500 characters)" id="course_decription" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

<script type="text/javascript">

    (function() {
        'use strict';
        window.addEventListener('load', function() {
            // Get the forms we want to add validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                event.preventDefault();
                event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
            });
        }, false);
    })();

</script> 


<script>
    if(flag == 1){
        document.getElementById("alert").style.display = "block";
        document.getElementById("alert").innerHTML = "<strong>Course Id exists!</strong> Please Select a different one";
    }
    else if(flag == 2){
        document.getElementById("alert").style.display = "block";
        document.getElementById("alert").innerHTML = "<strong>Oops!</strong> Some Error happened please refresh the page";
    }

    function time_out()
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/logout.php'; 
    }
</script>
</html>