<?php

session_start();

if (isset($_POST["email"]))
{
  $config = parse_ini_file("finaldb.ini");
  $dbh = new PDO($config['dsn'], $config['username'], $config['password']);

  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  try {
    $statement = $dbh->prepare("SELECT email from student_fp where Email=:email");

    $statement->execute(array(':email' => $_POST['email']));
    $result = $statement->fetch();

    if($result == false)
    {
      echo "Do you even go here?";
      die();
    }
?>

<html>

  <body>
    <link rel="stylesheet" type="text/css" href="main.css">

        <div class="takeExam">
          Take Exam
          <a href="googleSuccess.php">
            <span class="link-spanner"></span>
          </a>
        </div>

        <div class="viewResults">
          View Results
          <a href="googleSuccess.php">
            <span class="link-spanner"></span>
          </a>
        </div>

  </body>

</html>

<?php
  } catch (PDOException $e) {
    print "Error!" . $e -> getMessage()."<br/>";
    die();
  }
}
else
  echo "You shouldn't be here";
?>
