<?php

class User {
    private $id;
    private $name;
    private $email;
    private $password;
    private $isAdmin;

    public function __construct($id, $name, $email, $password, $isAdmin) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->isAdmin = $isAdmin;
    }

    public static function getUserById($id) {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT * FROM accounts WHERE ID = ? AND is_admin = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new User($row['ID'], $row['Name'], $row['Email'], $row['Password'], $row['is_admin']);
        }
        return null;
    }

    public function updateDetails($name, $email) {
        $conn = connectDB();
        $stmt = $conn->prepare("UPDATE accounts SET Name = ?, Email = ? WHERE ID = ? AND is_admin = 0");
        $stmt->bind_param("ssi", $name, $email, $this->id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function updatePassword($newPassword) {
        $conn = connectDB();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE accounts SET Password = ? WHERE ID = ? AND is_admin = 0");
        $stmt->bind_param("si", $hashedPassword, $this->id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function deactivateUser() {
        $conn = connectDB();
        $stmt = $conn->prepare("UPDATE accounts SET activated = 0 WHERE ID = ? AND is_admin = 0");
        $stmt->bind_param("i", $this->id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public static function searchNonAdminUsers($search = '') {
        $conn = connectDB();
        $search = '%' . $search . '%';
        $stmt = $conn->prepare("SELECT ID, Name, Email FROM accounts WHERE is_admin = 0 AND activated = 1 AND (Name LIKE ? OR Email LIKE ?)");
        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getIsAdmin() { return $this->isAdmin; }

    public static function getDeactivatedUsers() {
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT ID, Name, Email FROM accounts WHERE is_admin = 0 AND activated = 0");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function reactivateUser() {
        $conn = connectDB();
        $stmt = $conn->prepare("UPDATE accounts SET activated = 1 WHERE ID = ? AND is_admin = 0");
        $stmt->bind_param("i", $this->id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }


}