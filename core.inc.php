<?php

ob_start();
session_start();


$current_file = $_SERVER['SCRIPT_NAME'];
$server_ip = "localhost";
if(!loggedin() && (isset($_SESSION['otp']) || isset($_SESSION[''])) && $current_file!="/otp.php" && $current_file!='/logout.php'){
    header("Location:/otp.php");
    exit();
}
else if(!loggedin() && isset($_SESSION['authentication']) && $current_file!="/reset_password.php"){
    header("Location:http://".$server_ip."/reset_password.php");
    exit();
}
echo "<script> var flag = 0 </script>";

if(isset($_SESSION['interact_faculty_id']) && ($current_file!="/admin_src/af_ind_interact.php" &&  $current_file!="/admin_src/af_interact_3.php"))
{
    unset($_SESSION['interact_course_id']);
    unset($_SESSION['interact_faculty_id']);
    unset($_SESSION['interact_course_name']);
    unset($_SESSION['interact_faculty_name']); 
}

if(isset($_SESSION['interact_usn']) && ($current_file!="/admin_src/as_interact_2.php" &&  $current_file!="/admin_src/as_interact_3.php"))
{
    unset($_SESSION['interact_course_id']);
    unset($_SESSION['interact_usn']);
    unset($_SESSION['interact_course_name']);
    unset($_SESSION['interact_student_name']); 
}

if(isset($_SESSION['f_interact_admin_id']) && ($current_file!="/faculty_src/fa_interact_2.php" &&  $current_file!="/faculty_src/fa_interact_3.php"))
{
    unset($_SESSION['f_interact_course_id']);
    unset($_SESSION['f_interact_admin_id']);
    unset($_SESSION['f_interact_course_name']);
    unset($_SESSION['f_interact_admin_name']); 
}

if(isset($_SESSION['f_interact_usn']) && ($current_file!="/faculty_src/fs_interact_2.php" &&  $current_file!="/faculty_src/fs_interact_3.php"))
{
    unset($_SESSION['f_interact_course_id']);
    unset($_SESSION['f_interact_usn']);
    unset($_SESSION['f_interact_course_name']);
    unset($_SESSION['f_interact_student_name']); 
}

function loggedin(){
    if(isset($_SESSION['role']) && isset($_SESSION['id']) && !empty($_SESSION['role']) && !empty($_SESSION['id'])){
        return true;
    }
    else{
        return false;
    }
}

function replace_newline($var)
{   
    $var = str_replace ( array("\r\n", "\r", "\n"), "<br>", $var);
    if(substr($var,strlen($var)-4,strlen($var)-1) == '<br>')
        $var = substr($var,0,strlen($var)-4);
    return $var;
}
?>