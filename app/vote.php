<?php
session_start();


$voteFile = "../data/vote.json";
$questionFile = "../data/questions.json";
$username = $_SESSION["user"]['username']; // Replace this with actual logged-in user ID

// Fetch user's vote for a specific question
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["questionId"])) {
    $questionId = $_GET["questionId"];
    $votes = file_exists($voteFile) ? json_decode(file_get_contents($voteFile), true) : [];

    foreach ($votes as $vote) {
        if ($vote["username"] === $username && $vote["questionId"] === $questionId) {
            echo json_encode(["voteStatus" => $vote["voteStatus"]]);
            exit;
        }
    }

    echo json_encode(["voteStatus" => 0]); // Default: No vote
    exit;
}

// Process vote submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["questionId"], $_POST["voteStatus"])) {
    $questionId = $_POST["questionId"];
    $newVoteStatus = (int)$_POST["voteStatus"]; // 1 = like, -1 = dislike, 0 = remove vote

    if (!in_array($newVoteStatus, [1, -1, 0])) {
        echo json_encode(["error" => "Invalid vote."]);
        exit;
    }

    $voteData = file_exists($voteFile) ? json_decode(file_get_contents($voteFile), true) : [];
    $previousVoteStatus = 0;
    $foundIndex = null;

    foreach ($voteData as $index => $vote) {
        if ($vote["username"] === $username && $vote["questionId"] === $questionId) {
            $previousVoteStatus = $vote["voteStatus"];
            $foundIndex = $index;
            break;
        }
    }

    $voteChange = 0;

    if ($newVoteStatus === 0) {
        if ($foundIndex !== null) {
            array_splice($voteData, $foundIndex, 1);
            $voteChange = -$previousVoteStatus; // Revert previous vote
        }
    } else {
        if ($foundIndex !== null) {
            if ($previousVoteStatus !== $newVoteStatus) {
                $voteData[$foundIndex]["voteStatus"] = $newVoteStatus;
                $voteChange = $newVoteStatus - $previousVoteStatus;
            }
        } else {
            $voteData[] = ["username" => $username, "questionId" => $questionId, "voteStatus" => $newVoteStatus];
            $voteChange = $newVoteStatus;
        }
    }

    file_put_contents($voteFile, json_encode($voteData, JSON_PRETTY_PRINT));

    if ($voteChange !== 0) {
        updateQuestionLikes($questionId, $voteChange);
    }

    echo json_encode(["success" => true, "newVoteStatus" => $newVoteStatus, "voteChange" => $voteChange]);
    exit;
}

exit;

/**
 * Function to update question likes count based on votes
 */
function updateQuestionLikes($questionId, $voteChange) {
    global $questionFile;

    if (!file_exists($questionFile)) {
        return;
    }

    $questions = json_decode(file_get_contents($questionFile), true);

    foreach ($questions as &$question) {
        if ($question["id"] == $questionId) {
            $question["likes"] += $voteChange;
            break;
        }
    }

    file_put_contents($questionFile, json_encode($questions, JSON_PRETTY_PRINT));
}
?>