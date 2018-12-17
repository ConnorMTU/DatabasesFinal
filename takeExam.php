<html>

  <body>
    <link rel="stylesheet" type="text/css" href="main.css">


<?php

session_start();

if (isset($_SESSION['taken']) && isset($_SESSION['exam']))
{
  //var_dump($_POST);

  //echo "<br><br>";

  $config = parse_ini_file("finaldb.ini");
  $dbh = new PDO($config['dsn'], $config['username'], $config['password']);

  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  try {

    $statement = $dbh->prepare("SELECT question_number, correct_choice, points from questions_fp where name=:name");

    $statement->execute(array('name' => $_SESSION['exam']));
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    //echo "<br><br>";

    //var_dump($result);

    $student_result = 0;
    $exam_total = 0;

    // Calculate exam total
    for ($i = 0; $i < sizeof($result); $i++) {
      $exam_total += $result[$i]['points'];
    }

    $userSubmittedQuestions = array_keys($_POST);

    $statement = $dbh->prepare("INSERT INTO result_fp (id, exam_name, question, correct_choice, student_answer, student_pts) VALUES (:id, :exam_name, :question, :correct_choice, :student_answer, :student_pts)");

    //$statement->execute(array('name' => $_SESSION['exam']));

    // We only calculate based on what the student submitted, if they leave a question blank, they will not get credit for it.
    for ($i = 0; $i < sizeof($_POST); $i++) {
      for ($j = 0; $j < sizeof($result); $j++) {
        if ($result[$j]['question_number'] == $userSubmittedQuestions[$i]) {
          if ($result[$j]['correct_choice'] == $_POST[$userSubmittedQuestions[$i]]) {
            $student_result += $result[$j]['points'];

            /*echo "<br><br>";
            echo ':id ' . $_SESSION['id']. ' :exam_name ' . $_SESSION['exam']. ' :question ' . $userSubmittedQuestions[$i]. ' :correct_choice ' . $result[$j]['correct_choice']. ' :student_answer ' . $_POST[$userSubmittedQuestions[$i]]. ' :student_pts ' . $result[$j]['points'];
            echo "<br><br>";*/

            $statement->execute(array(':id' => $_SESSION['id'], ':exam_name' => $_SESSION['exam'], ':question' => $userSubmittedQuestions[$i], ':correct_choice' => $result[$j]['correct_choice'], ':student_answer' => $_POST[$userSubmittedQuestions[$i]], ':student_pts' => $result[$j]['points']));
            break;
          } else {
            /*echo "<br><br>";
            echo ' :id ' . $_SESSION['id']. ' :exam_name ' . $_SESSION['exam']. ' :question ' . $userSubmittedQuestions[$i]. ' :correct_choice ' . $result[$j]['correct_choice']. ' :student_answer ' . $_POST[$userSubmittedQuestions[$i]]. ' :student_pts ' . 0;
            echo "<br><br>";*/
            $statement->execute(array(':id' => $_SESSION['id'], ':exam_name' => $_SESSION['exam'], ':question' => $userSubmittedQuestions[$i], ':correct_choice' => $result[$j]['correct_choice'], ':student_answer' => $_POST[$userSubmittedQuestions[$i]], ':student_pts' => 0));
            break;
          }
        }
      }
    }

    echo "Total possible points: " . $exam_total . "<br>";
    echo "Total earned points: " . $student_result . "<br>";

    echo "You just submitted an exam, please continue to the View Results page to see more specific results.";
    unset($_SESSION['taken']);
    unset($_SESSION['exam']);
    echo "<br>";
    ?>

    <form action="googleSuccess.php" method="post">
      <input type="submit" value="Home">
    </form>

    <?php

  } catch (PDOException $e) {
    print "Error!" . $e -> getMessage()."<br/>";
    die();
  }
}
else if (isset($_POST["exam"]))
{
  // If they've chosen an exam
  // We don't want to use session here because we only want them taking it right
  // after they choose an exam

  // Start grabbing data for specific exam
  $config = parse_ini_file("finaldb.ini");
  $dbh = new PDO($config['dsn'], $config['username'], $config['password']);

  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  try {
    $statement = $dbh->prepare("SELECT question_number, points, question_prompt, choice_prompt, choices from questions_fp where name=:name");

    $statement->execute(array(':name' => $_POST['exam']));
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    //var_dump($result);

    //echo "<br><br>";

    // Start building questions
    //echo $result[0]['question_prompt'] . " (" . $result[0]['points'] . " pts)";

    echo "<form action='takeExam.php' method='post'>";

    // Iterate through multilevel array to start displaying all queried questions
    for ($i = 0; $i < sizeof($result); $i++) {
      echo $result[$i]['question_number'] . ". " . $result[$i]['question_prompt'] . " (" . $result[$i]['points'] . " pts)";
      echo "<br>";

      // For some reason data isn't stored correctly in table
      if (strlen($result[$i]['choices']) == 0)
      {
        echo "Broken question... contact test administrator";
        continue;
      }

      // Break apart choice_prompt
      $choice_prompt = explode(",", $result[$i]['choice_prompt']);
      $choices = explode(",", $result[$i]['choices']);

      // Iterate through total number of choices and display
      for ($j = 0; $j < sizeof($choices); $j++) {
        echo "<input type='radio' name='" . $result[$i]['question_number'] . "' value='" . $choices[$j] . "'>" . $choice_prompt[$j];
        echo "<br>";
      }

      echo "<br>";
    }

    echo "<br>";

    $_SESSION['taken'] = true;
    $_SESSION['exam'] = $_POST['exam'];

    ?>

    <!-- Placeholder button for submission, need to define submitExam() func -->
    <input type="submit" value="Submit">
  </form>
    <?php

  } catch (PDOException $e) {
    print "Error!" . $e -> getMessage()."<br/>";
    die();
  }
} else {
  // User is picking their first exam
  // Grab name data for all exams and use to display to user.
  $config = parse_ini_file("finaldb.ini");
  $dbh = new PDO($config['dsn'], $config['username'], $config['password']);

  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  try {
    $statement = $dbh->prepare("SELECT name from exam_fp");

    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Start building drop down menu from available exams in DB
    echo "<div id='wrapper'>";
    echo "<select id='exam' name='Exam'>";
    echo "<option value=''></option>";
    foreach ($result as $row) {
      echo "<option value='" . $row['name'] . "'>" . $row['name'] . "</option>";
    }
    echo "</select>";
    echo "</div>";
?>
    <button id="submit" onclick="submitData()">Submit</button>
    <div id="hidden_form_container" style="display:none;"></div>

<?php
  } catch (PDOException $e) {
    print "Error!" . $e -> getMessage()."<br/>";
    die();
  }
}

?>
<script>

  // Resuse the function we used for OAuth to submit post data because we already
  // had it so why do it differently if it works.
  function submitData() {
    var theForm, exam;
    theForm = document.createElement('form');
    theForm.action = 'takeExam.php';
    theForm.method = 'post';


    exam = document.createElement('input');
    exam.type = 'hidden';
    exam.name = 'exam';

    var dropdown = document.getElementById('exam');
    var examString = dropdown.options[dropdown.selectedIndex].text;

    exam.value = examString;

    theForm.appendChild(exam);
    document.getElementById('hidden_form_container').appendChild(theForm);
    theForm.submit();
  }

</script>

  </body>

</html>
