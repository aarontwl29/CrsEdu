<?php
// Include students class
require_once 'db/students.php';
require_once 'db/connection.php';

// Create Students instance
$studentsObj = new Students();
$message = '';
$messageType = '';

// Handle form submission
$savedDate = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_report'])) {
    $studentName = trim($_POST['student_name']);
    $reportDate = $_POST['report_date'];
    // Trim whitespace and normalize line breaks
    $content = trim($_POST['content']);
    // Remove excessive empty lines (more than 2 consecutive line breaks become 2)
    $content = preg_replace('/\n{3,}/', "\n\n", $content);
    
    if (!empty($studentName) && !empty($reportDate) && !empty($content)) {
        // Search for student by Chinese or English name
        $conn = getDB();
        
        $stmt = $conn->prepare("SELECT id FROM students WHERE name_chi = ? OR name_eng = ? LIMIT 1");
        $stmt->bind_param("ss", $studentName, $studentName);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        
        if ($student) {
            // Insert daily report
            $insertStmt = $conn->prepare("INSERT INTO daily_reports (student_id, report_date, content) VALUES (?, ?, ?)");
            $insertStmt->bind_param("iss", $student['id'], $reportDate, $content);
            if ($insertStmt->execute()) {
                $message = "Daily report saved successfully!";
                $messageType = "success";
                // Save date before clearing form
                $savedDate = $reportDate;
                // Clear form but keep date
                $_POST = array();
            } else {
                $message = "Error saving report. Please try again.";
                $messageType = "error";
            }
            $insertStmt->close();
        } else {
            $message = "Student not found. Please enter a valid name.";
            $messageType = "error";
        }
        $stmt->close();
    } else {
        $message = "Please fill in all fields.";
        $messageType = "error";
    }
}

// Get all students for autocomplete
$students = $studentsObj->getAll('name_chi', 'ASC');

// Fetch all daily reports
$conn = getDB();
$reportsQuery = "SELECT dr.id, dr.student_id, dr.report_date, dr.content, 
                        s.name_chi, s.name_eng, s.class 
                 FROM daily_reports dr 
                 JOIN students s ON dr.student_id = s.id 
                 ORDER BY dr.report_date DESC, dr.id DESC";
