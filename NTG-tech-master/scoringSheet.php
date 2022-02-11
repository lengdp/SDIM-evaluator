<!DOCTYPE html>
<!-- // 改表格 -->
<html lang="en">


<!-- Check current user -->

<?php 
        if (!session_id()){
            session_start();
        } 
        if(isset($_SESSION['user'])){
            $evaluator =  $_SESSION['user'];
            if(isset($_SESSION['role'])){
                if($_SESSION['role'] != 'student'){
                    header("Location:login.php");
                }
            }
        }
        else{
            header("Location:login.php");
        }
?>


<head>
    <link rel="shortcut icon" href="SDIM-coi.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SDIM</title>

     <style>
        body{
               background-color: #c1cbd7;
           }
        table{
            font-family: verdana,arial,sans-serif;
            font-size:11px;         
            color:#333333;
            border-width: 1px;
            border-color: #666666;
            border-collapse: collapse;
            width: 100%;
        }
        th {
            border-width: 1px;
            padding: 6px;
            border-style: solid;
            border-color: #666666;
            background-color:#e0e5df;
            width:10%;
            text-align:center;
        }
        td{
            border-width: 1px;
            padding: 6px;
            border-style: solid;
            border-color: #666666;
            background-color: #fffaf4;
            /* background-color: #000; */
            width:10%;
            text-align:center;
        }
        /* .comment{
            width:20%;
        } */
        td.invalid {
            border-width: 1px;
            padding: 6px;
            border-style: solid;
            border-color: #666666;
            background-color: #FF9696;
            width:10%;
            text-align:center;
        }
        #course {
            width: 100%;
        }
        select{
            width: 40%;
            margin: 0 30%;
        }
        .button-change{
            padding: 0;
        }
        #change-grade{
            width: 100%;
            display: block;
            height: 100%;
            border: none;
            color: #000;
            margin: 0;
            padding: 0;
            /* display: inline-block; */
        }#change-grade:hover{
            background-color: #dedede;
        }
        #get-grade{
            width: 100%;
            display: block;
            height: 100%;
            border: none;
            color: #000;
            margin: 0;
            padding: 0;
            /* display: inline-block; */
        }#get-grade:hover{
            background-color: #dedede;
        }
    </style>
    <script src="https://cdn.staticfile.org/vue/2.2.2/vue.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>
        function logout(){
                    $.post("logout.php")
                    location.reload(true);
                }
    </script>

