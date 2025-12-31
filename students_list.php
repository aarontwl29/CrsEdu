<?php
// Include students class
require_once 'db/students.php';

// Create Students instance
$studentsObj = new Students();

// Fetch all students
$students = $studentsObj->getAll('id', 'ASC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrsEdu - Student List</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #666;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            transition: background-color 0.3s;
        }
        .back-btn:hover {
            background-color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #4CAF50;
            color: white;
            padding: 12px;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status.active {
            background-color: #4CAF50;
            color: white;
        }
        .status.inactive {
            background-color: #ff9800;
            color: white;
        }
        .status.graduated {
            background-color: #2196F3;
            color: white;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .student-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ddd;
        }
        .no-img {
            width: 50px;
            height: 50px;
            background-color: #e0e0e0;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 12px;
        }
        .student-name-link {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .student-name-link:hover {
            color: #4CAF50;
            text-decoration: underline;
        }
        .view-btn {
            padding: 6px 12px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            transition: background-color 0.3s;
            display: inline-block;
        }
        .view-btn:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">← Back to Home</a>
        <h1>Student List</h1>
        
        <?php if (!empty($students)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Photo</th>
                        <th>Chinese Name</th>
                        <th>English Name</th>
                        <th>Nickname</th>
                        <th>Gender</th>
                        <th>Class</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($students as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td>
                                <?php if (!empty($row['image_path']) && file_exists($row['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['name_chi']); ?>" class="student-img">
                                <?php else: ?>
                                    <div class="no-img">No Photo</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="student_details.php?id=<?php echo $row['id']; ?>" class="student-name-link">
                                    <?php echo htmlspecialchars($row['name_chi']); ?>
                                </a>
                            </td>
                            <td>
                                <a href="student_details.php?id=<?php echo $row['id']; ?>" class="student-name-link">
                                    <?php echo htmlspecialchars($row['name_eng']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($row['nickname'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td><?php echo htmlspecialchars($row['class'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="status <?php echo strtolower($row['status']); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="student_details.php?id=<?php echo $row['id']; ?>" class="view-btn">View Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">
                <p>No students found in the database.</p>
                <p>Please run the database setup first to create the database and add sample data.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
