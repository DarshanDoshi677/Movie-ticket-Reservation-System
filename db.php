<?php
// Database connection parameters
$host = "localhost";
$port = "5432";
$dbname = "php";
$user = "postgres";
$password = "root";

// Create a PDO connection
$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";
$conn= new PDO($dsn);
?>
