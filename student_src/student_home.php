<?php
    require 'core.inc.php';
    require 'connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="student"))
        header("Location:index.php");

    if(isset($_SESSION['role']))
    {
        echo $_SESSION['role'];
    }
    echo "Student_home_page";

    echo "<a href='logout.php'>Logout</a>"


?>