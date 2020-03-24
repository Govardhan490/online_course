<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="faculty"))
        header("Location:../index.php");
    
    if(isset($_GET['test_id']))
    {
        $id = explode("~",$_GET['test_id']);
        $course_id = $id[0];
        $test_id = $id[1];
        $query = $conn->prepare("SELECT `faculty_id` FROM `course` WHERE `course_id`=?");
        $query->bind_param("s",$course_id);
        if($query->execute())
        {
            $query->store_result();
            $query->bind_result($sample);
            $query->fetch();
            if($query->num_rows == 1 && $_SESSION['id'] == $sample )
            {
                $url = "../courses/$course_id/tests/$test_id".".xml";
                if($xml=simplexml_load_file($url))
                {
                    $url1 = "../courses/$course_id/tests/$test_id"."_solutions.xml";
                    if($xml_sol=simplexml_load_file($url1))
                    {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>View Tests</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.min.js"></script>
    <link href='https://fonts.googleapis.com/css?family=Amita' rel='stylesheet'>
    <link rel="stylesheet" href="../jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="../jquery-ui/jquery-ui.structure.css">
    <link rel="stylesheet" href="../jquery-ui/jquery-ui.theme.css">
    <script src="../jquery-ui/jquery-ui.js"></script>
    <style>
        label{
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body style="background-color: rgb(255, 255, 128);">
    <div class="container-fluid pt-1">
        <div class="card">
            <div class="card-header p-3" style="text-align:center;display:inline;">
                <h1 style="font-family: Amita;"><b><i>View Tests</i></b></h1>
                <button type="button" onclick="time_out()" class="btn btn-danger" style="float: right;">Back</button>
                <ul class="d-flex justify-content-center row list-group list-group-horizontal" style="text-align: center;margin:auto;">
                    <li class="col-sm-3 list-group-item"><b>Name : </b><?php if(isset($_SESSION['first_name']) && isset($_SESSION['last_name'])){echo $_SESSION['first_name']." ".$_SESSION['last_name'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Email : </b><?php if(isset($_SESSION['email'])){echo $_SESSION['email'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Phone Number : </b><?php if(isset($_SESSION['phone_no'])){echo $_SESSION['phone_no'];} ?></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="alert alert-danger" id="alert" style="display: none;">
                    Total marks and assigned marks not matching please check once again.
                </div>
                <div class="alert alert-info" id="no_course" style="display: none;">
                    You are not handling any courses yet
                </div>
                <form>
                    <div class='row' style="padding: 5px;">
                        <label for="course_id" class="col-sm-1">Course Id</label>
                        <label for="test_id" class="col-sm-1">Test Id</label>
                        <label for="no_questions" class="col-sm-1">Questions</label>
                        <label for="total_marks" class="col-sm-1">Marks</label>
                        <label for="video_required" class="col-sm-2">Video</label>
                        <label for="link" class="col-sm-6">Video Link</label>
                        <br>
                        <input type="text" name="course_id" id="course_id" style="text-align: center" class="form-control col-sm-1" readonly value="<?php echo $course_id; ?>">
                        <input type="text" name="test_id" id="test_id" style="text-align: center" class="form-control col-sm-1" readonly value="<?php echo $test_id; ?>">
                        <input type="number" name="no_questions" id="no_questions" style="text-align: center" class="form-control col-sm-1" readonly value="<?php echo $xml->no_of_questions; ?>">
                        <input type="number" name="total_marks" id="total_marks" style="text-align: center" class="form-control col-sm-1" readonly value="<?php echo $xml->total_marks; ?>">
                        <input type="text" name="video_required" id="video_required" style="text-align: center" class="form-control col-sm-2" readonly value="<?php if($xml->video['required'] == "1"){echo "Required";}else{echo "Not Required";} ?>">
                        <input type="url" name="link" id="link" style="text-align: center" class="form-control col-sm-6" readonly value="<?php if($xml->video['required']){echo $xml->video->link;}else{echo "-";}?>">
                    </div>
                    <br>
                    <div id="tabs">
                        <ul>
                            <?php 
                                for($i=1;$i<=$xml->no_of_questions;$i++)
                                {
                                    echo "<li><a href='#Q-$i'>Q-$i</a></li>";
                                }
                            ?>
                        </ul>
                        <?php
                            for($i=1;$i<=$xml->no_of_questions;$i++)
                            {
                                echo "<div class'row' id='Q-"."$i' style='padding: 10px;'>
                                        <label for='q"."$i'>Question</label>
                                        <textarea class='form-control' name='q"."$i' id='q"."$i' rows='2' readonly>".$xml->questions->question[($i-1)]->q."</textarea><br>

                                        <label for='q"."$i"."o1'>Option 1</label>
                                        <input class='form-control' id='q"."$i"."o1' type='text'readonly value='".$xml->questions->question[($i-1)]->option_1."'><br>
                                        
                                        <label for='q"."$i"."o2'>Option 2</label>
                                        <input class='form-control' id='q"."$i"."o2' type='text'readonly value='".$xml->questions->question[($i-1)]->option_2."'><br>
                                        
                                        <label for='q"."$i"."o3'>Option 3</label>
                                        <input class='form-control' id='q"."$i"."o3' type='text'readonly value='".$xml->questions->question[($i-1)]->option_3."'><br>
                                        
                                        <label for='q"."$i"."o4'>Option 4</label>
                                        <input class='form-control' id='q"."$i"."o4' type='text'readonly value='".$xml->questions->question[($i-1)]->option_4."'><br>
                                        
                                        <div class='form-group row'>
                                            <div class='col-sm-6'>
                                                <label for='q"."$i"."ans'>Answer</label>
                                                <input class='form-control' id='q"."$i"."ans' type='text'readonly value='".$xml_sol->solution[($i-1)]."'><br>
                                            </div>
                                        
                                            <div class='col-sm-6'>
                                                <label for='q"."$i"."marks'>Marks</label>
                                                <input class='form-control' id='q"."$i"."marks' type='text'readonly value='".$xml->questions->question[($i-1)]->marks."'><br>
                                            </div>
                                        </div>";
                                if($i<$xml->no_of_questions)
                                {
                                    echo "<br><div align='right' class='form-group'> <button type='button' class='btn btn-success text-right next-tab' onclick='nextTab(".$i.")'> Next </button> </div></div>\n";
                                }
                                else
                                {
                                    echo "<br><div align='right' class='form-group'> <button type='button' onclick='time_out()' class='btn btn-success text-right next-tab'> Finish </button> </div></div>\n";
                                }
                            }
                        ?>
                    </div>
                </form>
            </div>
            <div id="dialog-confirm" title="Create Test" style="display: none;">
                <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Once Submitted you cannot make any changes. Are you sure?</p>
            </div>
        </div>
    </div>
</body>
<?php
                    }
                    else
                    {
                        $_SESSION['xml_fail'] = 1;
                        header("Location : create_tests.php");
                    }
                }
                else
                {
                    $_SESSION['xml_fail'] = 1;
                    header("Location : create_tests.php");
                }
            }
            else
            {
                header("Location:create_tests.php");
            }
        }
        else
        {
            header("Location:create_tests.php");
        }
    }
    else
    {
        header("Location:create_tests.php");
    }
?>

<script>

function time_out() {
    var ip = '<?php echo $server_ip; ?>';
    window.location.href = 'http://' + ip + '/faculty_src/create_tests.php';
}

function nextTab(x) {
    $("#tabs").tabs({ active: x });
}

$(document).ready(function() {

var total_marks = 0;

    if (flag == 1) {
        document.getElementById("alert").style.display = "block";
    }

    if (no_course == 1) {
        document.getElementById("no_course").style.display = "block";
    }

    $(function() {
        $("#tabs").tabs();
    });
});

</script>