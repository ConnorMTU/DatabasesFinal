<?php
    session_start();
    //var_dump($_POST);
    $exam;
    foreach($_POST as $row) {
   	 $exam = $row;
   	 //echo "<p>".$row."</p>";
    }
    
    $info = explode(':', $exam);
    
    echo "<h1>What Happened</h1>";
    $resultTable = [];
    try {
            	//get exam total
            	$config = parse_ini_file("finaldb.ini");
            	$dbh = new PDO($config['dsn'], $config['username'], $config['password']);
            	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            	//printf($row['exam_name']);
            	$statement = $dbh->prepare("SELECT * FROM result_fp where exam_name ="."'".$info[0]."'"." and id ="."'".$_SESSION['stuId']."'");
            	$statement->execute();
            	$resultTable = $statement->fetchAll();
    	//  	echo "<br/><p>EXAMTABLE</p><br/>";
    	//    	print_r($resultTable);
            	//echo "<br/><p>EXAMTABLE</p><br/>";
    	} catch (PDOException $e) {
            	print "Error!".$e->getMessage()."<br/>";
            	die();
    	}

    //print results to screen
    echo "</br>";
    //find the total score
    //echo "<p>".$_SESSION['"testResult".$exam']."</p>";
    echo "<p>".$info[0]."Score:".$info[1]."</p>";    
    echo "</br>";
    echo "<pre>Student Answer   	 Solution    Points Earned</pre>";
    echo "</br>";
    foreach($resultTable as $row) {
   	 echo "<pre>".$row['student_answer']."   		 ".$row['correct_choice']."   	 ".$row['student_pts']."</pre>";
   	 echo "</br>";
    }
   	 
?>

