<?php
// Include the database connection
include('connection.php');

// Check if form data is submitted using POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data from the POST request
    $expense_name = mysqli_real_escape_string($con, $_POST['expense-name']);
    $expense_amount = floatval($_POST['expense-amount']);
    $expense_category = mysqli_real_escape_string($con, $_POST['expense-category']);

    // Retrieve the planned amount for the given category from the budget table
    $sql = "SELECT planned_budget FROM budget WHERE category = '$expense_category'";
    $result = mysqli_query($con, $sql);
    
    // Check if the category exists in the budget table
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $planned_amount = $row['planned_budget'];

        // Check if the entered amount exceeds the planned budget for the category
        if ($expense_amount > $planned_amount) {
            // Redirect with an error message if the budget is exceeded
            header("Location: daily_expense.html?error=budget_exceeded");
            exit();
        } else {
            // Insert the expense into the expenses table if within budget
            $insert_sql = "INSERT INTO expenses (expensename, amount, category) 
                           VALUES ('$expense_name', '$expense_amount', '$expense_category')";
            if (mysqli_query($con, $insert_sql)) {
                // Redirect with a success message
                header("Location: daily_expense.html?success=expense_added");
                exit();
            } else {
                // If insertion fails, redirect with a failure message
                header("Location: daily_expense.html?error=insert_failed");
                exit();
            }
        }
    } else {
        // If the category does not exist in the budget table, redirect with an error message
        header("Location: daily_expense.html?error=category_not_found");
        exit();
    }
} else {
    // If the request method is not POST, redirect with an error
    header("Location: daily_expense.html?error=invalid_request");
    exit();
}

// Close the database connection
mysqli_close($con);
?>