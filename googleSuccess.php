<?php

session_start();

if (isset($_POST["loggedin"]))
  echo "Success!";
else
  echo "You shouldn't be here";
?>
