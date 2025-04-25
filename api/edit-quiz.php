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

$quiz_id = $_POST["quiz_id"] ?? '';
$title = $_POST ["title"] ?? '';
$description = $_POST["description"] ?? '';
$user_id = $_SESSION["user_id"];

if(empty($quiz_id)  ||  empty($title) ||  empty($description)) {
    echo json_encode(
        [
            "status" => "error",
            "message" => "all fields are required"
        ]
        );
        exit;
}

$query = "UPDATE `quizzes` SET `title` = ?, `description` = ? WHERE `id` = ? And `user_id` = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssii", $title, $description, $quiz_id, $user_id,);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
     echo json_encode(
        [
            "status" => "success",
            "message" => "quiz updated"
        ]
        );
    } else {
        echo json_encode(
            [
                "status" => "error",
                "message" => "no changes detected"

            ]);
    }
        
    }   else {
        echo json_encode(
         [
            "status" => "error",
            "message" => "failed to update"
        ]
        );
}


?>