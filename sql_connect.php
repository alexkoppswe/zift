<?php
try {
  $servername = "mysql.hostinger.se";
  $port = "";
  $username = "u413974798_user";
  $password = "8Wu2LBoU6mAT";
  $dbname = "u413974798_db";
  $dbh = new PDO("mysql:host=$servername;dbname=$dbname;port=$port", $username, $password);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) {
  echo "<br>".$e->getMessage();
  die();
}
?>