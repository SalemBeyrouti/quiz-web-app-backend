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
$role = $_SESSION["role"]  ?? '';

$quiz_id = $_POST["quiz_id"] ?? '';
$question_id = $_POST["question_id"] ?? '';
$question_text = $_POST["question_text"] ?? '';
$option_a = $_POST["option_a"] ?? '';
$option_b = $_POST["option_b"] ?? '';
$option_c = $_POST["option_c"] ?? '';
$option_d = $_POST["option_d"] ?? '';
$correct_answer = $_POST["correct_answer"] ?? '';

if (empty(empty($question_id) ||  $question_text) || empty($option_a) || empty($option_b) || empty($option_c) || empty($option_d) || empty($correct_answer)) {
    echo json_encode([
        "status" => "error",
        "message" => "all fields are required"
    ]);
    exit;
}

if ($role === 'admin') {
    $update_query = "UPDATE questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ? WHERE id = ?";

    $update_stmt = mysqli_prepare($conn, $update_query);
    if (!$update_stmt) {
        echo json_encode([
            "status" => "error",
            "message" => "failed"
        ]);
        exit;
    }
    mysqli_stmt_bind_param($update_stmt, "ssssssi", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $question_id);

} else {

    if (empty($quiz_id)) {
        echo json_encode([
            "status" => "error",
            "message" => "Quiz ID is required"
        ]);
        exit;
    }

    $check_owner_query = "SELECT * FROM quizzes WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $check_owner_query);

    if(!$stmt) {
        echo json_encode(
            [
                "status" => "error",
                "message" => "you dont own it"
            ]
            );
            exit;
    }
    mysqli_stmt_bind_param($stmt, "ii", $quiz_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0) {
        echo json_encode([
            "status" => "error",
            "message" => "You don't own this quiz"
        ]);
        exit;
    }

    $update_query = "UPDATE questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ? WHERE id = ? AND quiz_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);

    if (!$update_stmt) {
        echo json_encode([
            "status" => "error",
            "message" => "failed"
        ]);
        exit;
    }
    mysqli_stmt_bind_param($update_stmt, "ssssssii", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $question_id, $quiz_id);
}


mysqli_stmt_execute($update_stmt);

if (mysqli_stmt_affected_rows($update_stmt) > 0) {
    echo json_encode(
        [
        "status" => "success",
        "message" => "question updated successfully"
    ]);
} else {
    echo json_encode(
        [
        "status" => "error",
        "message" => "failed to edit question"
    ]);
}

?>