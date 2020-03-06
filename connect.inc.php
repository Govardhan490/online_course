<?php

    $user = "root";
    $host = "localhost";
    $password = "";
    $database = "online_course";

    $conn = new mysqli($host,$user,$password,$database);

    if($conn->connect_error){
        echo "Connection Failed".$conn->connect_error;
    }

?>
