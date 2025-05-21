<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user']) || $_SESSION["user"]["role"] !== "staff") {
    echo json_encode(["success" => "error", "message" => "Unauthorized access."]);
    header("Location: index.php");
    exit;
}

$jsonFile = "../data/modules.json";
$staff = $_SESSION['user']['username'];


// Check if data is received
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["modulename"])) {
    $modulenameText = trim($_POST["modulename"]);

    $newModule = [
        "code" => "module".uniqid(),
        "name" => strip_tags(htmlspecialchars($modulenameText)),
        "tutor" => $staff
    ];

    // Load existing modules
    $modules = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

    // Ensure $modules is an array before appending
    if (!is_array($modules)) {
        $modules = [];
    }

    // Append new question
    $modules[] = $newModule;    
    

    // Save back to file
    if (file_put_contents($jsonFile, json_encode($modules, JSON_PRETTY_PRINT))) {
        echo json_encode(["success" => "success", "message" => "Add successfully!"]);
    } else {
        echo json_encode(["success" => "error", "message" => "Error saving question."]);
    }
} else {
    echo json_encode(["success" => "error", "message" => "Invalid request."]);
}
?>
