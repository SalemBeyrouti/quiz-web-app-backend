<?php

require("../connect.php");
header("Content-Type: application/json");
session_start();

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email)  ||  empty($password)) {
    echo json_encode([
        "status" => "error",
        "message" => "email and password are required"
    ]);
    exit;
}

$query = "SELECT * FROM `users` WHERE `email` = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);


if ($row = mysqli_fetch_assoc($result)) {
    if (password_verify($password, $row["password"])) {
        $_SESSION["user_id"] = $row["id"];
        $_SESSION["user_email"] = $row["email"];
        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "user" => [
                "id" => $row["id"],
                "name" => $row["name"],
                "email" => $row["email"]
            ]
            ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Wrong password"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "user not found"
    ]);
}

?>