$reportsResult = $conn->query($reportsQuery);
$dailyReports = [];
if ($reportsResult) {
    while ($row = $reportsResult->fetch_assoc()) {
        $dailyReports[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrsEdu - Daily Report</title>
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
            max-width: 800px;
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
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }
        input[type="text"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            font-family: Arial, sans-serif;
        }
        textarea {
            min-height: 150px;
            resize: vertical;
        }
        input:focus,
        textarea:focus {
            outline: none;
            border-color: #4CAF50;
        }
        .submit-btn {
            width: 100%;
            padding: 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .autocomplete-items {
            position: absolute;
            border: 1px solid #d4d4d4;
            border-top: none;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
            background-color: white;
            max-height: 200px;
            overflow-y: auto;
        }
        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #d4d4d4;
        }
        .autocomplete-items div:hover {
            background-color: #e9e9e9;
        }
        .autocomplete {
            position: relative;
        }
        .hint {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .reports-section {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
        }
        .reports-section h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .report-card {
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            transition: box-shadow 0.3s;
        }
        .report-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .report-student {
            font-weight: bold;
            color: #333;
            font-size: 16px;
        }
        .report-date {
            color: #666;
            font-size: 14px;
        }
        .report-class {
            color: #888;
            font-size: 14px;
            margin-left: 10px;
        }
        .report-content {
            color: #555;
            line-height: 1.8;
            font-size: 15px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: pre-wrap;
        }
        .no-reports {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">← Back to Home</a>
        <h1>Daily Student Report</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="student_name">Student Name (Chinese or English):</label>
                <div class="autocomplete">
                    <input type="text" id="student_name" name="student_name" 
                           placeholder="Enter student name..." 
                           required 
                           autocomplete="off"
                           value="<?php echo isset($_POST['student_name']) ? htmlspecialchars($_POST['student_name']) : ''; ?>">
                </div>
                <div class="hint">Start typing to see suggestions</div>
            </div>
            
            <div class="form-group">
                <label for="report_date">Date:</label>
                <input type="date" id="report_date" name="report_date" 
                       value="<?php echo !empty($savedDate) ? htmlspecialchars($savedDate) : (isset($_POST['report_date']) ? htmlspecialchars($_POST['report_date']) : date('Y-m-d')); ?>" 
                       required>
            </div>
            
            <div class="form-group">
                <label for="content">Daily Description:</label>
                <textarea id="content" name="content" 
                          placeholder="Enter teacher's daily report about the student..." 
                          required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
            </div>
            
            <button type="submit" name="submit_report" class="submit-btn">Save Report</button>
        </form>
        
        <!-- Daily Reports List -->
        <div class="reports-section">
            <h2>All Daily Reports</h2>
            
            <?php if (!empty($dailyReports)): ?>
                <?php foreach($dailyReports as $report): ?>
                    <div class="report-card">
                        <div class="report-header">
                            <div>
                                <span class="report-student">
                                    <?php echo htmlspecialchars($report['name_chi']); ?> 
                                    (<?php echo htmlspecialchars($report['name_eng']); ?>)
                                </span>
                                <span class="report-class">
                                    Class: <?php echo htmlspecialchars($report['class'] ?? 'N/A'); ?>
                                </span>
                            </div>
                            <span class="report-date">
                                <?php echo date('Y-m-d', strtotime($report['report_date'])); ?>
                            </span>
                        </div>
                        <div class="report-content"><?php echo htmlspecialchars(trim($report['content'])); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-reports">No daily reports yet. Start adding reports using the form above.</div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Autocomplete functionality
        const students = <?php echo json_encode(array_map(function($s) {
            return [
                'chi' => $s['name_chi'],
                'eng' => $s['name_eng']
            ];
        }, $students)); ?>;
        
        const input = document.getElementById('student_name');
        const autocompleteContainer = document.querySelector('.autocomplete');
        
        let currentFocus = -1;
        
        input.addEventListener('input', function() {
            closeAllLists();
            if (!this.value) return;
            
            currentFocus = -1;
            const val = this.value.toLowerCase();
            
            const list = document.createElement('div');
            list.className = 'autocomplete-items';
            autocompleteContainer.appendChild(list);
            
            students.forEach(student => {
                const chiMatch = student.chi.toLowerCase().includes(val);
                const engMatch = student.eng.toLowerCase().includes(val);
                
                if (chiMatch || engMatch) {
                    const item = document.createElement('div');
                    
                    if (chiMatch && engMatch) {
                        item.innerHTML = `<strong>${student.chi}</strong> (${student.eng})`;
                        item.dataset.name = student.chi;
                    } else if (chiMatch) {
                        item.innerHTML = `<strong>${student.chi}</strong> (${student.eng})`;
                        item.dataset.name = student.chi;
                    } else {
                        item.innerHTML = `${student.chi} (<strong>${student.eng}</strong>)`;
                        item.dataset.name = student.eng;
                    }
                    
                    item.addEventListener('click', function() {
                        input.value = this.dataset.name;
                        closeAllLists();
                    });
                    
                    list.appendChild(item);
                }
            });
        });
        
        input.addEventListener('keydown', function(e) {
            let items = autocompleteContainer.querySelector('.autocomplete-items');
            if (items) items = items.getElementsByTagName('div');
            
            if (e.keyCode === 40) { // Down
                currentFocus++;
                addActive(items);
                e.preventDefault();
            } else if (e.keyCode === 38) { // Up
                currentFocus--;
                addActive(items);
                e.preventDefault();
            } else if (e.keyCode === 13) { // Enter
                e.preventDefault();
                if (currentFocus > -1 && items) {
                    items[currentFocus].click();
                }
            }
        });
        
        function addActive(items) {
            if (!items) return;
            removeActive(items);
            if (currentFocus >= items.length) currentFocus = 0;
            if (currentFocus < 0) currentFocus = items.length - 1;
            items[currentFocus].style.backgroundColor = '#e9e9e9';
        }
        
        function removeActive(items) {
            for (let i = 0; i < items.length; i++) {
                items[i].style.backgroundColor = '';
            }
        }
        
        function closeAllLists() {
            const items = document.querySelectorAll('.autocomplete-items');
            items.forEach(item => item.remove());
        }
        
        document.addEventListener('click', function(e) {
            if (e.target !== input) {
                closeAllLists();
            }
        });
    </script>
</body>
</html>
