<?php
    $course = isset($_POST['course']) ? $_POST['course'] : "?";
    $week = isset($_POST['week']) ? $_POST['week'] : "?";
   
    // $course='SDM242';
    // $week='5';
    $outputs= shell_exec("python summarizeEvaluation.py $course $week");
    //echo($outputs);
    $INweight = isset($_POST['INweight']) ? $_POST['INweight'] : "?";
    $TAweight = isset($_POST['TAweight']) ? $_POST['TAweight'] : "?";
    $STweight = isset($_POST['STweight']) ? $_POST['STweight'] : "?";
    // $INweight='0.6';
    // $TAweight='0.3';
    // $STweight='0.1';

    if($course == 'SDM242'){
        $STINweight = isset($_POST['STINweight']) ? $_POST['STINweight'] : "?";
        $STTAweight = isset($_POST['STTAweight']) ? $_POST['STTAweight'] : "?";
        // $STINweight='0.625';
        // $STTAweight='0.375';
        
        $outputc= shell_exec("python calcAverageScore.py $course $week");
        //echo($outputc);
        $output= shell_exec("python totalGrade4SDM242.py $course $week $INweight $TAweight $STweight $STINweight $STTAweight");
        //echo($output);
    }
    else{
        shell_exec("python totalGrade.py $course $week $INweight $TAweight $STweight");
    }
    $output = shell_exec("python radarMap.py $course $week");
    //echo($output);
    $outputg = shell_exec("python generateZip.py $course $week");
    // echo($outputg);

?>