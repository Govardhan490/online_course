<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="admin"))
        header("Location:../index.php");

    echo "<script> var no_course = 0; var success_flag=-1; </script>";

    if(isset($_SESSION['faculty_assign']))
    {
        if($_SESSION['faculty_assign'] == 1)
            echo "<script> success_flag=1; </script>";
        else if($_SESSION['faculty_assign'] == 0)
            echo "<script> success_flag=0; </script>";
        unset($_SESSION['faculty_assign']);
    }
?>


<!DOCTYPE html>
<html>
<head>
    <title>Assign Faculty</title>
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
            <h1 style="font-family: Amita;"><b><i>Assign Faculty</i></b></h1>
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
                    <a href="assign_faculty.php" class="list-group-item list-group-item-action active">Assign Faculty for pending courses</a>
                    <a href="admin_announcements.php" class="list-group-item list-group-item-action" style="color: black;">Make Announcements</a>
                    <a href="af_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Faculty</a>
                    <a href="as_interact.php" class="list-group-item list-group-item-action" style="color: black;">Interact with Students</a>
                    <a href="student_registered.php" class="list-group-item list-group-item-action" style="color: black;">List of Students registered for a course</a>
                    <a href="test_statistics.php" class="list-group-item list-group-item-action" style="color: black;">Test Statistics</a>
                    <a href="admin_home.php" class="list-group-item list-group-item-action" style="color: black;">Home</a>
                </div>
                <div class="col-sm-9">
                    <div class="alert alert-danger" id="alert" style="display: none;">
                        <strong>Oops!</strong> Some Error happened please refresh the page 
                    </div>
                    <div class="alert alert-success" id="success" style="display: none;">
                        Faculty Assigned Successfully 
                    </div>
                    <div class="container">
                        <h2>Courses</h2>
                        <div class="alert alert-info" id="no_course" style="display: none;">
                            You dont have any pending courses.
                        </div>
                        <input class="form-control" id="myInput" type="text" placeholder="Search.."><br>
                        <div id="accordion">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</body>

<?php

    $query = $conn->prepare("SELECT * FROM `course` WHERE `admin_id` = ? AND ISNULL(`faculty_id`)");
    $query->bind_param("s",$_SESSION['id']);
    if($query->execute())
    {
        $result = $query->get_result();  
        $rows = $result->num_rows;
        $courses = array();
        if($rows > 0)
        {    
            echo "<script> $(document).ready(function(){\n";
            for($i = 0;$i<$rows;$i++)
            {
                $data = $result->fetch_assoc();
                if($data['faculty_id'] == "")
                    $faculty_id = "<span style='color:red;'>Not assigned yet<span>";
                else
                    $faculty_id = $data['faculty_id'];
                $course_name = replace_newline($data['course_name']);
                $course_id = replace_newline($data['course_id']);
                $courses[$course_id] = 0;
                echo "$('#accordion').append(\"<div class='course_value'>$data[course_name] ($data[course_id])</div><div class='course_description' id='$data[course_id]_applied'></div>\");\n";
            }
            echo "\n});</script>";
            $query1 = $conn->prepare("SELECT `faculty`.`faculty_id`,`faculty_applied_courses`.`course_id`,`faculty`.`first_name`,`faculty`.`last_name`,`faculty`.`credentials`,`course`.`course_name`,`faculty`.`phone_no`,`faculty`.`email` from (( `faculty_applied_courses` INNER JOIN `course` ON `faculty_applied_courses`.`course_id` = `course`.`course_id` AND `course`.`admin_id` = ?) INNER JOIN `faculty`ON `faculty_applied_courses`.`faculty_id` = `faculty`.`faculty_id`) ORDER BY `faculty_applied_courses`.`course_id`");
            $query1->bind_param("s",$_SESSION['id']);
            if($query1->execute())
            {
                $query1->store_result();
                if($query1->num_rows > 0)
                {
                    $query1->bind_result($faculty_id,$course_id,$first_name,$last_name,$credentials,$course_name,$phone_no,$email);
                    echo "<script> $(document).ready(function(){\n";
                    while($query1->fetch())
                    {
                        $faculty_id = replace_newline($faculty_id);
                        $course_id = replace_newline($course_id);
                        $courses[$course_id] += 1;
                        $first_name = replace_newline($first_name);
                        $last_name = replace_newline($last_name);
                        $credentials = replace_newline($credentials);
                        $course_name = replace_newline($course_name);
                        $phone_no = replace_newline($phone_no);
                        $email = replace_newline($email);
                        echo "$('#".$course_id."_applied').append(\"<div>".$first_name." ".$last_name." (".$faculty_id.")</div><div><ul class='list-group'><li class='list-group-item'><b>Credentials</b>  : ". "$credentials"." </li><li class='list-group-item'><b>Phone No</b> : ".$phone_no."</li><li class='list-group-item'><b>Email</b> :".$email."</li></ul><br><button type='button' onclick=selectFaculty('".$course_id."~".$faculty_id."') class='btn btn-success' style='float:right;'>Select</button></div>\");\n ";
                    }
                    echo "\n});</script>";
                }
                foreach($courses as $x => $x_value) {
                    if($x_value == 0)
                    {
                        echo "<script>$(document).ready(function(){\n$('#".$x."_applied').append(\"<div class='alert alert-info'>No faculty has applied yet </div>\");\n}); </script>";
                    }
                    else
                    {
                        echo "<script>$(document).ready(function(){\n $( '#".$x."_applied' ).accordion({collapsible:true,active:false,heightStyle:true}); \n}); </script>";
                    }
                }
            }
            else{
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

<script>
    function time_out()
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/logout.php'; 
    }

    if(flag == 1)
    {
        document.getElementById("alert").style.display = "block";
        document.getElementById("alert").innerHTML = "<strong>Oops!</strong> Some Error happened please refresh the page";
    }
    
    if(no_course == 1)
        document.getElementById("no_course").style.display = "block";

    if(success_flag == 1)
    {
        document.getElementById("success").style.display = "block";
    }
    else if(success_flag == 0)
    {
        document.getElementById("alert").style.display = "block";
        document.getElementById("alert").innerHTML = "<strong>Oops!</strong> Some Error happened please try again";
    
    }

    function selectFaculty(id)
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/admin_src/select_faculty.php?id='+id;
    }

    $(document).ready(function(){
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".course_value").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                $("#accordion_first").accordion({active:false});
            });
        });
        $( "#accordion" ).accordion({
            collapsible:true,
            active:false,
            heightStyle:true

        });

    });
    

</script>
</html>
