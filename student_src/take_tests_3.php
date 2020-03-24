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
                    $result = 0;
                    $query2 = $conn->prepare("INSERT INTO `tests` VALUES (?,?,?,(SELECT `faculty_id` FROM `course` WHERE `course`.`course_id` = ?),?)");
                    $query2->bind_param("ssssi",$_SESSION['take_test_course_id'],$_SESSION['take_test_test_id'],$_SESSION['id'],$_SESSION['take_test_course_id'],$result);
                    if($query2->execute())
                    {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test</title>
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
    <style>
        .custom-control-label{
            font-size: 20px;
        }
    </style>
</head>
<body style="background-color: rgb(255, 255, 128);">
    <div class="container-fluid pt-1">
        <div class="card">
            <div class="card-header p-3" style="text-align:center;display:inline;">
                <h1 style="font-family: Amita;"><b><i>Test</i></b></h1>
                <ul class="d-flex justify-content-center row list-group list-group-horizontal" style="text-align: center;margin:auto;">
                    <li class="col-sm-3 list-group-item"><b>Name : </b><?php if(isset($_SESSION['first_name']) && isset($_SESSION['last_name'])){echo $_SESSION['first_name']." ".$_SESSION['last_name'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Email : </b><?php if(isset($_SESSION['email'])){echo $_SESSION['email'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Phone Number : </b><?php if(isset($_SESSION['phone_no'])){echo $_SESSION['phone_no'];} ?></li>
                </ul>
            </div>
            <div class="card-body">
            <form method="POST" id='form_answers' action="take_tests_4.php">
                    <div class='row' style="padding: 5px;">
                        <label for="course_id" class="col-sm-3">Course Id</label>
                        <label for="test_id" class="col-sm-3">Test Id</label>
                        <label for="no_questions" class="col-sm-3">Questions</label>
                        <label for="total_marks" class="col-sm-3">Total Marks</label>
                        <br>
                        <input type="text" name="course_id" id="course_id" style="text-align: center" class="form-control col-sm-3" readonly value="<?php echo $_SESSION['take_test_course_id'] ?>">
                        <input type="text" name="test_id" id="test_id" style="text-align: center" class="form-control col-sm-3" readonly value="<?php echo $_SESSION['take_test_test_id'] ?>">
                        <input type="number" name="no_questions" id="no_questions" style="text-align: center" class="form-control col-sm-3" readonly value="<?php echo $xml->no_of_questions; ?>">
                        <input type="number" name="total_marks" id="total_marks" style="text-align: center" class="form-control col-sm-3" readonly value="<?php echo $xml->total_marks; ?>">
                    </div>
                    <br>
                    <div id="tabs">
                        <ul>
                            <?php 
                                for($i=1;$i<=$xml->no_of_questions;$i++)
                                {
                                    echo "<li><a href='#Q-$i'>Q-$i (".$xml->questions->question[($i-1)]->marks." marks)</a></li>";
                                }
                            ?>
                        </ul>
                        <?php
                            for($i=1;$i<=$xml->no_of_questions;$i++)
                            {
                                echo "<div class'row' id='Q-"."$i' style='padding: 10px;'>
                                        <label for='q"."$i'>Question</label>
                                        <p style='font-size: 20px;' class='form-control main' name='q"."$i' id='q"."$i' rows='2'>".$xml->questions->question[($i-1)]->q."</p><br>

                                        <div class='custom-control custom-radio main'>
                                        <input type='radio' class='custom-control-input' id='q"."$i"."_option1' name='q"."$i"."_answer' value='1'>
                                            <label class='custom-control-label' for='q"."$i"."_option1'>".$xml->questions->question[($i-1)]->option_1."</label>
                                        </div>

                                        <div class='custom-control custom-radio main'>
                                        <input type='radio' class='custom-control-input' id='q"."$i"."_option2' name='q"."$i"."_answer' value='2'>
                                            <label class='custom-control-label' for='q"."$i"."_option2'>".$xml->questions->question[($i-1)]->option_2."</label>
                                        </div>

                                        <div class='custom-control custom-radio main'>
                                        <input type='radio' class='custom-control-input' id='q"."$i"."_option3' name='q"."$i"."_answer' value='3'>
                                            <label class='custom-control-label' for='q"."$i"."_option3'>".$xml->questions->question[($i-1)]->option_3."</label>
                                        </div>

                                        <div class='custom-control custom-radio main'>
                                        <input type='radio' class='custom-control-input' id='q"."$i"."_option4' name='q"."$i"."_answer' value='4'>
                                            <label class='custom-control-label' for='q"."$i"."_option4'>".$xml->questions->question[($i-1)]->option_4."</label>
                                        </div>";
                                if($i<$xml->no_of_questions)
                                {
                                    echo "<br><div align='right' class='form-group'> <button type='button' class='btn btn-success text-right next-tab' onclick='nextTab(".$i.")'> Next </button> </div></div>\n";
                                }
                                else
                                {
                                    echo "<br><div align='right' class='form-group'> <button type='button' onclick='submit_answers()' class='btn btn-success text-right next-tab'> Finish </button> </div></div>\n";
                                }
                            }
                        ?>
                    </div>
                </form>
            </div>
        </div>
        <div id="dialog-confirm" title="Take Test" style="display: none;">
            <p>General Rules :-<br>1. A test can be taken only once. <br>2. All the Questions will be Multiple Choice Questions and there will be no negative markings.<br> 3. There is no time limit for the test. But once Submitted you cannot retake the test and cannot come back <br> 4. If you go to other pages after test has started in same tab then your results will be considered as zero.  <br> <br> Do you want to continue or go back?</p>
        </div>

        <div id="dialog1-confirm" title="Submit Answers" style="display: none;">
            <p>Do you really want to submit the answers?</p>
        </div>
    </div>
</body>

<script>

    function time_out()
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/student_src/take_tests.php'; 
    }

    function submit_answers()
    {
        $("#dialog1-confirm").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "Submit": function() {
                        $('#form_answers').submit();
                        $(this).dialog("close");
                    },
                    "Cancel": function() {
                        $(this).dialog("close");
                    }
                }
        });
        $(".ui-dialog-titlebar-close").hide();
    }

    function nextTab(x) {
        $("#tabs").tabs({ active: x });
    }

    $(document).ready(function() {
        $('.main').hide();
        $(function() {
            $("#tabs").tabs();
        });

        $("#dialog-confirm").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "Continue": function() {
                        $('.main').show();
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