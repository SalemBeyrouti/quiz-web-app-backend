<?php

require("../connect.php");
header("Content-Type: application/json");
session_start();


if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "status" => "error",
        "message" => "Please login first"
    ]);
    exit;
}

$quiz_id = $_POST["quiz_id"] ?? '';
$user_id = $_SESSION["user_id"];
$user_email = $_SESSION["user_email"] ?? '';

if (empty($quiz_id)) {
    echo json_encode(
        [
            "status" => "error",
            "message" => "enter quiz id"
        ]
        );
        exit;
}

$query = "SELECT user_id FROM quizzes WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $quiz_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if(!$row) {
    echo json_encode(
        [
            "status" => "error",
            "message" => "quiz not found"
        ]
        );
        exit;
}

$quiz_creator_id = $row["user_id"];

if ($quiz_creator_id != $user_id  && $user_email != "admin@quiz.com") {
    echo json_encode(
        [
            "status" => "error",
            "message" => "you cant delete this quiz"
        ]
        );
        exit;
}

$delete_query = "DELETE FROM quizzes WHERE id = ?";
$delete_stmt = mysqli_prepare($conn, $delete_query);
mysqli_stmt_bind_param($delete_stmt, "i", $quiz_id);

if(mysqli_stmt_execute($delete_stmt)) {
    if(mysqli_stmt_affected_rows($delete_stmt) >0 ) {
        echo json_encode(
            [
                "status" => "success",
                "message" => "quiz is deleted"
            ]
            );
    } else {
        echo json_encode(
            [
                "status" => "error",
                "message" => "failed to delete quiz"
            ]
            );
    }
} else {
    echo json_encode(
        [
            "status" => "error",
            "message" => "data error"
        ]
        );
}