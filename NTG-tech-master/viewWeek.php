<?php 
    if (!session_id()){
        //echo "刷新了";
        session_start();
    } 
    //$_SESSION["newEvaluation"]="";
    header("Content-Type:text/html;charset=utf-8");
    $evaluationsInJson = htmlspecialchars_decode(isset($_POST['evaluationData']) ? htmlspecialchars($_POST['evaluationData']) : '');
    $course = htmlspecialchars_decode(isset($_POST['course']) ? htmlspecialchars($_POST['course']) : '');
    $evaluations = json_decode($evaluationsInJson);
    // echo 'view';
    // echo 'evaluations:'.$evaluations;
    include ('dbinfo.php');
    
    /*
    $servername = "39.102.54.216";
    $username = "root";
    $password = "2788098";
    $port = 3306;
    */

    $dbname = $course;
    //echo $dbname;
    $conn = connectToServer($servername, $DBusername, $DBpassword, $dbname, $port);
    $evaluationAlreadyExist = alreadySubmitted($conn, $evaluations[0]);
    //echo 'count'.count($evaluations);
    if($evaluationAlreadyExist){
        echo 'Evaluation on week '.$evaluations[0] ->{'week'}.' Already Exist <br />';
        for ($evaluationNumber=0; $evaluationNumber < count($evaluations); $evaluationNumber++){
            $_SESSION["newEvaluation"]="";
            getEvaluation($conn,$evaluations[$evaluationNumber]);
            //echo 'again:'.$_SESSION["newEvaluation"][0]['Week'];
        }
    }
    $conn -> close();

    
    function connectToServer($servername, $username, $password, $dbname, $port){
        $conn = new mysqli($servername, $username, $password, $dbname, $port);
        $conn -> query("SET NAMES utf8");
        // 检测连接
        if ($conn->connect_error) {
            die("连接失败: " . $conn->connect_error);
        } 
        return $conn;
    }
    
    function getEvaluation($conn,$evaluation){
        //echo "getEcaluation";
        $week = $evaluation -> {'week'};
        //echo $week;
        $evaluatorName = $evaluation ->{'evaluator'};
        $evaluator = getPerson($conn, $evaluatorName);
        //echo $evaluator;
        $evaluatorId = $evaluator['id'];
        //echo $evaluatorId;
        //echo $week;
        $sql = "SELECT * FROM `Evaluation` WHERE EvaluatorID = '$evaluatorId' and Week = $week;";
        $result = $conn->query($sql);
        //$result=json_encode($result);
        $data=array();
        //$row=mysqli_fetch_array($result);
        //echo 'row'.$row;
        //echo mysqli_num_rows($result);
        // echo count($result);
        // echo "------";
        while($row=mysqli_fetch_row($result)){
            $data[]=$row;
            //echo $row;
            //print_r($row);
        }
        $result->free();
        //print_r($data);
        //print_r(count($data));
        $newEvaluation=Array();
        for($item=0;$item<count($data);$item++){
            $newEvaluation[$item]['id']=$data[$item][0];
            $newEvaluation[$item]['Week']=$data[$item][1];
            //echo 'week:'.$data[$item][1];
            $newEvaluation[$item]['personName']=getName($conn,$data[$item][2]);
            $newEvaluation[$item]['EvaluatorID']=$data[$item][3];
            $newEvaluation[$item]['RubricsItem']=$data[$item][4];
            $newEvaluation[$item]['Score']=$data[$item][5];
            $newEvaluation[$item]['InputDate']=$data[$item][6];
            $newEvaluation[$item]['Source']=$data[$item][7];
        }
        // axios.post('administratorScoringSheet.php',$newEvaluation).then(function(response){
        //     that.calcTotalGradeWorkState = "Done";
        // });
        $_SESSION["newEvaluation"]=$newEvaluation;
        // $newEvaluation=json_encode($newEvaluation);
        // $file='tempData.json';
        // file_put_contents($file,$newEvaluation);
        // $fp=fopen($file,'a');
        // fwrite($fp,$newEvaluation);
        // fclose($fp);
        //print_r($newEvaluation);
    }

    function getName($conn, $id){
        $sqlGetName="SELECT personName FROM Persons WHERE id = '$id'";
        $result = $conn -> query($sqlGetName);
        $row = $result ->fetch_assoc();
        $result->free();
        //print_r($row);
        return $row;
    }

    function getPerson($conn, $name){
        $sqlGetId = "SELECT id , PersonRole FROM Persons WHERE personName = '$name'";
        $result = $conn -> query($sqlGetId);
        $row = $result ->fetch_assoc();
        $result->free();
        return $row;
    }


    function addEvaluation($conn, $evaluation){
        $week = $evaluation -> {'week'};
        $evaluatorName = $evaluation ->{'evaluator'};
        $evaluator = getPerson($conn, $evaluatorName);
        $evaluatorId = $evaluator['id'];
        $evaluateeId = getPerson($conn, $evaluation ->{'evaluatee'})['id'];
        $inputDate = $evaluation -> {'InputDate'};
        $dataSource = $evaluator['PersonRole'];
        $scores = $evaluation -> {'scores'};
        $comment = $evaluation ->{'comment'};
        $getRubricsNumbersql = "SELECT count(*) FROM Rubrics;";
        $result = $conn-> query ($getRubricsNumbersql);
        $rubricsNumber = $result -> fetch_assoc()['count(*)'];
        $sql = "";
        if(!empty($comment)){
            $commentsql = "INSERT INTO `Comments` VALUES (NULL, $week, $evaluateeId, $evaluatorId, '$comment', $dataSource, '$inputDate');";
            $conn -> query($commentsql);
        }
        for ($rubrics = 0; $rubrics < $rubricsNumber; $rubrics++ ){
            $rubricsItem = $rubrics + 1;
            $score = $scores[$rubrics];
            $sql .= "INSERT INTO `Evaluation` VALUES (NULL, $week, $evaluateeId, $evaluatorId, $rubricsItem, '$score', '$inputDate', $dataSource);";
        }
        if ($conn->multi_query($sql) === TRUE) {            
            while ($conn->more_results() && $conn->next_result())
            {
                //什么也不做
            }
        }

    }
    function addSubmitRecord($conn, $evaluation){
        $week = $evaluation -> {'week'};
        $evaluatorName = $evaluation ->{'evaluator'};
        $inputDate = $evaluation -> {'InputDate'};
        $sql ="INSERT INTO `SubmitRecord` VALUES (NULL, '$evaluatorName', $week, '$inputDate');";
        $conn->query($sql); 
    }
    function alreadySubmitted($conn, $evaluation){
        $week = $evaluation -> {'week'};
        $evaluatorName = $evaluation ->{'evaluator'};
        $sql = "SELECT * FROM `SubmitRecord` WHERE Evaluator = '$evaluatorName' and Week = $week;";
        $result = $conn->query($sql);
        //echo mysqli_fetch_array($result);
        return mysqli_num_rows($result);
    }
?>