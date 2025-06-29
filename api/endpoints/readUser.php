<?php

// api/endpoints/users/read.php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../config/database.php';
include_once '../models/users.php';

// Instantiate the database
$database = new Database();
$db = $database->connect();

// Instantiate the Users object
$users = new User($db);

// Execute query
$result = $users->read();

if ($result && $result->rowCount() > 0) {
    $users_arr = [];
    $users_arr['data'] = [];
    
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        // Convert profile_image to base64 if it exists
        $profile_image_base64 = null;
        if ($profile_image) {
            $profile_image_base64 = base64_encode($profile_image);
        }
        
        $user_item = [
            'user_id'         => $user_id,
            'username'        => $username,
            'bio'             => $bio,
            'profile_image'   => $profile_image_base64,
            'created_at'      => $created_at,
            'url'             => $url
        ];
        
        $users_arr['data'][] = $user_item;
    }
    
    echo json_encode($users_arr, JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        'message' => 'No users found'
    ]);
}
?>