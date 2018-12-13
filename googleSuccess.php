<?php

session_start();

// We check here to see if the user is already in a valid session. If they are
// reload the HTML at this point.
if (isset($_POST["email"]) or isset($_SESSION["id"]))
{
  // We use the goto to avoid duplicating code if a user is already logged in
  // and a valid session exists, avoids trying to requery for data we already have.
  if (isset($_SESSION["id"]))
    goto userLoggedIn;

  // We will only hit this point if the user is starting their session for the first time
  $config = parse_ini_file("finaldb.ini");
  $dbh = new PDO($config['dsn'], $config['username'], $config['password']);

  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  try {
    /* Given the login data from OAuth and what we passed to this page,
     start grabbing data from the databases, emails are unique but ID is the
     actual DB key. Use a prepared statement because we rely on an external
     source for data to input.
     */
    $statement = $dbh->prepare("SELECT id from student_fp where Email=:email");

    $statement->execute(array(':email' => $_POST['email']));
    $result = $statement->fetch();

    if($result == false)
    {
      echo "Do you even go here?";
      die();
    }

    $_SESSION["id"] = $result['id'];

    userLoggedIn:
?>

<html>

  <body>
    <link rel="stylesheet" type="text/css" href="main.css">

        <div class="takeExam">
          Take Exam
          <a href="takeExam.php">
            <span class="link-spanner"></span>
          </a>
        </div>

        <div class="viewResults">
          View Results
          <a href="viewResults.php">
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
