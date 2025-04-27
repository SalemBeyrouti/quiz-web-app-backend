<?php

require("../connect.php");
header("Content-Type: application/json");
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(
        [
            "status" => "error",
            "message" => "Invalid request method. Only POST is allowed."
        ]
    );
    exit;
}


if (!isset($_SESSION["user_id"])) {
    echo json_encode([ 
        "status" => "error",
        "message" => "Please login first"
    ]);
    exit;
}


$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"] ?? '';

$quiz_id = $_POST["quiz_id"] ?? '';
$question_id = $_POST["question_id"] ?? '';

if (empty($question_id)) {
    echo json_encode(
        [
            "status" => "error",
            "message" => "question ID is required"
        ]
        );
        exit;
}

if ($role === 'admin') {

    $delete_query = "DELETE FROM question WHERE id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_query);

    if (!$delete_stmt){
        echo json_encode(
            [
                "status" => "error",
                "message" => "failed to prepare query"
            ]
            );
            exit;
    }

    mysqli_stmt_bind_param($delete_stmt, "i", $question_id);
    mysqli_stmt_execute($delete_stmt);

    if (mysqli_stmt_affected_rows($delete_stmt) > 0) {
        echo json_encode(
            [
                "status" => "success",
                "message" => "question deleted"
            ]
            );
    } else {
        echo json_encode(
            [
                "status" => "error",
                "message" => "failed to delete question"
            ]
            );
    }
} else {
    if (empty($quiz_id)) {
        echo json_encode(
            [
                "status" => "error",
                "message" => "quiz ID required"
            ]
            );
            exit;
    }

    $check_owner_query = "SELECT * FROM quizzes WHERE id =? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $check_owner_query);

    if(!$stmt) {
        echo json_encode(
            [
                "status" => "error",
                "message" => "failed"
            ]
            );
            exit;
    }

    mysqli_stmt_bind_param($stmt, "ii", $quiz_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        echo json_encode(
            [
                "status" => "error",
                "message" => "you dont own this quiz"
            ]
            );
            exit;
    } 

    $delete_query = "DELETE FROM questions WHERE id = ? AND quiz_id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_query);

    if (!$delete_stmt) {
        echo json_encode(
            [
                "status" => "error",
                "message" => "failed"
            ]
            );
            exit;
    } 

    mysqli_stmt_bind_param($delete_stmt, "ii", $question_id, $quiz_id);
    mysqli_stmt_execute($delete_stmt);

    if(mysqli_stmt_affected_rows($delete_stmt) > 0) {
        echo json_encode(
            [
                "status" => "success",
                "message" => "question deleted successfully"
            ]
            );
    } else {
        echo json_encode(
            [
                "status" => "error",
                "message" => "failed to delete question"
            ]
            );
    }
}

?>