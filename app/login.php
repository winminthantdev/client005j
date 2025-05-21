<?php
session_start();

$userFile = "../data/users.json"; 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";

    if (empty($username) || empty($password)) {
        echo json_encode(["success" => "error", "message" => "Username and password required"]);
        exit;
    }

    if (!file_exists($userFile)) {
        echo json_encode(["success" => "error", "message" => "User data not found"]);
        exit;
    }

    $users = json_decode(file_get_contents($userFile), true);
    
    foreach ($users as $user) {
        
        if ($user["username"] === $username && $user["password"] === $password) {
            $_SESSION["user"] = [
                "username" => $user["username"],
                "role" => $user["role"]
            ]; 
            echo json_encode(["success" => "success", "role" => $user["role"]]);
            exit;
        }
        
    }

    echo json_encode(["success" => "error", "message" => "Username or password incorrect"]);
    exit;
}
?>
