<?php

require("../connect.php");

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($name)  ||  empty($email)  ||  empty($password)) {
    echo json_encode(
        [
            "status" => "error",
            "message" => "All fields are required"
            
        ]
    );
    exit;

}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$query = "INSERT INTO `users`(`name`, `email`, `password`) VALUES(?,?,?)";
$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(
        [
            "status" => "success",
            "message" => "User registered successfully"
        ]
        );
} else {
    echo json_encode(
        [
            "status" => "error",
            "message" => "Error occured"
        ]
        );
}






?>