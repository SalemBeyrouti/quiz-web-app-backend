<?php

require("../connect.php");
header("Content-Type: application/json");
session_start();

if (!isset($_SESSION["user_id"])){
    echo json_encode(
        [
            "status" => "error",
            "message" => "Please login first"
        ]
        );
        exit;
}

$title = $_POST["title"] ?? '';
$description = $_POST["description"] ?? '';
$user_id = $_SESSION["user_id"];

if (empty($title) || empty($description)) {
    echo json_encode(
        [
            "status" => "error",
            "message" => "title and description required"
        ]
        );
        exit;
}

$query = "INSERT INTO `quizzes` (`title`, `description`, `user_id`) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssi", $title, $description, $user_id);

if (mysqli_stmt_execute($stmt)) {
    $quiz_id = mysqli_insert_id($conn);
    echo json_encode(
        [
            "status" => "success",
            "message" => "quiz created successfully",
            "quiz_id" => $quiz_id
        ]
        );
} else {
    echo json_encode(
        [
            "status" => "error",
            "message" => "failed to create quiz"
        ]
        );
}



?>