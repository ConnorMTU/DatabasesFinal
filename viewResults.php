<?php
session_start();

$resultTable = [];
try{
        $config = parse_ini_file("finaldb.ini");
        $dbh = new PDO($config['dsn'], $config['username'], $config['password']);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement = $dbh->prepare("SELECT * FROM result_fp");
        $statement->execute();
        $resultTable = $statement->fetchAll();
        print_r($resultTable);
} catch (PDOException $e) {
        print "Error!".$e->getMessage()."<br/>";
        die();
}

//create some exam buttons with exam total 
foreach($resultTable
        //call function

function display() {
//display results herer 
//
}
?>

<html>
        <body>
                <h1>EXAM SCORES!!</h1>

        </body>

</html>
