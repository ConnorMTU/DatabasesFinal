<html>

  <body>
    <link rel="stylesheet" type="text/css" href="main.css">


<?php

session_start();

if (isset($_POST["exam"]))
{
  var_dump($_POST);
  // If they've chosen an exam
  // can use unset() to unset variable
}

else
{
  // User is picking their first exam
  $config = parse_ini_file("finaldb.ini");
  $dbh = new PDO($config['dsn'], $config['username'], $config['password']);

  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  try {
    $statement = $dbh->prepare("SELECT * from exam_fp");

    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

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
  } catch (PDOException $e) {}
}


?>
<script>

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

<html>
