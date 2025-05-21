<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user']) || $_SESSION["user"]["role"] !== "student") {
    echo json_encode(["success" => "error", "message" => "Unauthorized access."]);
    header("Location: staffindex.php");
    exit;
}

$user = $_SESSION['user']['username'];
$jsonFile = "../data/questions.json";
$bannedWordsFile = "../data/banned_words.txt"; 

// Check if data is received
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["question"], $_POST["moduleCode"])) {
    $questionText = trim($_POST["question"]);

    // Read banned words from file
    $bannedWords = file_exists($bannedWordsFile) ? file($bannedWordsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

    // Convert question text to lowercase for case-insensitive comparison
    $lowercaseQuestion = strtolower($questionText);

    // Check for banned words
    foreach ($bannedWords as $word) {
        if (strpos($lowercaseQuestion, strtolower($word)) !== false) {
            echo json_encode(["success" => "error", "message" => "Your question contains banned words."]);
            exit;
        }
    }


    // Load existing questions
    $questions = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

    // Ensure $questions is an array before appending
    if (!is_array($questions)) {
        $questions = [];
    }

    $isEdit = !empty($_POST['questionId']);
    $responseMessage = "";

    if($isEdit){
        // Edit existing question
        $edited = false;
        foreach ($questions as &$q) {
            if ($q["id"] === $_POST["questionId"] && $q["moduleCode"] === $_POST["moduleCode"] && $q["username"] === $user) {
                $q["question"] = strip_tags(htmlspecialchars($questionText));
                $q["editStatus"] = "Edited";
                $responseMessage = "Question updated successfully!";
                $edited = true;
                break;
            }
        }

        if(!$edited){
            echo json_encode(["success" => "error", "message" => "Question not found or unauthorized edit."]);
            exit;
        }
        
    }else{
        $newQuestion = [
            "id" => "question".uniqid(),
            "moduleCode" => $_POST["moduleCode"], 
            "username" => $user, 
            "question" => strip_tags(htmlspecialchars($questionText)), // Extra security
            "likes" => 0,
            "answers"=> []
        ];
            // Append new question
            $questions[] = $newQuestion;
            $responseMessage = "Question submitted successfully!";
    }

    // Save back to file
    if (file_put_contents($jsonFile, json_encode($questions, JSON_PRETTY_PRINT))) {
        echo json_encode(["success" => "success", "message" => $responseMessage]);
    } else {
        echo json_encode(["success" => "error", "message" => "Error saving question."]);
    }
} else {
    echo json_encode(["success" => "error", "message" => "Invalid request."]);
}
?>
