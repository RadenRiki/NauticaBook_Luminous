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
        $_SESSION['email'] = $user['email'];
        $_SESSION['referral'] = $user['referral'];
    
        // Redirect to profile.php
        echo "
        <script>
            sessionStorage.setItem('user', JSON.stringify({
                id: '{$user['id']}',
                name: '{$user['name']}',
                email: '{$user['email']}',
                referral: '{$user['referral']}'
            }));
            window.location.href = 'profile.php';
        </script>
        ";
    } else {
        echo "<script>alert('Invalid email or password. Please try again.'); window.location.href='login.html';</script>";
    }    
}
?>
