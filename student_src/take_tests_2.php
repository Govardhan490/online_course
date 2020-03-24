<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="student"))
        header("Location:../index.php");
    
    
    if(isset($_SESSION['take_test_course_id']) && isset($_SESSION['take_test_test_id']))
    {
        $query = $conn->prepare("SELECT COUNT(`usn`) FROM `registered` WHERE `registered`.`course_id` = ? AND `registered`.`usn` = ? ");
        $query->bind_param("ss",$_SESSION['take_test_course_id'],$_SESSION['id']);
        if($query->execute())
        {
            $query->store_result();
            $query->bind_result($count);
            $query->fetch();
            if($count == "1")
            {
                $url = "../courses/$_SESSION[take_test_course_id]/tests/$_SESSION[take_test_test_id]".".xml";
                if($xml=simplexml_load_file($url))
                {
                    if($xml->video["required"] == "1")
                    {
                        $yt_link = $xml->video->link;
                        $yt_link = str_replace("watch?v=", "embed/",$yt_link);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Video</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="//www.youtube.com/iframe_api" async></script>
    <link href='https://fonts.googleapis.com/css?family=Amita' rel='stylesheet'>
    <link rel="stylesheet" href="../jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="../jquery-ui/jquery-ui.structure.css">
    <link rel="stylesheet" href="../jquery-ui/jquery-ui.theme.css">
    <script src="../jquery-ui/jquery-ui.js"></script>
</head>
<body style="background-color: rgb(255, 255, 128);">
    <div class="container-fluid pt-1">
        <div class="card">
            <div class="card-header p-3" style="text-align:center;display:inline;">
                <h1 style="font-family: Amita;"><b><i>Video</i></b></h1>
                <button type="button" onclick="time_out()" class="btn btn-danger" style="float: right;">Cancel</button>
                <ul class="d-flex justify-content-center row list-group list-group-horizontal" style="text-align: center;margin:auto;">
                    <li class="col-sm-3 list-group-item"><b>Name : </b><?php if(isset($_SESSION['first_name']) && isset($_SESSION['last_name'])){echo $_SESSION['first_name']." ".$_SESSION['last_name'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Email : </b><?php if(isset($_SESSION['email'])){echo $_SESSION['email'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Phone Number : </b><?php if(isset($_SESSION['phone_no'])){echo $_SESSION['phone_no'];} ?></li>
                </ul>
            </div>
            <div class="card-body" style="height: 610px;">
                <iframe width="80%" id="ytplayer" height="90%" style="margin:auto;display:block;" src='<?php echo $yt_link; ?>?showinfo=0&enablejsapi=1&origin=http://<?php echo $server_ip; ?>' frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
        <div id="dialog-confirm" title="Take Test" style="display: none;">
            <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Your Faculty recommends you to watch this video before taking the test. Would you like to continue or go back?<br> Once the video is over you will be automatically redirected to the page where you take test.</p>
        </div>
    </div>
</body>

<script>

    var ik_player;
    window.onYouTubeIframeAPIReady = function() {
        ik_player = new YT.Player('ytplayer',{
            events:{
                "onStateChange" : onYouTubePlayerStateChange
            }
        });
    }

    function onYouTubePlayerStateChange(event) 
    {
        switch (event.data) {
            case YT.PlayerState.ENDED:
                var ip = '<?php echo $server_ip; ?>';
                window.location.href = 'http://'+ip+'/student_src/take_tests_3.php';
                break;
        }
    }

    function time_out()
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/student_src/take_tests.php'; 
    }

    $(document).ready(function(){
        $("#dialog-confirm").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "Continue": function() {
                        $(this).dialog("close");
                    },
                    "Go Back": function() {
                        var ip = '<?php echo $server_ip; ?>';
                        window.location.href = 'http://'+ip+'/student_src/take_tests.php';
                        $(this).dialog("close");
                    }
                }
        });
        $(".ui-dialog-titlebar-close").hide();
        $(".ui-icon-alert").hide();
    });
</script>
</html>
<?php
                    }
                    else
                    {
                        header("Location: take_tests.php");
                    }
                }
                else
                {
                    header("Location: take_tests.php");
                }
            }
        }
        else
        {
            header("Location: take_tests.php"); 
        }
    }
    else
    {
        header("Location: take_tests.php");
    }
?>