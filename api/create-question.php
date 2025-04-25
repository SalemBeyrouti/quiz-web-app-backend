<?php

require("../connect.php");
header("Content-Type: application/json");
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    echo json_encode([
        "status" => "error",
        "message" => "Please login first"
    ]);
    exit;
}

$quiz_id = $_POST["quiz_id"] ?? '';
$question_text = $_POST["question_text"] ?? '';
$option_a = $_POST["option_a"] ?? '';
$option_b = $_POST["option_b"] ?? '';
$option_c = $_POST["option_c"] ?? '';
$option_d = $_POST["option_d"] ?? '';
$correct_answer = $_POST["correct_answer"] ?? '';
$question_order = $_POST["question_order"] ?? 1;


if(empty($quiz_id)  || empty($question_text) || empty($option_a)  || empty($option_b) || empty($option_c) || empty($option_d)  || empty($correct_answer)) {
    echo json_encode(
        [
            "status" => "error",
            "message" => "insert all fields"
        ]
        );
        exit;
}


$valid_answers = [ 'a', 'b', 'c', 'd'];
if (!in_array($correct_answer, $valid_answers)) {
    echo json_encode(
        [
            "status" => "error",
            "message" => "correct answer not inserted"
        ]
        );
        exit;

}

$user_id = $_SESSION["user_id"];
$user_email = $_SESSION["user_email"] ?? '';

$check_query = "SELECT user_id FROM quizzes WHERE id = ?";
$stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt, "i", $quiz_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo json_encode(
        [
            "status" => "error",
            "message" => "quiz not found"
        ]
        );
        exit;
}

$quiz_creator_id = $row["user_id"];

if ($quiz_creator_id != $user_id && $user_email != "admin@quiz.com") {
    echo json_encode(
        [
            "status" => "error",
            "message" => "you cant edit this quiz"
        ]
        );
        exit;
 }

$insert_query = "INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d,correct_answer, question_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $insert_query);
mysqli_stmt_bind_param($stmt, "issssssi", $quiz_id,$question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $question_order);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(
        [
            "status" => "success",
            "message" => "question created successfully"
        ]
        );
} else {
    echo json_encode(
        [
            "status" => "error",
            "message" => "failed to create question"
        ]
        );
}

?>