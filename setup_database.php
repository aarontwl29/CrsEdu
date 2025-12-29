<?php
/**
 * Database Setup Script
 * Run this file once to create the CrsEdu database and students table
 * Access: http://localhost/CrsEdu/setup_database.php
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

// Connect to MySQL server (without selecting a database)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Database Setup</h2>";

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS CrsEdu";
if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✓ Database 'CrsEdu' created successfully or already exists</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating database: " . $conn->error . "</p>";
}

// Select the database
$conn->select_db('CrsEdu');

// Create students table
$sql = "CREATE TABLE IF NOT EXISTS students (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    enrollment_date DATE DEFAULT CURRENT_DATE,
    major VARCHAR(100),
    status ENUM('Active', 'Inactive', 'Graduated') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✓ Table 'students' created successfully or already exists</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating table: " . $conn->error . "</p>";
}

// Insert sample data
$sql = "INSERT IGNORE INTO students (student_id, first_name, last_name, email, phone, date_of_birth, major, status) VALUES
('STU001', 'John', 'Doe', 'john.doe@example.com', '555-0101', '2000-05-15', 'Computer Science', 'Active'),
('STU002', 'Jane', 'Smith', 'jane.smith@example.com', '555-0102', '1999-08-22', 'Business Administration', 'Active'),
('STU003', 'Michael', 'Johnson', 'michael.j@example.com', '555-0103', '2001-03-10', 'Engineering', 'Active'),
('STU004', 'Emily', 'Brown', 'emily.brown@example.com', '555-0104', '2000-11-30', 'Mathematics', 'Active'),
('STU005', 'David', 'Wilson', 'david.w@example.com', '555-0105', '1998-07-18', 'Physics', 'Graduated')";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✓ Sample student data inserted successfully</p>";
} else {
    echo "<p style='color: red;'>✗ Error inserting sample data: " . $conn->error . "</p>";
}

echo "<hr>";
echo "<p><strong>Setup Complete!</strong></p>";
echo "<p>You can now use the database 'CrsEdu' with the 'students' table.</p>";
echo "<p><a href='index.php'>Go to Index Page</a></p>";

$conn->close();
?>
