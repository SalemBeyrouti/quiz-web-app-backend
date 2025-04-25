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

$user_id = $_SESSION["user_id"];
$user_email = $_SESSION["user_email"];

if ($user_email === "admin@quiz.com") {

    $query = "SELECT quizzes.id, quizzes.title, quizzes.description, users.name AS author FROM quizzes
    JOIN users ON quizzes.user_id = users.id";
    $stmt = mysqli_prepare($conn, $query);
} else {

    $query = "SELECT id, title, description FROM `quizzes` WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);

}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$quizzes = [];

while ($row = mysqli_fetch_assoc($result)) {
    $quizzes[] = $row;

}
echo json_encode(
    [
        "status" => "success",
        "quizzes" => $quizzes
    ]
    );

?>