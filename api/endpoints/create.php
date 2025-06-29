<?php
// api/endpoints/user_create.php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json;');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../config/database.php';
include_once '../models/Users.php';

// Instantiate the database
$database = new Database();
$db = $database->connect();

// Instantiate the User object
$users = new User($db);

// Get row posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (empty($data->username)) {
    echo json_encode(
        array('message' => 'Username is required')
    );
    exit;
}

// Check if username already exists
$users->username = $data->username;
if ($users->username_exists()) {
    echo json_encode(
        array('message' => 'Username already exists')
    );
    exit;
}

// Set user properties
$users->username = $data->username;
$users->bio = isset($data->bio) ? $data->bio : null;
$users->url = isset($data->url) ? $data->url : null;

// Handle profile image (base64 encoded)
if (isset($data->profile_image) && !empty($data->profile_image)) {
    // Decode base64 image
    $users->profile_image = base64_decode($data->profile_image);
} else {
    $users->profile_image = null;
}

// Create user
if ($users->create()) {
    // Convert to JSON and output
    echo json_encode(
        array(
            'message' => 'User created successfully',
            'user_id' => $users->user_id,
            'username' => $users->username
        )
    );
} else {
    echo json_encode(
        array('message' => 'User not created')
    );
}
?>