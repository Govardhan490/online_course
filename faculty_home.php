<?php

require 'core.inc.php';
require 'connect.inc.php';

if(isset($_SESSION['role']))
{
    echo $_SESSION['role'];
}

echo "Faculty_home_page";

echo "<a href='logout.php'>Logout</a>"

?>