<?php
// Establish a connection to the MySQL database
$con = mysqli_connect("localhost", "root", "", "expense_tracker");

// Check for connection errors
if (mysqli_connect_error()) {
    // If connection fails, output an error message
    echo "Cannot Connect to Database: " . mysqli_connect_error();
    exit();
}
?>