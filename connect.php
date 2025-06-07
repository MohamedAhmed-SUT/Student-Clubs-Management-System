<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "test";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);

} catch (Exception $e) {
    
    echo "Failed to connect to the database: " . $e->getMessage();
}

?>