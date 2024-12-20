<?php
session_start(); // Start the session

// Database connection
$conn = mysqli_connect("localhost", "root", "", "luminousdb");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check credentials
    $loginQuery = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($loginQuery);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Set session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];

        // Redirect to home.html
        header("Location: home.html");
    } else {
        echo "<script>alert('Invalid email or password. Please try again.'); window.location.href='login.html';</script>";
    }
}
?>
