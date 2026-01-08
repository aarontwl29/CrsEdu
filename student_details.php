<?php
// Include required files
require_once 'db/students.php';
require_once 'db/connection.php';

// Get student ID from URL parameter
$studentId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($studentId <= 0) {
    header('Location: students_list.php');
    exit;
}

// Fetch student information
$studentsObj = new Students();
$student = $studentsObj->getById($studentId);

if (!$student) {
    header('Location: students_list.php');
    exit;
}

// Fetch daily reports for this student
$conn = getDB();
$reportsStmt = $conn->prepare("SELECT id, report_date, content FROM daily_reports WHERE student_id = ? ORDER BY report_date DESC");
$reportsStmt->bind_param("i", $studentId);
$reportsStmt->execute();
$reportsResult = $reportsStmt->get_result();
$dailyReports = [];
while ($row = $reportsResult->fetch_assoc()) {
    $dailyReports[] = $row;
}
$reportsStmt->close();

// Define which sections to show
$sections = [
    'basic_info' => true,
    'daily_reports' => true,
    'absences' => true,
    // Add more sections here as needed
    // 'grades' => true,
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details - <?php echo htmlspecialchars($student['name_chi']); ?></title>
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
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #666;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .back-btn:hover {
            background-color: #555;
        }
        .student-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 30px;
        }
        .student-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #4CAF50;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .no-photo {
            width: 150px;
            height: 150px;
            background-color: #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 14px;
            border: 4px solid #4CAF50;
        }
        .student-info-header {
            flex: 1;
        }
        .student-name {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }
        .student-name-eng {
            font-size: 20px;
            color: #666;
            margin-bottom: 15px;
        }
        .info-badge {
            display: inline-block;
            padding: 6px 12px;
            margin-right: 10px;
            margin-bottom: 8px;
            background-color: #f0f0f0;
            border-radius: 5px;
            font-size: 14px;
        }
        .info-badge strong {
            color: #333;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
        }
        .status-badge.active {
            background-color: #4CAF50;
            color: white;
        }
        .status-badge.inactive {
            background-color: #ff9800;
            color: white;
        }
        .status-badge.graduated {
            background-color: #2196F3;
            color: white;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 22px;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4CAF50;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .info-item {
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
        }
        .info-label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            color: #333;
            font-weight: bold;
        }
        .report-card {
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }
        .report-date {
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .report-content {
            color: #555;
            line-height: 1.8;
            font-size: 15px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: pre-wrap;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
        .section-divider {
            height: 2px;
            background: linear-gradient(to right, transparent, #e0e0e0, transparent);
            margin: 40px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="students_list.php" class="back-btn">← Back to Student List</a>
        </div>

        <!-- Student Header Section -->
        <div class="student-header">
            <?php if (!empty($student['image_path']) && file_exists($student['image_path'])): ?>
                <img src="<?php echo htmlspecialchars($student['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($student['name_chi']); ?>" 
                     class="student-photo">
            <?php else: ?>
                <div class="no-photo">No Photo</div>
            <?php endif; ?>
            
            <div class="student-info-header">
                <h1 class="student-name"><?php echo htmlspecialchars($student['name_chi']); ?></h1>
                <p class="student-name-eng"><?php echo htmlspecialchars($student['name_eng']); ?></p>
                <div>
                    <?php if (!empty($student['nickname']) && $student['nickname'] !== 'NULL'): ?>
                        <span class="info-badge"><strong>Nickname:</strong> <?php echo htmlspecialchars($student['nickname']); ?></span>
                    <?php endif; ?>
                    <span class="info-badge"><strong>Gender:</strong> <?php echo htmlspecialchars($student['gender']); ?></span>
                    <span class="info-badge"><strong>Class:</strong> <?php echo htmlspecialchars($student['class'] ?? 'N/A'); ?></span>
                    <span class="status-badge <?php echo strtolower($student['status']); ?>">
                        <?php echo htmlspecialchars($student['status']); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        <?php
        // Include section: Basic Information
        if ($sections['basic_info']):
            include 'sections/student_basic_info.php';
        endif;

        // Include section: Daily Reports
        if ($sections['daily_reports']):
            include 'sections/student_daily_reports.php';
        endif;

        // Include section: Absence Calendar
        if ($sections['absences']):
            echo '<div id="absences">';
            $student_id = $studentId; // Make student_id available for the section
            include 'sections/student_absences.php';
            echo '</div>';
        endif;

        // Add more sections as needed
        // if ($sections['grades']):
        //     include 'sections/student_grades.php';
        // endif;
        ?>

    </div>
</body>
</html>
