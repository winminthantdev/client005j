<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user']) || $_SESSION["user"]["role"] !== "staff") {
    echo json_encode(["success" => "error", "message" => "Unauthorized access."]);
    exit;
}

$jsonFile = "../data/questions.json";

// Check if data is received
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["questionId"], $_POST["answer"])) {

// Check if file exists
if (!file_exists($jsonFile)) {
    echo json_encode(['status' => 'error', 'message' => 'Questions file not found']);
    exit;
}

// Load existing questions
$questions = json_decode(file_get_contents($jsonFile), true);

$questionId = $_POST["questionId"];
$answer = $_POST["answer"];
$staff = $_SESSION['user']['username'];

  
// Find the question by ID and update it
$updated = false;
foreach ($questions as &$question) {
    if ($question['id'] == $questionId) {
        // Add new answer
        $question['answers'][] = [
            'staff' => $staff,
            'answer' => $answer
        ];
        
        // Update status to answered
        $question['status'] = "answered";

        $updated = true;
        break;
    }
}

// Save the updated data back to JSON file
if ($updated) {
    if(file_put_contents($jsonFile, json_encode($questions, JSON_PRETTY_PRINT))){
        echo json_encode(['status' => 'success', 'message' => 'Answer submitted successfully']);
    }else {
        echo json_encode(["success" => "error", "message" => "Error saving answer."]);
    }
        
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Question not found']);
    }
}

?>