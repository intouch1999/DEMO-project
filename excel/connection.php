<?php
$servername = "localhost";
$username = "Username_sivanat";
$password = "sivanatdev";
try {
  $conn = new PDO("mysql:host=$servername;dbname=jumnum_db", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "Connection failed please contact admin";
}
?>