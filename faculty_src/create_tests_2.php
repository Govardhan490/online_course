<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="faculty"))
        header("Location:../index.php");
    
    if(isset($_GET['test_id']) && !empty($_GET['test_id']))
    {
        $value = explode("~",$_GET['test_id']);
        $course_id = $value[0];
        $test_id = $value[1];
        $course_name = $value[2];
        $query = $conn->prepare("SELECT `course_id` FROM `course` WHERE `faculty_id` = ? AND `course_id` = ?");
        $query->bind_param("ss",$_SESSION['id'],$course_id);
        if($query->execute())
        {
            $query->store_result();
            if($query->num_rows == 1)
            {
                $_SESSION['create_test_course_id'] = $course_id;
                $_SESSION['create_test_test_id'] = $test_id;
                $_SESSION['create_test_course_name'] = $course_name;
            }
            else
            {
                header("Location:create_tests.php");
            }
        }
        else
        {
            header("Location:create_tests.php");
        }        
    }
    else
    {
        header("Location:create_tests.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Tests</title>
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
                <h1 style="font-family: Amita;"><b><i>Create Tests</i></b></h1>
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
                    <div class="container">
                        <ul class="list-group">
                            <li class="list-group-item">Course : <?php echo "$_SESSION[create_test_course_name] ($_SESSION[create_test_course_id])"; ?></li>
                            <li class="list-group-item">Test No : <?php echo "$_SESSION[create_test_test_id]"; ?></li>
                        </ul>
                        <br>
                        <h4>Test Description</h4>
                        <form action="create_tests_3.php" onsubmit="return validateYouTubeUrl()" method="post" style="padding: 20px; ">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input onclick="showForm(1)" checked type="radio" class="custom-control-input" id="customRadio" name="video" value="yes" required>
                                <label class="custom-control-label" for="customRadio">Video Required</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input onclick="showForm(0)" type="radio" class="custom-control-input" id="customRadio2" name="video" value="no" required>
                                <label class="custom-control-label" for="customRadio2">Video not Required</label>
                            </div>
                            <br><br>
                            <div class="row">
                                <input class="form-control" type="url" name="link" id="link" placeholder="Youtube Video Link" required>
                                <br><br>
                                <input type="number" name="no_questions" id="no_questions" max="15" placeholder="No of Questions" class="form-control col-sm-6" min='5' required>
                                <input type="number" name="total_marks" id="total_marks" max="20" placeholder="Total Marks" class="form-control col-sm-6" min='10' required>
                            </div>
                            <br>
                            <div align='right' class="form-group"> 
                                <button class="btn btn-success text-right" type="submit"> Create </button> 
                            </div> 
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    function showForm(x)
    {
        if(x == 1)
        {
            document.getElementById("link").style.display = "block";
            document.getElementById("link").setAttribute("required","");
        }
        if(x == 0)
        {
            document.getElementById("link").style.display = "none";
            document.getElementById("link").removeAttribute("required");
        }
    }

    function validateYouTubeUrl()
    {
        if($('input[name=video]:checked').val() == 'yes')
        {
            var url = $('#link').val();
            if (url != undefined || url != '') {
                var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
                var match = url.match(regExp);
                if (match && match[2].length == 11) {
                    return true;
                    //$('#ytplayerSide').attr('src', 'https://www.youtube.com/embed/' + match[2] + '?autoplay=0');
                }
                else {
                    $("#alert").css("display","block");
                    $("#alert").text("Please enter a valid Youtube URL");
                    return false;
                }
            }
        }
        else
            return true;
    }
    $(document).ready(function(){
        
    });
</script>
</html>