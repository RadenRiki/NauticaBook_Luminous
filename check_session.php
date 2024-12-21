<?php
session_start();
header('Content-Type: application/json');

$response = [
    'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
    'name' => isset($_SESSION['name']) ? $_SESSION['name'] : null
];

echo json_encode($response);
?>