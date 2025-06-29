<?php
// Author: Joseph Francois S. Payago
// Date: 2025-06-29
// Description: User class for handling user data in the database

class User {
    private $conn;
    private $table = "users";

    // Public properties
    public $user_id;
    public $profile_image;
    public $username;
    public $bio;
    public $url;
    public $created_at;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all users
    public function read() {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        return ($stmt->execute()) ? $stmt : null;
    }

    // Read a single user by ID
    public function read_single() {
        $query = "SELECT * FROM {$this->table} WHERE user_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->user_id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->user_id        = $row['user_id'];
            $this->profile_image  = $row['profile_image'];
            $this->username       = $row['username'];
            $this->bio            = $row['bio'];
            $this->url            = $row['url'];
            $this->created_at     = $row['created_at'];
            return true;
        }

        return false;
    }

    // Create a new user
    public function create() {
        $query = "INSERT INTO {$this->table} SET
            profile_image = :profile_image,
            username = :username,
            bio = :bio,
            url = :url";

        $stmt = $this->conn->prepare($query);
        $this->sanitizeData();
        $this->bindUserParams($stmt);

        if ($stmt->execute()) {
            // Get the auto-generated user_id
            $this->user_id = $this->conn->lastInsertId();
            return true;
        }

        printf("Create Error: %s\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Update existing user
    public function update() {
        $query = "UPDATE {$this->table} SET
            profile_image = :profile_image,
            username = :username,
            bio = :bio,
            url = :url
            WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $this->sanitizeData();
        $this->bindUserParams($stmt, true);

        if ($stmt->execute()) {
            return true;
        }

        printf("Update Error: %s\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Delete a user
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        }

        printf("Delete Error: %s\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Read user by username
    public function read_by_username() {
        $query = "SELECT * FROM {$this->table} WHERE username = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->username, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->user_id        = $row['user_id'];
            $this->profile_image  = $row['profile_image'];
            $this->username       = $row['username'];
            $this->bio            = $row['bio'];
            $this->url            = $row['url'];
            $this->created_at     = $row['created_at'];
            return true;
        }

        return false;
    }

    // Check if username exists
    public function username_exists() {
        $query = "SELECT user_id FROM {$this->table} WHERE username = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->username, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Sanitize all input data
    private function sanitizeData() {
        $this->username       = htmlspecialchars(strip_tags($this->username));
        $this->bio            = htmlspecialchars(strip_tags($this->bio));
        $this->url            = htmlspecialchars(strip_tags($this->url));
        // Note: profile_image (LONGBLOB) is not sanitized as it's binary data
    }

    // Bind parameters to prepared statement
    private function bindUserParams($stmt, $include_id = false) {
        if ($include_id) {
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
        }
        $stmt->bindParam(':profile_image', $this->profile_image, PDO::PARAM_LOB);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':bio', $this->bio);
        $stmt->bindParam(':url', $this->url);
    }
}
?>