</head>
<body>
    <!-- HEAD -->   
    <div id = "titleOfWeb" style = "width:100%;height:50px;background-color:#A79DA5;">
        <div id = "headBlank1" style ="width:10%;height:50px;float:left;">
        </div>
        <div id = "headBlank2" style ="width:20%;height:50px;float:left;">
            <img src="SDIM.svg" alt="SDIM LOGO" width="150">
        </div>
        <div id = "headBlank3" style ="width:10%;height:50px;float:left;">
        </div>
        <div id = "headBlank4" style ="width:20%;height:50px;float:left;">
            <h1 style = "text-align: center; font-size:20px;">SDIM分数管理</h1>
        </div>
        <div id = "headBlank5" style ="width:20%;height:50px;float:left;font-size:10px;line-height:10px;text-align: center;">
            <p>Current User: <?php echo $_SESSION['user'];?> </p>
            <p>Role: Student</p>
        </div>
        <div id = "headBlank6"  style ="width:10%;height:50px;float:left;position:relative;">
            <button id ="logout" type="button" onclick="logout()" style = "width:80%;height:50%;position:absolute;left:5%;top:25%;text-align:center;font-size:5px;">Logout</button>
        </div>
        <div id = "headBlank7" style ="width:10%;height:50px;float:left;position:relative;">
        </div>
    </div>

    <!-- BLANK -->  
    <div id = "blank" style = "width:100%;height:50px;">
        <p></p>
    </div>
    
    <!-- SCORING SHEET -->
    <div id = "scoringSheet">
            <table>
                <tr>
                    <th>Name</th>
                    <td>{{evaluator}}</td>
                    <th>Course</th>
                    <td colspan="2">
                        <select v-model='course' @change = "getScoringSheetData();">
                        <option align="center" v-for = 'coursename in courseList' :value='coursename'>{{coursename}}</option>
                        </select>
                    </td>
                    <th>Week</th>
                    <td v-Text="week" contenteditable @input="weekCheck" colspan="2" :class = "{invalid: !weekValid}">3</td>
                    <td class="button-change"><button type="button" id="get-grade" @click="weekCheck">查询分数</button></td>
                </tr>

                <!-- lengdp -->
                <tr>
                    <td>打分</td>
                    <td contenteditable id="KnowledgeAcquisition"></td>
                    <td contenteditable id="Motivation"></td>
                    <td contenteditable id="Communication"></td>
                    <td contenteditable id="HandsOnSkills"></td>
                    <td contenteditable id="ThinkingSkills"></td>
                    <td contenteditable id="Responsibility"></td>
                    <td contenteditable id="ProjectExecution"></td>
                    <td id="button-change"><button id="change-grade" type="button" @click="changeGrade">修改分数</button></td>
                </tr>
                <tbody>
                    <tr>
                        <th>Item\Evaluatee</th>
                        <th v-for = "i in rubrics.length">{{rubrics[i-1]}}</th>
                        <th>Comment</th>
                    </tr>
                    <tr v-for = "person in evaluations">
                        <td class="personName">{{person.evaluatee}}</td>
                        <td v-for = "i in rubrics.length" v-Text="person.score[i-1]" contenteditable='true' @input = "person.score[i-1]=$event.target.innerText;" :class="person.valid[i-1]?rubrics[i-1]:('invalid '+rubrics[i-1])">
                        </td>
                        <td v-Text="person.comment" contenteditable='true' @input = "person.comment=$event.target.innerText;"></td>
                    </tr>
                </tbody>

                <!-- <tbody>
                    <tr>
                        <th>Item\Evaluatee</th>
                        <th v-for = "person in evaluations">
                        {{person.evaluatee}}
                        </th>
                    </tr>
                    <tr v-for = "i in rubrics.length">       
                        <th>{{rubrics[i-1]}}</th>
                        <td v-for = "person in evaluations" v-Text="person.score[i-1]" contenteditable @input = "person.score[i-1]=$event.target.innerText;" :class = "{invalid: !person.valid[i-1]}">
                        </td>
                    </tr>
                    <tr>
                        <th>Comment</th>
                        <td v-for = "person in evaluations" v-Text="person.comment" contenteditable @input = "person.comment=$event.target.innerText;">
                    </tr>
                <tbody> -->
            </table>
            <button @click = "submitEvaluation">Submit</button>
            <p v-HTML = "alertText"></p>

    </div>

    <script>
        new Vue({
            el:"#scoringSheet",
            data:{
                evaluations:[],
                week:"",
                weekValid:true,
                evaluator:"<?php echo $_SESSION['user'];?>",
                date:"",
                rubrics:[],
                course:"",
                alertText:"Response：",
                errorCount:0,
                submitAllowed:true,
                courseList:<?php echo json_encode($_SESSION['courseList'])?>,
                data:<?php echo json_encode($_SESSION['newEvaluation']);?>
            },
            mounted:function(){
                // lengdp
                // 批量复制
                //console.log(evaluations)
                var that=this;
                //console.log(that.evaluations)
                $('table').bind('paste',function(e){
                    //消除默认粘贴
                    event.preventDefault();
                    //获取粘贴数据
                    var data=null;
                    var clipboardData=window.clipboardData||e.originalEvent.clipboardData;
                    data=clipboardData.getData('Text');
                    //console.log(data)
                    //console.log(data.replace(/\t/g, '\\t').replace(/\n/g, '\\n'));
                    //解析数据
                    var arr=data.split('\n').filter(function(item){
                        return (item!=="")
                    }).map(function (item){
                        return item.split("\t");
                    });
                    //console.log(arr);
                    //输出至表格
                    var tab=this;
                    //console.log($(e.target).parents('tr'))
                    var td = $(e.target);
                    //console.log(td)
                    //console.log(td[0].cellIndex)
                    //console.log(td.parents('tr')[0].rowIndex)
                    var startRow = td.parents('tr')[0].rowIndex; 
                    var startCell = td[0].cellIndex; 
                    var rows = tab.rows.length;  //总行数
                    evaLength=that.evaluations.length
                    groupLength=[]
                    for(var g=0;g<evaLength;g++){
                        groupLength[g]=that.evaluations[g].length
                    }
                    //console.log(groupStart)
                    //console.log(groupLength)
                    //console.log(startRow)
                    //console.log(startCell)
                    var personIndex=startRow-3;
                    //console.log("*****")
                    // console.log(groupIndex)
                    //console.log(personIndex)
                    for (var i = 0; i < arr.length && startRow + i < rows; i++) {
                    var cells = tab.rows[startRow + i].cells.length;  //该行总列数
                    for(var j = 0; j < arr[i].length && startCell + j < cells; j++) {
                        var cell = $(tab.rows[startRow + i]).find("td")[startCell + j];
                        //console.log(cell)
                        $(cell).text(arr[i][j]);
                        that.evaluations[personIndex+i].score[startCell-1+j]=parseFloat(arr[i][j])
                        }
                    }
                })
            },
            methods:{
                getScoringSheetData:function(){
                    this.evaluations = [];
                    if(this.course == ""){
                        this.course = this.courseList[0];
                    }
                    var params = new URLSearchParams();
                    params.append('evaluator',this.evaluator);
                    params.append('course',this.course);
                    var that = this;
                    axios
                        .post('getScoringSheetData.php',params)
                        .then(
                            function(response){
                                that.rubrics = response.data.rubrics;
                                var evaluateeNames = response.data.evaluatee;
                                for (person in evaluateeNames){
                                that.evaluations.push({evaluatee : evaluateeNames[person], score:new Array(that.rubrics.length), comment:"", valid:new Array(that.rubrics.length).fill(true)});
                                }
                            }

                        );
                    //获取当前时间并打印
                    let yy = new Date().getFullYear();
                    let mm = new Date().getMonth()+1;
                    let dd = new Date().getDate();
                    let hh = new Date().getHours();
                    let mf = new Date().getMinutes()<10 ? '0'+new Date().getMinutes() : new Date().getMinutes();
                    let ss = new Date().getSeconds()<10 ? '0'+new Date().getSeconds() : new Date().getSeconds();
                    //console.log(yy+'/'+mm+'/'+dd+' '+hh+':'+mf+':'+ss);
                    this.date=yy+'-'+mm+'-'+dd+'-'+hh+':'+mf+':'+ss;

                },
                submitEvaluation:function(){
                    if(confirm("确定提交？")){
                        function Evaluation(week, evaluator, evaluatee, course, scores, date,comment){
                        this.week = week;
                        this.evaluator = evaluator;
                        this.evaluatee = evaluatee;
                        this.course = course
                        /*
                        this.K = K;
                        this.M = M;
                        this.C = C;
                        this.H = H;
                        this.T = T;
                        this.R = R;
                        this.P = P;
                        */
                        this.scores = scores;
                        this.InputDate = date;
                        this.comment = comment;
                    }

                    function validateScore(personRecord, rubricsNumber, that){
                        invalid=[];
                        for (var i = 0; i < rubricsNumber; i++){
                            if(personRecord.score[i]<=0 || personRecord.score[i]>5 || isNaN(personRecord.score[i])){
                                that.submitAllowed = false;
                                invalid.push(i);
                                that.errorCount+=1;
                                alertContent = '<br>error' + that.errorCount + ': Score should be numbers between 0 and 5';
                                that.alertText+=alertContent;
                            }
                            else{
                                personRecord.valid[i]= true;
                            }
                        }
                        return invalid;
                    }

                    function validateWeek(week, that){
                        if(week<=0 || week>16 || isNaN(week) || Math.floor(week) != week){
                            that.submitAllowed = false;
                            that.weekValid = false;
                            that.errorCount+=1;
                            alertContent = '<br>error' + that.errorCount + ': week should be a number between 0 and 16';
                            that.alertText+=alertContent;
                        }

                    }

                    function validateCourse(that){
                        if(that.course == ""){
                            that.submitAllowed = false;
                            that.errorCount+=1;
                            alertContent = '<br>error' + that.errorCount + ':Please select a course';
                            that.alertText+=alertContent;
                        }

                    }

                    function validateComment(comment,that){
        
                        var sqlStr=sql_str().split(',');
                        
                        for(var i=0;i<sqlStr.length;i++){
                            if(comment.toLowerCase().indexOf(sqlStr[i])!=-1){
                                that.submitAllowed = false;
                                that.errorCount+=1;
                                alertContent = '<br>error' + that.errorCount + ':illegal words in comment: ' + sqlStr[i];
                                that.alertText+=alertContent;
                                that.commentValid = false;
                                break;
                            }
                        }
                    }

                    function sql_str(){
                        var str="and,delete,or,exec,insert,select,union,update,count,*,',join,>,<";
                        return str;
                    }

                    var evaluationsToSubmit = [];
                    var evaluations = this.evaluations;
                    var that = this;
                    this.errorCount = 0;
                    this.alertText = "";
                    this.submitAllowed = true;
                    this.weekValid = true;
                    
                    validateWeek(this.week, that);
                    validateCourse(that);
                    for(person in evaluations){
                        var personRecord = evaluations[person];
                        invalid = validateScore(personRecord,this.rubrics.length,that);
                        for(i in invalid){
                            this.evaluations[person].valid[invalid[i]] = false;
                            this.$forceUpdate();
                        }
                        validateComment(personRecord.comment,that);
                        evaluationsToSubmit.push(
                            new Evaluation(
                                this.week, this.evaluator, personRecord.evaluatee, 
                                this.course, personRecord.score, this.date, personRecord.comment));
                    }
                    if (this.submitAllowed == true){
                        var evaluationsInJson = JSON.stringify(evaluationsToSubmit)
                        $.post("handleEvaluations.php",{
                            evaluationData:evaluationsInJson,
                            'course':that.course
                        },
                        function(data, status){
                            alert(data);
                            console.log(data);
                            this.alertText += data;
                        });
                    }
                    else{
                        this.alertText += '<br>Submit fail';
                    }
                    }
                },
                //lengdp
                changeGrade:function(){
                    //console.log("hhh")
                    var KnowledgeAcquisition=($("#KnowledgeAcquisition").text());
                    //$(".KnowledgeAcquisition").focus()
                    //console.log($(".KnowledgeAcquisition"))
                    $(".KnowledgeAcquisition").text(KnowledgeAcquisition);
                    var Motivation=($("#Motivation").text());
                    $(".Motivation").text(Motivation);
                    var Communication=($("#Communication").text());
                    $(".Communication").text(Communication)
                    var HandsOnSkills=($("#HandsOnSkills").text());
                    $(".HandsOnSkills").text(HandsOnSkills)
                    var ThinkingSkills=($("#ThinkingSkills").text());
                    $(".ThinkingSkills").text(ThinkingSkills)
                    var Responsibility=($("#Responsibility").text());
                    $(".Responsibility").text(Responsibility)
                    var ProjectExecution=($("#ProjectExecution").text());
                    $(".ProjectExecution").text(ProjectExecution)
                    gradeArr=[KnowledgeAcquisition,Motivation,Communication,HandsOnSkills,ThinkingSkills,Responsibility,ProjectExecution]
                    //console.log(gradeArr)
                    //console.log(this.rubrics.length)
                    evaluations=this.evaluations
                    //console.log(evaluations)
                    groupLength=evaluations.length
                    //console.log(groupLength)
                    for(var p=0;p<groupLength;p++){
                        for(var i=0;i<this.rubrics.length;i++){
                            evaluations[p].score[i]=gradeArr[i]
                        }
                    }
                    
                    //console.log(this.evaluations)
                },
                weekCheck:function(e){
                    function Evaluation(week, evaluator,course){
                        this.week = week;
                        this.evaluator = evaluator;
                        this.course=course;
                    }
                    var that=this
                    //console.log("hhhh")
                    //console.log(this.evaluations)
                    //console.log(e.target.innerText)
                    that.week=e.target.innerText;
                    var weekInfo=[]
                    weekInfo.push(new Evaluation(this.week, this.evaluator,this.course))
                    //console.log(weekInfo)
                    var weekCheckInJson=JSON.stringify(weekInfo)
                    //console.log(weekCheckInJson)
                    $.post("viewWeek.php",{
                        evaluationData:weekCheckInJson,
                        'course':that.course
                    },function(data, status){
                            //console.log(data)
                            //alert(data);
                            this.alertText += data;
                        }
                    )
                    if(parseInt(that.week)==parseInt(that.data[0]["Week"])){
                        data=that.data
                        //console.log(data)
                        names=$(".personName")
                        for(var i=0;i<names.length;i++){
                            for(var j=0;j<data.length;j++){
                                if($(names[i]).text()==data[j]["personName"]["personName"]){
                                    for(var m=0;m< $(names[i]).siblings().length-1;m++){
                                        if(data[j]["RubricsItem"]==(m+1)){
                                            $($(names[i]).siblings()[m]).text(data[j]["Score"])
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                }
            },
            created(){
                    this.getScoringSheetData();
                }
            })
    </script>
</body>
</html>