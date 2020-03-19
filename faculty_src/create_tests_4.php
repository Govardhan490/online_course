<?php

require '../core.inc.php';
require '../connect.inc.php';

if(!loggedin() || (loggedin() && ($_SESSION['role'])!="faculty"))
    header("Location:../index.php");

if(!isset($_SESSION['create_test_course_id']))
    header("Location:create_tests.php");

if(isset($_POST['course_id']) && isset($_POST['test_id']) && isset($_POST['no_questions']) && isset($_POST['total_marks']) && isset($_POST['video_required']) && isset($_POST['link']))
{
    $course_id = $_POST['course_id'];
    $test_id = $_POST['test_id'];
    $no_questions = $_POST['no_questions'];
    $total_marks = $_POST['total_marks'];
    if($_POST['video_required'] == "Required")
    {
        $video_required = 1;
        $link = $_POST['link'];
    }
    else
    {
        $video_required = 0;
    }

    if(!empty($course_id) && !empty($test_id) && !empty($no_questions) && !empty($total_marks))
    {
        $xml = new DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = true;
        $test = $xml->createElement("Test");
        $xml->appendChild($test);
        $no_of_questions = $xml->createElement("no_of_questions",(int)$no_questions);
        $test->appendChild($no_of_questions);
        $marks = $xml->createElement("total_marks",(int)$total_marks);
        $test->appendChild($marks);
        $video = $xml->createElement("video");
        $video->setAttribute("required",$video_required);
        if($video_required == 1)
        {
            $video_link = $xml->createElement("link",$link);
            $video->appendChild($video_link);
        }
        $test->appendChild($video);
        $questions = $xml->createElement("questions");
        $test->appendChild($questions);

        for($i=1;$i<=$no_questions;$i++)
        {
            if(isset($_POST["q$i"]) && isset($_POST["q$i"."o1"]) && isset($_POST["q$i"."o2"]) && isset($_POST["q$i"."o3"]) && isset($_POST["q$i"."o4"]) && isset($_POST["q$i"."marks"]))
            {
                $question_data = $_POST["q$i"];
                $option_data_1 = $_POST["q$i"."o1"];
                $option_data_2 = $_POST["q$i"."o2"];
                $option_data_3 = $_POST["q$i"."o3"];
                $option_data_4 = $_POST["q$i"."o4"];
                $marks_data = $_POST["q$i"."marks"];
                if(!empty($question_data) && !empty($option_data_1) && !empty($option_data_2) && !empty($option_data_3) && !empty($option_data_4) && !empty($marks_data))
                {
                    $question_head = $xml->createElement("question"); 
                    $questions->appendChild($question_head);
                    $question = $xml->createElement("q",$question_data);
                    $option1 = $xml->createElement("option_1",$option_data_1);
                    $option2 = $xml->createElement("option_2",$option_data_2);
                    $option3 = $xml->createElement("option_3",$option_data_3);
                    $option4 = $xml->createElement("option_4",$option_data_4);
                    $marks = $xml->createElement("marks",$marks_data);
                    $question_head->appendChild($question);
                    $question_head->appendChild($option1);
                    $question_head->appendChild($option2);
                    $question_head->appendChild($option3);
                    $question_head->appendChild($option4);
                    $question_head->appendChild($marks);

                }
            }
        }

        echo "<xmp>". $xml->saveXML()."</xmp>";
        $dir = "../courses/".$course_id."/tests";
        if ( !file_exists( $dir ) && !is_dir( $dir ) ) 
        {
            mkdir( $dir,0777,true);       
        } 
        $target_file = $dir = "../courses/".$course_id."/tests/".$test_id.".xml";
        if($xml->save($target_file))
        {
            $xml1 = new DOMDocument('1.0', 'utf-8');
            $xml1->formatOutput = true;
            $solutions = $xml1->createElement("Solutions");
            $xml1->appendChild($solutions);
            $no_of_questions = $xml1->createElement("no_of_questions",(int)$no_questions);
            $solutions->appendChild($no_of_questions);
            for($i=1;$i<=$no_questions;$i++)
            {
                if(isset($_POST["q$i"."ans"]))
                {
                    $ans_data = $_POST["q$i"."ans"];
                    $ans = $xml1->createElement("solution",$ans_data);
                    $solutions->appendChild($ans);
                }
            }
            echo "<xmp>". $xml1->saveXML()."</xmp>";
            $dir = "../courses/".$course_id."/tests";
            if ( !file_exists( $dir ) && !is_dir( $dir ) ) 
            {
                mkdir( $dir,0777,true);       
            } 
            $target_file = $dir = "../courses/".$course_id."/tests/".$test_id."_solutions.xml";
            if($xml1->save($target_file))
            {
                $test_no = (int)substr($test_id,1,2);
                $query = $conn->prepare("UPDATE `course` SET `no_of_tests` = ? WHERE `course_id` = ?");
                $query->bind_param("is",$test_no,$course_id);
                if($query->execute())
                {
                    $_SESSION['create_success'] = 1;
                    header("Location:create_tests.php");
                }
                else
                {
                    $target_file1 = $dir = "../courses/".$course_id."/tests/".$test_id.".xml";
                    $target_file2 = $dir = "../courses/".$course_id."/tests/".$test_id."_solutions.xml";
                    unlink($target_file1);
                    unlink($target_file2);
                    header("Location:create_tests.php");
                }
            }
            else
            {
                $_SESSION['create_success'] = 0;
                $target_file1 = $dir = "../courses/".$course_id."/tests/".$test_id.".xml";
                unlink($target_file1);
                header("Location:create_tests.php");
            }
        }
        else
        {
            $_SESSION['create_success'] = 0;
            header("Location:create_tests.php");
        }
    }

}
else
{
    header("Location:create_tests.php");
}
?>