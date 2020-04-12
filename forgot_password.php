<?php

    require 'core.inc.php';
    require 'connect.inc.php';
    require 'phpmailer/PHPMailerAutoload.php';

    if(loggedin())
        header("Location:index.php");

    if(isset($_POST['email']) && isset($_POST['role']))
    {
        $role = trim($_POST['role']);
        $email = trim($_POST['email']);
        if(!empty($role) && !empty($email))
        {
            if($role == "admin")
                $query = $conn->prepare("SELECT `email` from `administrator` WHERE LOWER(`email`) = ?");
            else if($role == "faculty")
                $query = $conn->prepare("SELECT `email` from `faculty` WHERE LOWER(`email`) = ?");
            else if($role == "student")
                $query = $conn->prepare("SELECT `email` from `student` WHERE LOWER(`email`) = ?");
            $email_lower = strtolower($email);
            $query->bind_param("s",$email_lower);
            if($query->execute())
            {
                $query->store_result();
                if($query->num_rows() == 1)
                {
                    $rndno = rand(100000, 999999);
                    /* Email Info*/
                    $mail->addAddress($email);
                    if($mail->send())
                    {
                        $_SESSION['otp'] = $rndno;
                        $_SESSION['forgot_password'] = 1;
                        $_SESSION['forgot_user_name'] = $email;
                        $_SESSION['forgot_role'] = $role;
                        header("Location:otp.php");
                    }
                    else
                    {
                        echo "<script> flag = 2 </script>";
                    }
                }
                else
                {
                    echo "<script> flag = 1 </script>";
                }
            }
            else
            {
                echo "<script> flag = 2 </script>";
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
    <title>Forgot Password</title>
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
                    <strong>Error!</strong> Invalid Email.
                </div>
                <form id="input_one" action="<?php echo $current_file; ?>" class="needs-validation" novalidate method="POST">
                    <label for="radios">Role:</label>
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
                    <button type="submit" class="btn btn-primary">Submit</button>
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

    if(flag != 0){
        document.getElementById("alert").style.display = "block";
        if(flag == 1)
            document.getElementById("alert").innerHTML = "<strong>Error!</strong> Invalid Email.";
        else if(flag == 2)
            document.getElementById("alert").innerHTML = "<strong>Error!</strong> Some Error Happened while connecting to website. Please try again later";
    }
</script>
</html>