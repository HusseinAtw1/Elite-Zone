<?php 
function connectDB() {
    $db_host = "localhost";
    $db_user = "root";
    $db_password = "";
    $db_name = "elite-zone";
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
    if ($mysqli->connect_errno) {
        error_log("Failed to connect to MySQL: " . $mysqli->connect_error);
        die("Sorry, there was a problem connecting to the database.");
    }
    return $mysqli;
}
?>