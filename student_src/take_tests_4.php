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
                    $url2 = "../courses/$_SESSION[take_test_course_id]/tests/$_SESSION[take_test_test_id]"."_solutions.xml";
                    if($xml_sol=simplexml_load_file($url2))
                    {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Results</title>
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
                <h1 style="font-family: Amita;"><b><i>Results</i></b></h1>
                <ul class="d-flex justify-content-center row list-group list-group-horizontal" style="text-align: center;margin:auto;">
                    <li class="col-sm-3 list-group-item"><b>Name : </b><?php if(isset($_SESSION['first_name']) && isset($_SESSION['last_name'])){echo $_SESSION['first_name']." ".$_SESSION['last_name'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Email : </b><?php if(isset($_SESSION['email'])){echo $_SESSION['email'];} ?></li>
                    <li class="col-sm-3 list-group-item"><b>Phone Number : </b><?php if(isset($_SESSION['phone_no'])){echo $_SESSION['phone_no'];} ?></li>
                </ul>
            </div>
            <div class="card-body">
            <form>
                    <div class='row' style="padding: 5px;">
                        <label for="course_id" class="col-sm-3">Course Id</label>
                        <label for="test_id" class="col-sm-1">Test Id</label>
                        <label for="no_questions" class="col-sm-3">Questions</label>
                        <label for="total_marks" class="col-sm-2">Total Marks</label>
                        <label for="marks_obtained" class="col-sm-3">Total Marks Obtained</label>
                        <br>
                        <input type="text" name="course_id" id="course_id" style="text-align: center" class="form-control col-sm-3" readonly value="<?php echo $_SESSION['take_test_course_id']; ?>">
                        <input type="text" name="test_id" id="test_id" style="text-align: center" class="form-control col-sm-1" readonly value="<?php echo $_SESSION['take_test_test_id'];  ?>">
                        <input type="number" name="no_questions" id="no_questions" style="text-align: center" class="form-control col-sm-3" readonly value="<?php echo $xml->no_of_questions; ?>">
                        <input type="number" name="total_marks" id="total_marks" style="text-align: center" class="form-control col-sm-2" readonly value="<?php echo $xml->total_marks; ?>">
                        <input type="number" name="marks_obtained" id="marks_obtained" style="text-align: center" class="form-control col-sm-3 is-valid" readonly value="<?php echo $xml->total_marks; ?>">
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
                                if(isset($_POST["q$i"."_answer"]))
                                {
                                    $ans = $_POST["q$i"."_answer"];
                                }
                                else
                                {
                                    $ans = "";
                                }

                                echo "<div class'row' id='Q-"."$i' style='padding: 10px;'>
                                        <label for='q"."$i'>Question</label>
                                        <textarea class='form-control' name='q"."$i' id='q"."$i' rows='2' readonly style='background-color: white;'>".$xml->questions->question[($i-1)]->q."</textarea><br>

                                        <label for='q"."$i"."o1'>Option 1</label>
                                        <input class='form-control' id='q"."$i"."o1' type='text'readonly style='background-color: white;' value='".$xml->questions->question[($i-1)]->option_1."'><br>
                                        
                                        <label for='q"."$i"."o2'>Option 2</label>
                                        <input class='form-control' id='q"."$i"."o2' type='text'readonly style='background-color: white;' value='".$xml->questions->question[($i-1)]->option_2."'><br>
                                        
                                        <label for='q"."$i"."o3'>Option 3</label>
                                        <input class='form-control' id='q"."$i"."o3' type='text'readonly style='background-color: white;' value='".$xml->questions->question[($i-1)]->option_3."'><br>
                                        
                                        <label for='q"."$i"."o4'>Option 4</label>
                                        <input class='form-control' id='q"."$i"."o4' type='text'readonly style='background-color: white;' value='".$xml->questions->question[($i-1)]->option_4."'><br>
                                        
                                        <div class='form-group row'>
                                            <div class='col-sm-6'>
                                                <label for='q"."$i"."ans'>Marks Allotted</label>
                                                <input class='form-control' id='q"."$i"."ans' type='text'readonly value='".$xml->questions->question[($i-1)]->marks."'><br>
                                            </div>";
                            
                                    if($ans == $xml_sol->solution[($i-1)])
                                    {
                                        echo "<div class='col-sm-6'>
                                                <label for='q"."$i"."marks'>Obtained Marks</label>
                                                <input class='form-control' id='q"."$i"."marks' type='text'readonly value='".$xml->questions->question[($i-1)]->marks."'><br>
                                            </div>
                                        </div>";
                                        $result += (int)$xml->questions->question[($i-1)]->marks;
                                        echo "<script> $('#q"."$i"."o"."$ans').addClass('is-valid'); </script>";
                                    }
                                    else
                                    {
                                        echo "<div class='col-sm-6'>
                                                <label for='q"."$i"."marks'>Obtained Marks</label>
                                                <input class='form-control' id='q"."$i"."marks' type='text'readonly value='0'><br>
                                            </div>
                                        </div>";
                                        if($ans != "")
                                        {
                                            $sol = $xml_sol->solution[($i-1)];
                                            echo "<script> $('#q"."$i"."o"."$ans').addClass('is-invalid');$('#q"."$i"."o"."$sol').addClass('is-valid'); </script>";
                                        }
                                        else
                                        {
                                            $sol = $xml_sol->solution[($i-1)];
                                            echo "<script> $('#q"."$i"."o"."$sol').addClass('is-valid'); </script>";
                                        }
                                    }

                                            
                                if($i<$xml->no_of_questions)
                                {
                                    echo "<br><div align='right' class='form-group'> <button type='button' class='btn btn-success text-right next-tab' onclick='nextTab(".$i.")'> Next </button> </div></div>\n";
                                }
                                else
                                {
                                    echo "<br><div align='right' class='form-group'> <button type='button' onclick='time_out()' class='btn btn-success text-right next-tab'> Finish </button> </div></div>\n";
                                }

                            }
                            echo "<script> $('#marks_obtained').val('$result'); </script>";
                        ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

<script>

    function time_out()
    {
        var ip = '<?php echo $server_ip; ?>';
        window.location.href = 'http://'+ip+'/student_src/take_tests.php'; 
    }

    function nextTab(x) {
        $("#tabs").tabs({ active: x });
    }

    $(document).ready(function() {
        $(function() {
            $("#tabs").tabs();
        });
    });
</script>
</html>
<?php           
                        $total_percent = ($result/$xml->total_marks)*100;
                        $query3 = $conn->prepare("UPDATE `tests` SET `result` = ? WHERE `usn` = ? AND `test_id` = ? AND `course_id` = ?");
                        $query3->bind_param("isss",$total_percent,$_SESSION['id'],$_SESSION['take_test_test_id'],$_SESSION['take_test_course_id']);
                        $query3->execute();
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