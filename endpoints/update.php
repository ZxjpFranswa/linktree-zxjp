<?php
//api/endpoints/user_update.php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json;');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../config/database.php';
include_once '../models/Users.php';

//instantiate the database
$database = new Database();
$db = $database->connect();

// Instantiate the User object
$users = new User($db);

//get row posted data
$data = json_decode(file_get_contents("php://input") );

//set user properties 
if (is_object($data)) {
    $users->user_id = $data->user_id;
    $users->profile_image = isset($data->profile_image) ? base64_decode($data->profile_image) : null;
    $users->username = $data->username;
    $users->bio = $data->bio;
    $users->url = $data->url;

    // update user
    if($users->update()){
        echo json_encode(
            array('message' => 'User is Updated')
        );
    } else {
        echo json_encode(
            array('message' => 'User is not Updated')
        );
    }
} else {
    echo json_encode(
        array('message' => 'Invalid input')
    );
}
?>