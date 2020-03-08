<?php

    require 'core.inc.php';
    require 'connect.inc.php';
    require 'phpmailer/PHPMailerAutoload.php';

    echo "<script> var sign_up_fail; </script>";

    if(isset($_SESSION['sign_up_fail']))
    {
        echo "<script> sign_up_fail = 1; </script>";
        unset($_SESSION['sign_up_fail']);
    }

    $email_flag = 0;
    $id_flag = 0;
    $success_flag = 0;

    if(isset($_POST['role']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['unique_id']) && isset($_POST['phone_number'])){
        $role = $_POST['role'];
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $unique_id = trim($_POST['unique_id']);
        $phone_num = trim($_POST['phone_number']);
        $cred_flag = 0;
        if($role == "faculty")
        {
            if(isset($_POST['credentials']))
            {
                $cred_flag = 1;
                $credentials = trim($_POST['credentials']);
            }
        }
        if(!empty($role) && !empty($email) && !empty($password) && !empty($confirm_password) && !empty($first_name) && !empty($unique_id) && !empty($phone_num) ){
            if($password != $confirm_password)
                echo "<script> flag = 1; </script>";
            else
            {
                if($role == "admin")
                    $query = $conn->prepare("SELECT `email` FROM `administrator` WHERE `email` = ?"); 
                else if($role == "faculty")
                    $query = $conn->prepare("SELECT `email` FROM `faculty` WHERE `email` = ?");
                else if($role == "student")
                    $query = $conn->prepare("SELECT `email` FROM `student` WHERE `email` = ?");

                $query->bind_param("s",$email);

                if($query->execute())
                {
                    $query->store_result();
                    if($query->num_rows() == 0)
                    {
                        $query->close();
                        if($role == "admin")
                            $query1 = $conn->prepare("SELECT `admin_id` FROM `administrator` WHERE `admin_id` = ?");   
                        else if($role == "faculty")
                            $query1 = $conn->prepare("SELECT `faculty_id` FROM `faculty` WHERE `faculty_id` = ?");
                        else if($role == "student")
                            $query1 = $conn->prepare("SELECT `usn` FROM `student` WHERE `usn` = ?");

                        $query1->bind_param("s",$unique_id);

                        if($query1->execute())
                        {
                            $query1->store_result();
                            if($query1->num_rows() == 0)
                            {
                                $query1->close();
                                $password_hash = md5($password);
                                $rndno = rand(100000, 999999);
                                $mail = new PHPMailer();
                                $mail->isSMTP();
                                $mail->SMTPAuth = true;
                                $mail->SMTPSecure = 'ssl';
                                $mail->Host = 'smtp.gmail.com';
                                $mail->Port = '465';
                                $mail->isHTML();
                                $mail->Username = "onlinecourseportaldbms@gmail.com";
                                $mail->Password = "nahb1212@M";
                                $mail->setFrom("no-reply@onlinecourses.org");
                                $mail->Subject = "No Reply";
                                $mail->Body = "Your OTP for Registering is ".$rndno."\n Valid for 3 minutes";
                                $mail->addAddress($email);
                                echo "OK";
                                if(!$mail->send())
                                {
                                    
                                    echo "<script> flag = 4; </script>";
                                }
                                else
                                {
                                    $_SESSION['sign_up'] = 1;
                                    $_SESSION['otp'] = $rndno;
                                    $_SESSION['role'] = $role;
                                    $_SESSION['id'] = $unique_id;
                                    $_SESSION['first_name'] = $first_name;
                                    $_SESSION['last_name'] = $last_name;
                                    $_SESSION['email'] = $email;
                                    $_SESSION['password'] = $password_hash;
                                    $_SESSION['phone_num'] = $phone_num;
                                    if($role == "faculty")
                                        $_SESSION['credentials'] = $credentials;
                                    
                                    header("Location:otp.php");
                                }
                            }
                            else
                            {
                                echo "<script> flag = 3; </script>"; 
                                $id_flag = 1;
                            }                      
                        }
                        else
                            echo "<script> flag = 4; </script>";
                    }
                    else
                    {
                        echo "<script> flag = 2; </script>";
                        $email_flag = 1;
                    }
                }
                else
                    echo "<script> flag = 4; </script>"; 
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
    <title>Sign Up</title>
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
                    <strong>Error!</strong> Passwords doesnt match
                </div>
                <form action="<?php echo $current_file; ?>" id="form" class="needs-validation" novalidate method="POST">
                    <label for="radios">SignUp As:</label>
                    <div id="radios">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="admin"" name="role" value="admin" onclick="cred_delete()" required>
                            <label class="custom-control-label" for="admin">Admin</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="faculty" name="role" onclick="cred_add()" value="faculty" required>
                            <label class="custom-control-label" for="faculty">Faculty</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" id="student" name="role" value="student" onclick="cred_delete()" required>
                            <label class="custom-control-label" for="student">Student</label>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <input type="email" maxlength="30" class="form-control" value="<?php if($success_flag == 0 && $email_flag == 0 && isset($_POST['email'])){echo $_POST['email'];} ?>" id="email" placeholder="Email" name="email" required>
                    </div>
                    <div class="row">
                        <div class="col">
                            <input type="password" id="password" class="form-control" placeholder="Password" name="password" required>
                        </div>
                        <div class="col">
                            <input type="password" id="confirm_password" class="form-control" placeholder="Confirm Password" name="confirm_password" required>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                        <input type="text" id="first_name" maxlength="20" class="form-control" value="<?php if($success_flag == 0 && isset($_POST['first_name'])){echo $_POST['first_name'];}?>" placeholder="First Name" name="first_name" required>
                        </div>
                        <div class="col">
                        <input type="text" id="last_name" maxlength="20" class="form-control" value="<?php if($success_flag == 0 && isset($_POST['last_name'])){echo $_POST['last_name'];}?>" placeholder="Last Name" name="last_name">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                        <input type="text" id="unique_id" value="<?php if($success_flag == 0 && $id_flag == 0 && isset($_POST['unique_id'])){echo $_POST['unique_id'];} ?>" maxlength="12" class="form-control" placeholder="SSN/USN" name="unique_id" required>
                        </div>
                        <div class="col">
                        <input type="tel" id="ph_no" maxlength="10" class="form-control" pattern="[0-9].{9}" value="<?php if($success_flag == 0 && isset($_POST['phone_number'])){echo $_POST['phone_number'];}?>" placeholder="Phone Number" name="phone_number" required>
                        </div>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary">Sign Up</button>
                </form>
                <br>
                <div>Already a member? <a href="/loginform.php">Login Now!</a></div>
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
    
    var cred_flag = 0;

    function cred_delete()
    {
        if(cred_flag == 1)
        {
            var credentials = document.getElementById("cred_div");
            credentials.remove();
            var br = document.getElementById("myBr");
            br.remove();
            cred_flag = 0;
            
        }
    }

    function cred_add()
    {
        if(cred_flag == 0)
        {
            var credentials_div = document.createElement("div");
            credentials_div.id = "cred_div";
            var credentials = document.createElement("input");
            credentials.id = "cred";
            credentials.name = "credentials";
            credentials.className = "form-control";
            credentials.type = "textarea";
            credentials.maxLength = "250";
            credentials.required = "true";
            credentials.placeholder = "Credentials (Specializations, Experience etc)";
            credentials.value = "<?php if($success_flag == 0 && isset($_POST['credentials'])){ echo $_POST['credentials']; } ?>";
            var br = document.createElement("br");
            br.id = "myBr";
            credentials_div.appendChild(credentials);
            var parent = document.getElementById("form");
            parent.insertBefore(credentials_div,parent.children[10]);
            parent.insertBefore(br,parent.children[11]);
            cred_flag = 1;
            var credentials = document.getElementById
        }

    };

    $(document).ready(function(){
        if(flag != 0)
        {
            var alert = document.getElementById("alert");
            alert.style.display = "block";
            if(flag == 1)
                alert.innerHTML = "<strong>Error!</strong> Passwords doesnt match";
            else if(flag == 2)
                alert.innerHTML = "<strong>Error!</strong> Email already exits, Please try with different Email";
            else if(flag == 3)
                alert.innerHTML = "<strong>Error!</strong> USN/SSN already exits, Please try with different USN/SSN";
            else if(flag == 4)
                alert.innerHTML = "<strong>Error!</strong> Some Error Happened while connecting to website. Please try again later";        
        }

        if(sign_up_fail == 1)
        {
            var alert = document.getElementById("alert");
            alert.style.display = "block";
            alert.innerHTML = "<strong>Error!</strong> Some Error Happened while connecting to website. Please try again later";        

        }
    });
</script>
    

</html>
