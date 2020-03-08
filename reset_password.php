<?php

    require 'core.inc.php';
    require 'connect.inc.php';

    if(isset($_SESSION['authentication']) && isset($_SESSION['forgot_user_name']) && isset($_SESSION['forgot_role']))
    {
        if(isset($_POST['password']) && isset($_POST['confirm_password']))
        {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            if(!empty($password) && !empty($confirm_password))
            {
                if($password != $confirm_password)
                {
                    echo "<script> flag = 1; </script>";
                }
                else
                {
                    $password_hash = md5($password);
                    $role = $_SESSION['forgot_role'];
                    if($role == "admin")
                        $query = $conn->prepare("UPDATE `administrator` SET `password` = ? WHERE `email`=?");
                    else if($role == "faculty")
                        $query = $conn->prepare("UPDATE `faculty` SET `password` = ? WHERE `email`=?");
                    else if($role == "student")
                        $query = $conn->prepare("UPDATE `student` SET `password` = ? WHERE `email`=?");
                    $query->bind_param("ss",$password_hash,$_SESSION['forgot_user_name']);
                    if($query->execute())
                    {
                        unset($_SESSION['authentication']);
                        unset($_SESSION['forgot_user_name']);
                        unset($_SESSION['forgot_role']);
                        $_SESSION['password_reset'] = 1;
                        header("Location:index.php");
                    }
                    else
                    {
                        echo "<script> flag = 2; </script>";
                    }                    
                }
            }
        }
    }
    else
    {
        header("Location:index.php");
    }
?>


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
                    <strong>Error!</strong> Passwords doesnt Match.
                </div>
                <form id="input_three" action="<?php echo $current_file; ?>" class="needs-validation" novalidate method="POST">
                    <div>
                        <input type="password" id="password" class="form-control" placeholder="Enter Password" name="password" required>
                    </div>
                    <br>
                    <div>
                        <input type="password" id="confirm_password" class="form-control" placeholder="Re Enter Password" name="confirm_password" required>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
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