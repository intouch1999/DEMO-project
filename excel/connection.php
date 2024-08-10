<?php
$servername = "localhost";
$username = "Username";
$password = "Password";
try {
  $conn = new PDO("mysql:host=$servername;dbname=DB_NAME", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "Connection failed please contact admin";
}
?>