<?php
    require 'core.inc.php';
    require 'connect.inc.php';

    if(!isset($_SESSION['otp']))
        header("Location:index.php");
    
    if(isset($_POST['otp']))
    {
        $otp = trim($_POST['otp']);
        if(!empty($otp))
        {
            if($otp != $_SESSION['otp'])
            {
                echo "<script> flag = 1; </script>";
            }
            else 
            {
                if(isset($_SESSION['forgot_password']))
                {
                    unset($_SESSION['otp']);
                    unset($_SESSION['forgot_password']);
                    $_SESSION['authentication'] = 1;
                    header("Location:reset_password.php");
                }
                else if(isset($_SESSION['sign_up']))
                { 
                    unset($_SESSION['otp']);
                    unset($_SESSION['sign_up']);
                    $role = $_SESSION['role_sign_up']; 
                    $unique_id = $_SESSION['id_sign_up'];
                    $first_name = $_SESSION['first_name'];
                    $last_name = $_SESSION['last_name'];
                    $email = $_SESSION['email'];
                    $password_hash = $_SESSION['password'];
                    $phone_num = $_SESSION['phone_num'];
                    if($role == "faculty")
                    {
                        $credentials = $_SESSION['credentials'];
                    }
                    session_destroy();
                    session_start();
                    if($role == "admin")
                    {
                        $query2 = $conn->prepare("INSERT INTO `administrator` VALUES (?,?,?,?,?,?)");
                        $query2->bind_param("ssssss",$unique_id,$first_name,$last_name,$email,$password_hash,$phone_num);   
                    }
                    else if($role == "faculty")
                    {
                        $query2 = $conn->prepare("INSERT INTO `faculty` VALUES (?,?,?,?,?,?,?)");
                        $query2->bind_param("sssssss",$unique_id,$first_name,$last_name,$email,$password_hash,$phone_num,$credentials);
                    }
                    else if($role == "student")
                    {    
                        $query2 = $conn->prepare("INSERT INTO `student` VALUES (?,?,?,?,?,?)");
                        $query2->bind_param("ssssss",$unique_id,$first_name,$last_name,$email,$password_hash,$phone_num);
                    }
                    if($query2->execute())
                    {
                        $_SESSION['sign_up_success'] = 1;
                        header("Location:index.php");
                    }
                    else
                    {
                        $_SESSION['sign_up_fail'] = 1;
                        header("Location:signup.php");
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
    <title>OTP</title>
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
                    <strong>Error!</strong> Invalid OTP.
                </div>
                <form id="input_two" action="<?php echo $current_file; ?>" class="needs-validation" novalidate method="POST">
                    <div>
                        <input type="tel" id="otp" maxlength="6" class="form-control" placeholder="Enter OTP (Sent to Email)" name="otp" required>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <input type="button" value="Cancel" class="btn btn-primary" style="background-color: red;" onclick="time_out()">
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Time Out</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                <p>Time out, You have not entered OTP, If u have not got OTP then please check your Email Id again</p>
                </div>
                <div class="modal-footer">
                <button type="button" onclick="time_out()" class="btn btn-default" data-dismiss="modal"> Close </button>
                </div>
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

    setTimeout(function () {    
        $("#myModal").modal();
    },180000);

    function time_out()
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/logout.php'; 
    }
</script>
</html>