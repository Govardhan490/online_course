<?php

    require '../core.inc.php';
    require '../connect.inc.php';

    if(!loggedin() || (loggedin() && ($_SESSION['role'])!="faculty"))
        header("Location:../index.php");
    
    if(!isset($_SESSION['create_test_course_id']))
        header("Location:create_tests.php");
    
    if(isset($_POST['video']) && isset($_POST['no_questions']) && isset($_POST['total_marks']))
    {
        if($_POST['video'] == 'yes' && isset($_POST['link']))
        {
            $link = $_POST['link'];
            $required = 1;
        }
        else
        {
            $required = 0;
        }
        $no_questions = $_POST['no_questions'];
        $total_marks = $_POST['total_marks'];
    }
    else
    {
        header("Location:create_tests.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Tests</title>
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
</head>
<body style="background-color: rgb(255, 255, 128);">
    <div class="container-fluid pt-1">
        <div class="card">
            <div class="card-header p-3" style="text-align:center;display:inline;">
                <h1 style="font-family: Amita;"><b><i>Create Tests</i></b></h1>
                <button type="button" onclick="time_out()" class="btn btn-danger" style="float: right;">Cancel</button>
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
                <form name="questions_form" id="questions_form" action="create_tests_4.php" method="post">
                    <div class='row' style="padding: 5px;">
                        <label for="course_id" class="col-sm-1">Course Id</label>
                        <label for="test_id" class="col-sm-1">Test Id</label>
                        <label for="no_questions" class="col-sm-1">Questions</label>
                        <label for="total_marks" class="col-sm-1">Marks</label>
                        <label for="video_required" class="col-sm-2">Video</label>
                        <label for="link" class="col-sm-6">Video Link</label>
                        <br>
                        <input type="text" name="course_id" id="course_id" style="text-align: center" class="form-control col-sm-1" readonly value="<?php echo $_SESSION['create_test_course_id']; ?>">
                        <input type="text" name="test_id" id="test_id" style="text-align: center" class="form-control col-sm-1" readonly value="<?php echo $_SESSION['create_test_test_id'] ?>">
                        <input type="number" name="no_questions" id="no_questions" style="text-align: center" class="form-control col-sm-1" readonly value="<?php echo $no_questions; ?>">
                        <input type="number" name="total_marks" id="total_marks" style="text-align: center" class="form-control col-sm-1" readonly value="<?php echo $total_marks; ?>">
                        <input type="text" name="video_required" id="video_required" style="text-align: center" class="form-control col-sm-2" readonly value="<?php if($required == 1){echo "Required";}else{echo "Not Required";} ?>">
                        <input type="url" name="link" id="link" style="text-align: center" class="form-control col-sm-6" readonly value="<?php if($required == 1){echo $link;}?>">
                    </div>
                    <br>
                    <div id="tabs">
                        <ul>
                            <?php 
                                for($i=1;$i<=$no_questions;$i++)
                                {
                                    echo "<li><a href='#Q-$i'>Q-$i</a></li>";
                                }
                            ?>
                        </ul>
                        <?php
                            for($i=1;$i<=$no_questions;$i++)
                            {
                                echo "<div class'row' id='Q-"."$i' style='padding: 10px;'>
                                        <div class='form-group'>
                                            <textarea onfocusout='validate(\"q"."$i\")' class='form-control col' name='q"."$i' id='q"."$i' rows='2' placeholder='Question "."$i'></textarea>
                                            <div class='invalid-feedback'>Please fill out this field.</div>
                                        </div>
                                        <br>
                                        
                                        <div class='form-group'>
                                            <input class='form-control' onfocusout='validate(\"q"."$i"."o1\")' type='text' name='q"."$i"."o1' id='q"."$i"."o1' placeholder='Option 1'>
                                            <div class='invalid-feedback'>Please fill out this field.</div>
                                        </div>
                                        <br>
                                        
                                        <div class='form-group'>
                                            <input class='form-control' onfocusout='validate(\"q"."$i"."o2\")' type='text' name='q"."$i"."o2' id='q"."$i"."o2' placeholder='Option 2'>
                                            <div class='invalid-feedback'>Please fill out this field.</div>
                                        </div>
                                        <br>
                                        
                                        <div class='form-group'>
                                            <input class='form-control' type='text' onfocusout='validate(\"q"."$i"."o3\")' name='q"."$i"."o3' id='q"."$i"."o3' placeholder='Option 3'>
                                            <div class='invalid-feedback'>Please fill out this field.</div>
                                        </div>
                                        <br>
                                        
                                        <div class='form-group'>
                                            <input class='form-control' type='text' onfocusout='validate(\"q"."$i"."o4\")' name='q"."$i"."o4' id='q"."$i"."o4' placeholder='Option 4'>
                                            <div class='invalid-feedback'>Please fill out this field.</div>
                                        </div>
                                        <br>
                                        
                                        <div class='form-group row'>
                                            <div class='col-sm-6'>
                                                <select onfocusout='answer_validate(\"q"."$i"."ans\")' name='q"."$i"."ans' id='q"."$i"."ans' class='custom-select'>
                                                    <option value='' disabled selected>Answer</option>
                                                    <option value='1'>1</option>
                                                    <option value='2'>2</option>
                                                    <option value='3'>3</option>
                                                    <option value='4'>4</option>
                                                </select>
                                            <div class='invalid-feedback'>Please fill out this field.</div>
                                            </div>
                                        
                                            <div class='col-sm-6'>
                                                <select onfocusout='answer_validate(\"q"."$i"."marks\")' name='q"."$i"."marks' id='q"."$i"."marks' class='custom-select'>
                                                <option value='' disabled selected>Marks</option>
                                                    <option value='1'>1</option>
                                                    <option value='1.5'>1.5</option>
                                                    <option value='2'>2</option>
                                                    <option value='2.5'>2.5</option>
                                                    <option value='3'>3</option>
                                                    <option value='3.5'>3.5</option>
                                                    <option value='4'>4</option>
                                                </select>
                                                <div class='invalid-feedback'>Please fill out this field.</div>
                                            </div>
                                        </div>";
                                if($i<$no_questions)
                                {
                                    echo "<br><div align='right' class='form-group'> <button type='button' class='btn btn-success text-right next-tab' onclick='nextTab(".$i.")'> Next </button> </div></div>\n";
                                }
                                else
                                {
                                    echo "<br><div align='right' class='form-group'> <button type='button' onclick='full_validate(".$no_questions.")' class='btn btn-success text-right next-tab'> Submit </button> </div></div>\n";
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

<script>

function validate(id) {
    if ($("#" + id).val() == null || $("#" + id).val().length == 0) {
        $("#" + id).removeClass("is-valid");
        $("#" + id).addClass("is-invalid");
        return 0;
    } else {
        $("#" + id).removeClass("is-invalid");
        $("#" + id).addClass("is-valid");
        return 1;
    }
}

function answer_validate(id) {
    x = $("#" + id).val();
    if (x == null) {
        $("#" + id).removeClass("is-valid");
        $("#" + id).addClass("is-invalid");
        return 0;
    } else {
        $("#" + id).removeClass("is-invalid");
        $("#" + id).addClass("is-valid");
        return 1;
    }
}

function time_out() {
    var ip = '<?php echo $server_ip; ?>';
    window.location.href = 'http://' + ip + '/faculty_src/create_tests.php';
}

function nextTab(x) {
    var res = 0;

    res += validate('q' + x);
    res += validate('q' + x + 'o1');
    res += validate('q' + x + 'o2');
    res += validate('q' + x + 'o3');
    res += validate('q' + x + 'o4');
    res += answer_validate('q' + x + 'ans');
    res += answer_validate('q' + x + 'marks');
    if (res == 7) {
        $("#tabs").tabs({ active: x });
    }
}

function full_validate(no_tabs) {
    var i;
    var flag = 0;
    for(i=1;i<=no_tabs;i++)
    {
        var res = 0;
        res += validate('q' + i);
        res += validate('q' + i + 'o1');
        res += validate('q' + i + 'o2');
        res += validate('q' + i + 'o3');
        res += validate('q' + i + 'o4');
        res += answer_validate('q' + i + 'ans');
        res += answer_validate('q' + i + 'marks');
        if(res != 7)
        {
            flag = 1;
            $("#tabs").tabs({ active: i-1 });
            break;
        }
    }
    if(flag == 0)
    {
        var total_marks = <?php echo "$total_marks"; ?>;
        var assigned_marks = 0;
        for(i=1;i<=no_tabs;i++)
        {
            assigned_marks += parseInt($("#q"+i+'marks').val());
        }
        if(assigned_marks != total_marks)
        {
            for(i=1;i<=no_tabs;i++)
            {
                $("#q"+i+'marks').val(null);
                answer_validate('q' + i + 'marks');
            }
            $("#tabs").tabs({ active: 0 });
            flag = 1;
            $("#alert").css("display","block");
            $(window).scrollTop(0);
        }
        else
        {
            $("#dialog-confirm").dialog({
                resizable: false,
                height: "auto",
                width: 400,
                modal: true,
                buttons: {
                    "Submit": function() {
                        $(this).dialog("close");
                        document.getElementById("questions_form").submit();
                    },
                    Cancel: function() {
                        flag = 0;
                        $(this).dialog("close");
                    }
                }
            });
        }
    }
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
</html>