<?php

    require 'core.inc.php';
    require 'connect.inc.php';

    echo "<script>
    var flag = 0;
    </script>";

    if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['role']))
    {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $password_hash = md5($password);
        $role = $_POST['role'];
        if(!empty($email) && !empty($password) && !empty($role)){
            if($role == "admin"){
                $query = $conn->prepare("SELECT `admin_id` FROM `administrator` WHERE `email`= ? AND `password` = ? ");
            }
            else if($role == "faculty"){
                $query = $conn->prepare("SELECT `faculty_id` FROM `faculty` WHERE `email`= ? AND `password` = ? ");
            }
            else if($role == "student"){
                $query = $conn->prepare("SELECT `usn` FROM `student` WHERE `email`= ? AND `password` = ? ");
            }
            $query->bind_param("ss",$email,$password_hash);
            if($query->execute()){
                $query->store_result();
                if($query->num_rows() != 1){
                    echo "<script>
                        flag = 1;
                    </script>";
                }
                else{
                    $query->bind_result($id);
                    $query->fetch();
                    echo "<script>
                        flag = 0;
                    </script>";
                    if(!isset($_SESSION['role'] ))
                    {
                        $_SESSION['role'] = $role;
                    }
                    if(!isset($_SESSION['id'] ))
                    {
                        $_SESSION['id'] = $id;
                    }
                    echo $_SESSION['role'];
                    echo $_SESSION['id'];
                    var_dump($_SESSION);
                    if(loggedin())
                    {
                        header("Location:".$role."_home.php");
                        exit();
                    }
                }
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <link href='https://fonts.googleapis.com/css?family=Amita' rel='stylesheet'>
</head>

<body style="background-color: rgb(255, 255, 128);">

    <div class="container p-5">
        <div class="card">
            <div class="card-header p-3" style="font-family: Amita;text-align:center;"><h1 class="display-4"><b><i>Online Course Portal</i></b></h1></div>
            <div class="card-body">
                <div class="alert alert-danger" id="alert" style="display: none;">
                    <strong>Error!</strong> Invalid Email or Password.
                </div>
                <form action="<?php echo $current_file; ?>" class="needs-validation" novalidate method="POST">
                <label for="radios">Login As:</label>
                    <div id="radios">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="admin" name="role" value="admin" required>
                            <label class="custom-control-label" for="admin">Admin</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="faculty" name="role" value="faculty" required>
                            <label class="custom-control-label" for="faculty">Faculty</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="student" name="role" value="student" required>
                            <label class="custom-control-label" for="student">Student</label>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <input type="email" class="form-control" id="email" placeholder="Email" name="email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="pwd" placeholder="Password" name="password" required>
                        <a href="forgot_password.php" style="float:right;">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn btn-primary">Log In</button>
                </form>
                <br>
                <div>Not a member? <a href="/signup.php">Register Now</a></div>
            </div>
        </div>
    </div>

    
</body>


<script>

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

    if(flag == 1){
        document.getElementById("alert").style.display = "block";
    }
</script>
</html>