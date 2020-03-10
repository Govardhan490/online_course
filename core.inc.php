<?php

ob_start();
session_start();

$current_file = $_SERVER['SCRIPT_NAME'];
$server_ip = "localhost";
if(!loggedin() && (isset($_SESSION['otp']) || isset($_SESSION[''])) && $current_file!="/otp.php" && $current_file!='/logout.php'){
    header("Location:http://".$server_ip."/otp.php");
    exit();
}
else if(!loggedin() && isset($_SESSION['authentication']) && $current_file!="/reset_password.php"){
    header("Location:http://".$server_ip."/reset_password.php");
    exit();
}
echo "<script> var flag = 0 </script>";

function loggedin(){
    if(isset($_SESSION['role']) && isset($_SESSION['id']) && !empty($_SESSION['role']) && !empty($_SESSION['id'])){
        return true;
    }
    else{
        return false;
    }
}

?>