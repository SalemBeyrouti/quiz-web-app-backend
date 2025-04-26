<?php

require("../connect.php");
header("Content-Type: application/json");
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(
        [
            "status" => "error",
            "message" => "cant do that"
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

error_log("USER ROLE: " .$role);



$quiz_id = $_POST["quiz_id"] ?? '';

if(empty($quiz_id)) {
    echo json_encode(
        [
            "status" => "error",
            "message" => "Enter the quiz ID"
        ]
        );
        exit;

}


if($role !== 'admin') {
    $check_owner_query = "SELECT * FROM quizzes WHERE id = ? AND user_id = ?";

    $stmt = mysqli_prepare($conn, $check_owner_query);
    mysqli_stmt_bind_param($stmt, "ii", $quiz_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 0){
        echo json_encode( 
            [
                "status" => "erro",
                "message" => "you dont own this quiz"
            ]
            );
            exit;
    }
}

$query = "SELECT id, question_text, option_a, option_b, option_c, option_d, question_order FROM questions WHERE quiz_id = ? ORDER BY question_order ASC";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    echo json_encode(
        [
            "status" => "error",
            "message" => "cant do that also"
        ]
        );
        exit;
}

mysqli_stmt_bind_param($stmt, "i", $quiz_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$questions = [];

while ($row = mysqli_fetch_assoc($result)) {
    $questions [] = $row;

}

echo json_encode(
    [
        "status" => "success",
        "questions" => $questions
    ]
    );

?>