<?php

require("../connect.php");

$name = "Admin";
$email = "admin@quiz.com";
$password = "admin123";
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = "admin";


$query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashed_password, $role);

if (mysqli_stmt_execute($stmt)) {
    echo "admin user created";

} else {
    echo "error failed to create admin";
}

?>