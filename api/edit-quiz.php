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
$user_email = $_SESSION["user_email"];

if(empty($quiz_id)  ||  empty($title) ||  empty($description)) {
    echo json_encode(
        [
            "status" => "error",
            "message" => "all fields are required"
        ]
        );
        exit;
}

$query = "SELECT user_id FROM quizzes WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $quiz_id);
mysqli_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo json_encode(
        [
            "status" => "error",
            "message" => "quiz not found"
        ]);
        exit;
}

$quiz_creator_id = $row["user_id"];

if($quiz_creator_id != $user_id && $user_email != "admin@quiz.com"){
    echo json_encode(
        [
            "status" => "error",
            "message" => "you are not allowed to edit"
        ]
        );
        exit;
}

$update_query = "UPDATE `quizzes` SET `title` = ?, `description` = ? WHERE `id` = ?";
$update_stmt = mysqli_prepare($conn, $update_query);
mysqli_stmt_bind_param($update_stmt, "ssi", $title, $description, $quiz_id);

if (mysqli_stmt_execute($update_stmt)) {
    if (mysqli_stmt_affected_rows($update_stmt) > 0) {
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