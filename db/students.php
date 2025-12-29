<?php
require_once __DIR__ . '/connection.php';

// Students table operations
class Students {
    private $conn;
    
    public function __construct() {
        $this->conn = getDB();
    }
    
    // Get all students
    public function getAll($orderBy = 'id', $order = 'ASC') {
        $orderBy = $this->conn->real_escape_string($orderBy);
        $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
        
        $sql = "SELECT * FROM students ORDER BY $orderBy $order";
        $result = $this->conn->query($sql);
        
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
    
    // Get student by ID
    public function getById($id) {
        $id = (int)$id;
        $sql = "SELECT * FROM students WHERE id = $id";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    // Get students by class
    public function getByClass($class) {
        $class = $this->conn->real_escape_string($class);
        $sql = "SELECT * FROM students WHERE class = '$class' ORDER BY name_chi ASC";
        $result = $this->conn->query($sql);
        
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
    
    // Get students by status
    public function getByStatus($status) {
        $status = $this->conn->real_escape_string($status);
        $sql = "SELECT * FROM students WHERE status = '$status' ORDER BY name_chi ASC";
        $result = $this->conn->query($sql);
        
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
    
    // Insert new student
    public function insert($data) {
        $name_chi = $this->conn->real_escape_string($data['name_chi']);
        $name_eng = $this->conn->real_escape_string($data['name_eng']);
        $nickname = isset($data['nickname']) ? "'" . $this->conn->real_escape_string($data['nickname']) . "'" : "NULL";
        $gender = $this->conn->real_escape_string($data['gender']);
        $class = isset($data['class']) ? "'" . $this->conn->real_escape_string($data['class']) . "'" : "NULL";
        $status = isset($data['status']) ? "'" . $this->conn->real_escape_string($data['status']) . "'" : "'Active'";
        $image_path = isset($data['image_path']) ? "'" . $this->conn->real_escape_string($data['image_path']) . "'" : "NULL";
        
        $sql = "INSERT INTO students (name_chi, name_eng, nickname, gender, class, status, image_path) 
                VALUES ('$name_chi', '$name_eng', $nickname, '$gender', $class, $status, $image_path)";
        
        if ($this->conn->query($sql)) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    // Update student
    public function update($id, $data) {
        $id = (int)$id;
        $updates = [];
        
        if (isset($data['name_chi'])) {
            $updates[] = "name_chi = '" . $this->conn->real_escape_string($data['name_chi']) . "'";
        }
        if (isset($data['name_eng'])) {
            $updates[] = "name_eng = '" . $this->conn->real_escape_string($data['name_eng']) . "'";
        }
        if (isset($data['nickname'])) {
            $updates[] = "nickname = '" . $this->conn->real_escape_string($data['nickname']) . "'";
        }
        if (isset($data['gender'])) {
            $updates[] = "gender = '" . $this->conn->real_escape_string($data['gender']) . "'";
        }
        if (isset($data['class'])) {
            $updates[] = "class = '" . $this->conn->real_escape_string($data['class']) . "'";
        }
        if (isset($data['status'])) {
            $updates[] = "status = '" . $this->conn->real_escape_string($data['status']) . "'";
        }
        if (isset($data['image_path'])) {
            $updates[] = "image_path = '" . $this->conn->real_escape_string($data['image_path']) . "'";
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql = "UPDATE students SET " . implode(', ', $updates) . " WHERE id = $id";
        return $this->conn->query($sql);
    }
    
    // Delete student
    public function delete($id) {
        $id = (int)$id;
        $sql = "DELETE FROM students WHERE id = $id";
        return $this->conn->query($sql);
    }
    
    // Search students
    public function search($keyword) {
        $keyword = $this->conn->real_escape_string($keyword);
        $sql = "SELECT * FROM students 
                WHERE name_chi LIKE '%$keyword%' 
                OR name_eng LIKE '%$keyword%' 
                OR nickname LIKE '%$keyword%'
                OR class LIKE '%$keyword%'
                ORDER BY name_chi ASC";
        $result = $this->conn->query($sql);
        
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}
?>
