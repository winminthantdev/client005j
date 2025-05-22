<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION["user"]["role"] !== "staff") {
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit;
}

$jsonFile = "../data/questions.json";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["questionId"], $_POST["answer"])) {

    $questionId = $_POST["questionId"];
    $answerText = $_POST["answer"];
    $staff = $_SESSION['user']['username'];

    if (!file_exists($jsonFile)) {
        echo json_encode(['success' => false, 'message' => 'Questions file not found']);
        exit;
    }

    $questions = json_decode(file_get_contents($jsonFile), true);
    $updated = false;

    foreach ($questions as &$question) {
        if ($question['id'] == $questionId) {
            $question['answers'][] = [
                'id' => 'ans_' . uniqid(),
                'staff' => $staff,
                'answer' => $answerText,
                'editStatus' => ''
            ];
            $question['status'] = "answered";
            $updated = true;
            break;
        }
    }

    if ($updated) {
        if (file_put_contents($jsonFile, json_encode($questions, JSON_PRETTY_PRINT))) {
            echo json_encode([
                'success' => true,
                'message' => 'Answer submitted successfully'
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Error saving data."]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Question not found']);
    }

} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editanswer"], $_POST["answerId"])) {

    $editAnswerText = $_POST["editanswer"];
    $editAnswerId = $_POST["answerId"];
    $staff = $_SESSION['user']['username']; 

    if (!file_exists($jsonFile)) {
        echo json_encode(['success' => false, 'message' => 'Questions file not found']);
        exit;
    }

    $questions = json_decode(file_get_contents($jsonFile), true);
    $updated = false;

    foreach ($questions as &$question) {
        foreach ($question['answers'] as &$answer) {
            if ($answer['id'] === $editAnswerId && $answer['staff'] === $staff) {
                $answer['answer'] = $editAnswerText;
                $answer['editStatus'] = "Edited";
                $updated = true;
                break 2;
            }
        }
    }

    if ($updated) {
        if (file_put_contents($jsonFile, json_encode($questions, JSON_PRETTY_PRINT))) {
            echo json_encode([
                'success' => true,
                'message' => 'Answer edited successfully'
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Error saving data."]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Question or Answer not found']);
    }
}
?>
