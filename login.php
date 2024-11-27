<?php
// Start the session
session_start();

// Assuming you have already connected to the database
require 'connection.php'; 

// Check if the login form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    // Query to check if the username exists in the database
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        // Fetch the user data
        $user = mysqli_fetch_assoc($result);

        // Check if the entered password matches the stored password
        if ($password === $user['password']) {
            // Password matches, set up session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to the dashboard using JavaScript
            echo "<script>
                    alert('Login successful!');
                    window.location.href = 'home.html';  // Change this to your desired page
                  </script>";
            exit(); // Prevent further code execution after redirect
        } else {
            // Invalid password, show an alert and redirect back to login page
            echo "<script>
                    alert('Invalid username or password.');
                    window.location.href = 'index.html';  // Redirect to login page
                  </script>";
            exit(); // Prevent further code execution after redirect
        }
    } else {
        // Username not found, show an alert and redirect back to login page
        echo "<script>
                alert('Invalid username or password.');
                window.location.href = 'index.html';  // Redirect to login page
              </script>";
        exit(); // Prevent further code execution after redirect
    }
}
?>
