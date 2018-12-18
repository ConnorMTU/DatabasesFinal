<html>
    	<body>
            	<h1>EXAM SCORES!!</h1>

    	</body>

</html>

<?php
session_start();

$resultTable = [];
$stuId = "00ABC";
try{
    global $stuId;
    $config = parse_ini_file("finaldb.ini");
    $dbh = new PDO($config['dsn'], $config['username'], $config['password']);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //$stuId = ($_SESSION['id']);
    $statement = $dbh->prepare("SELECT * FROM result_fp where id ="."'".$stuId."'");
    $statement->execute();
    $resultTable = $statement->fetchAll();
    $_SESSION['stuId'] = $stuId;
    //print_r($resultTable);
} catch (PDOException $e) {
    print "Error!".$e->getMessage()."<br/>";
    die();
}

//create some exam buttons with exam total
$examsAdded = ['bbbbb'];
foreach($resultTable as $row) {
    $exam_name = $row['exam_name'];
    $difExam = 0;
    foreach($examsAdded as $addedExam) {
   	 if($addedExam == $exam_name) {
   		 $difExam = 1;
   	 }
    }
    if( $difExam == 0 ) {
   	 $examsAdded[] = $exam_name;
    //    echo "</br>";
   	 createButton($row, $resultTable);
   	 echo "</br>";
    //    echo "<p>EXAM ADDED".$exam_name." </p>";
    //    echo "</br>";
   	 
    }

}
$testResult = 0;
function getExamScore($row, $resultTable) {
    //get student points
    $stuTotal = 0;
    foreach($resultTable as $rowIter) {
   	 $stuTotal = $stuTotal + $rowIter['student_pts'];
    }
    //printf($stuTotal);
    try {
   	 $config = parse_ini_file("finaldb.ini");
   		 $dbh = new PDO($config['dsn'], $config['username'], $config['password']);
   		 $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   		 //printf($row['exam_name']);
   		 $statement = $dbh->prepare("SELECT * FROM questions_fp where name ="."'".$row['exam_name']."'");
   		 $statement->execute();
   		 $examTable = $statement->fetchAll();
    //    echo "<br/><p>EXAMTABLE</p><br/>";
    //    print_r($examTable);
   	 //echo "<br/><p>EXAMTABLE</p><br/>";
    } catch (PDOException $e) {
   		 print "Error!".$e->getMessage()."<br/>";
   		 die();
    }
    
    //get exam total
    $examTotal=0;
    foreach($examTable as $row) {
   	 $examTotal = $row['points'] + $examTotal;
    }
    global $testResult;
    $testResult = $stuTotal/$examTotal;
    $testResult = number_format($testResult, 2, '.', '');
}

function createButton($row, $resultTable) {
    //using the row find the score the student got and the value with it.
    $score = getExamScore($row, $resultTable);
    global $testResult;
    $buttonLabel = $row['exam_name']." ".$score;
    echo '<form action="view.php" method = "post">
   	 <input type="submit" name="'.$buttonLabel.'" id="'.$buttonLabel.'" value="'.$buttonLabel." : ".$testResult.'" />
   	 </form>';
    //print_r(array_key_exists('exam1',$_POST));
    $_SESSION['testResult'.$row['exam_name']] = $testResult;
}
//session_destroy();
?>

