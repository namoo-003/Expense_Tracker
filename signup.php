<?php
// Include the connection file to establish the database connection
require 'connection.php'; 

// Check if the sign-up form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the username and password from POST data
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($con, $_POST['confirmPassword']);

    // Check if the password and confirm password match
    if ($password !== $confirm_password) {
        echo "<script>
                    alert('Passwords do not match');
                    window.location.href = 'index.html';  // Change this to your desired page
                  </script>";
    } else {
        // Check if the username already exists in the database
        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            // Username already exists
            echo "<script>
                    alert('Username is already taken. Please choose a different one.');
                    window.location.href = 'index.html';  // Change this to your desired page
                  </script>";
        } else {
            // Insert the new user into the database with plain-text password
            $insert_query = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
            if (mysqli_query($con, $insert_query)) {
                // Successful sign-up
                echo "<script>
                    alert('Registration successful! You can now log in.');
                    window.location.href = 'index.html';  // Change this to your desired page
                  </script>";
            } else {
                // Error while inserting into the database
                echo "<script>
                    alert('Error registering user. Please try again.');
                    window.location.href = 'index.html';  // Change this to your desired page
                  </script>";
            }
        }
    }
}
?>