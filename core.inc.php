<?php

ob_start();
session_start();

$current_file = $_SERVER['SCRIPT_NAME'];
$server_ip = "localhost";
session_destroy();

function loggedin(){
    if(isset($_SESSION['role']) && isset($_SESSION['id']) && !empty($_SESSION['role']) && !empty($_SESSION['id'])){
        return true;
    }
    else{
        return false;
    }
}

?>