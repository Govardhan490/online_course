<?php

    require 'core.inc.php';
    require 'connect.inc.php';

    if(loggedin())
        header("Location:index.php");

    echo "<script> var pass_reset; </script>";
    echo "<script> var sign_up_success; </script>";

    if(isset($_SESSION['password_reset']))
    {
        echo "<script> pass_reset = 1; </script>";
        unset($_SESSION['password_reset']);
    }
    if(isset($_SESSION['sign_up_success']))
    {
        echo "<script> sign_up_success = 1;</script>";
        unset($_SESSION['sign_up_success']);
    }

    if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['role']))
    {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $password_hash = md5($password);
        $role = $_POST['role'];
        if(!empty($email) && !empty($password) && !empty($role)){
            if($role == "admin"){
                $query = $conn->prepare("SELECT `admin_id`,`first_name`,`last_name`,`email`,`phone_no` FROM `administrator` WHERE LOWER(`email`)= ? AND `password` = ? ");
            }
            else if($role == "faculty"){
                $query = $conn->prepare("SELECT `faculty_id`,`first_name`,`last_name`,`email`,`phone_no`,`credentials` FROM `faculty` WHERE LOWER(`email`)= ? AND `password` = ? ");
            }
            else if($role == "student"){
                $query = $conn->prepare("SELECT `usn`,`first_name`,`last_name`,`email`,`phone_no` FROM `student` WHERE LOWER(`email`) = ? AND `password` = ? ");
            }
            $email_lower = strtolower($email);
            $query->bind_param("ss",$email_lower,$password_hash);
            if($query->execute()){
                $query->store_result();
                if($query->num_rows() != 1){
                    echo "<script>
                        flag = 1;
                    </script>";
                }
                else{
                    if($role == "faculty")
                        $query->bind_result($id,$first_name,$last_name,$email,$phone_num,$credentials);
                    else
                        $query->bind_result($id,$first_name,$last_name,$email,$phone_num);
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
                    if(!isset($_SESSION['first_name'] ))
                    {
                        $_SESSION['first_name'] = $first_name;
                    }
                    if(!isset($_SESSION['last_name'] ))
                    {
                        $_SESSION['last_name'] = $last_name;
                    }
                    if(!isset($_SESSION['email'] ))
                    {
                        $_SESSION['email'] = $email;
                    }
                    if(!isset($_SESSION['phone_no'] ))
                    {
                        $_SESSION['phone_no'] = $phone_num;
                    }
                    if( $role == "faculty" && !isset($_SESSION['credentials'] ))
                    {
                        $_SESSION['credentials'] = $credentials;
                    }
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
                <div class="alert alert-success" id="success" style="display: none;">
                    Sign Up Success!
                </div>
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

    if(pass_reset == 1)
    {
        var reset = document.getElementById("success");
        reset.innerHTML = "Password Reset Successfully!!";
        reset.style.display = "block";
    }

    if(sign_up_success == 1)
    {
        var reset = document.getElementById("success");
        reset.innerHTML = "Registered Successfully!!";
        reset.style.display = "block";
    }
</script>
</html>