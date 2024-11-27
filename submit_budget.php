<?php
// Include the connection file
require 'connection.php';  // Make sure the path to connection.php is correct

// Check if the form data is set
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize the input data
    $foodBudget = !empty($_POST['food']) ? mysqli_real_escape_string($con, $_POST['food']) : null;
    $stationeryBudget = !empty($_POST['stationery']) ? mysqli_real_escape_string($con, $_POST['stationery']) : null;
    $housingBudget = !empty($_POST['housing']) ? mysqli_real_escape_string($con, $_POST['housing']) : null;
    $miscellaneousBudget = !empty($_POST['miscellaneous']) ? mysqli_real_escape_string($con, $_POST['miscellaneous']) : null;

    // Set the current timestamp
    $currentDate = date('Y-m-d H:i:s');

    // Prepare the insert query based on available budget values
    $sql = "INSERT INTO budget (category, planned_budget, date_recorded) VALUES ";

    $values = [];

    if ($foodBudget !== null) {
        $values[] = "('Food', '$foodBudget', '$currentDate')";
    }
    if ($stationeryBudget !== null) {
        $values[] = "('Stationery', '$stationeryBudget', '$currentDate')";
    }
    if ($housingBudget !== null) {
        $values[] = "('Housing', '$housingBudget', '$currentDate')";
    }
    if ($miscellaneousBudget !== null) {
        $values[] = "('Miscellaneous', '$miscellaneousBudget', '$currentDate')";
    }

    // Only execute the query if there is at least one valid entry
    if (!empty($values)) {
        $sql .= implode(", ", $values);

        if ($con->query($sql) === TRUE) {
            echo "<script>
                    alert('Budget Saved Successfully!');
                    window.location.href = 'home.html';  // Change this to your desired page
                  </script>";
        } else {
            echo "<script>
                    alert('Error!');
                    window.location.href = 'budget.html';  // Change this to your desired page
                  </script>";
            echo "Error: " . $sql . "<br>" . $con->error;
        }
    } else {
        echo "<script>
                    alert('Please enter a valid budget for atleast one category!');
                    window.location.href = 'budget.html';  // Change this to your desired page
                  </script>";
    }
}
?>