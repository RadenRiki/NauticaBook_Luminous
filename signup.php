<?php
session_start(); // Start the session

// Database connection
$conn = new mysqli("localhost", "root", "root", "luminousdb");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $referral = "REF" . strval(rand(10000000, 99999999)); // Generate random referral code

    // Check if email already exists
    $checkEmailQuery = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered. Please use another email.'); window.location.href='signup.html';</script>";
    } else {
        // Insert user into database
        $insertQuery = "INSERT INTO users (name, email, password, referral) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssss", $name, $email, $password, $referral);

        if ($stmt->execute()) {
            // Set session data
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['name'] = $name;
        
            // Set user data ke sessionStorage via JavaScript
            echo "
            <script>
                sessionStorage.setItem('user', JSON.stringify({
                    id: '{$stmt->insert_id}',
                    name: '{$name}'
                }));
                window.location.href = 'profile.html';
            </script>
            ";
        } else {
            echo "<script>alert('Signup failed. Please try again.'); window.location.href='signup.html';</script>";
        }        
    }
}
?>
