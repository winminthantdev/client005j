<?php

// Function to get data from a JSON file
function getJsonData($file_path) {
    // Check if the file exists
    if (!file_exists($file_path)) {
        return json_encode(["error" => "File not found"]);
    }

    // Read and decode the JSON file
    $json_data = file_get_contents($file_path);
    $data = json_decode($json_data, true);

    // If JSON decoding fails
    if ($data === null) {
        return json_encode(["error" => "Invalid JSON data"]);
    }

    // Return the data as JSON
    return json_encode($data);
}

?>
