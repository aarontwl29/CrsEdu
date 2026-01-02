<?php
// Include required files
require_once 'db/students.php';
require_once 'db/connection.php';

$message = '';
$messageType = '';
// Check GET first (from date picker), then POST (from form submission), then default to today
$selectedDate = isset($_GET['date']) ? $_GET['date'] : (isset($_POST['absence_date']) ? $_POST['absence_date'] : date('Y-m-d'));

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_absence'])) {
    $absenceDate = $_POST['absence_date'];
    $conn = getDB();
    $successCount = 0;
    $skippedCount = 0;
    $emptyCount = 0;
    
    if (!empty($_POST['student_id']) && !empty($_POST['absence_type'])) {
        foreach ($_POST['student_id'] as $index => $studentId) {
            $absenceType = $_POST['absence_type'][$index];
            
            if (empty($studentId) || empty($absenceType)) {
                $emptyCount++;
                continue;
            }
            
            // Check if record already exists
            $checkStmt = $conn->prepare("SELECT id FROM absence_records WHERE student_id = ? AND absence_date = ?");
            $checkStmt->bind_param("is", $studentId, $absenceDate);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows == 0) {
                // Insert new record
                $insertStmt = $conn->prepare("INSERT INTO absence_records (student_id, absence_date, absence_type) VALUES (?, ?, ?)");
                $insertStmt->bind_param("iss", $studentId, $absenceDate, $absenceType);
                if ($insertStmt->execute()) {
                    $successCount++;
                }
                $insertStmt->close();
            } else {
                $skippedCount++;
            }
            $checkStmt->close();
        }
    }
    
    if ($successCount > 0) {
        $message = "Successfully recorded $successCount absence(s) for " . date('M d, Y', strtotime($absenceDate));
        $messageType = "success";
    } elseif ($skippedCount > 0) {
        $message = "No new absences added. $skippedCount record(s) already exist for this date.";
        $messageType = "error";
    } elseif ($emptyCount > 0) {
        $message = "Please select a student and absence type for all rows.";
        $messageType = "error";
    } else {
        $message = "No absences were added. Please check your input.";
        $messageType = "error";
    }
}

// Handle deletion
if (isset($_GET['delete']) && isset($_GET['date'])) {
    $deleteId = intval($_GET['delete']);
    $selectedDate = $_GET['date'];
    $conn = getDB();
    $deleteStmt = $conn->prepare("DELETE FROM absence_records WHERE id = ?");
    $deleteStmt->bind_param("i", $deleteId);
    if ($deleteStmt->execute()) {
        $message = "Absence record deleted successfully";
        $messageType = "success";
    }
    $deleteStmt->close();
    header("Location: absence_records.php?date=" . $selectedDate);
    exit;
}

// Get existing records for selected date
$conn = getDB();
$existingStmt = $conn->prepare("SELECT ar.id, ar.student_id, ar.absence_type, s.name_chi, s.name_eng, s.short_name, s.class 
                                FROM absence_records ar 
                                JOIN students s ON ar.student_id = s.id 
                                WHERE ar.absence_date = ? 
                                ORDER BY s.class, s.name_chi");
$existingStmt->bind_param("s", $selectedDate);
$existingStmt->execute();
$existingResult = $existingStmt->get_result();
$existingRecords = [];
while ($row = $existingResult->fetch_assoc()) {
    $existingRecords[] = $row;
}
$existingStmt->close();

// Get all students for search
$studentsObj = new Students();
$allStudents = $studentsObj->getAll('name_chi', 'ASC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Student Absences</title>
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
            margin-bottom: 20px;
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
        .date-selector {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .date-selector label {
            font-weight: bold;
            margin-right: 10px;
        }
        .date-selector input[type="date"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .section {
            margin-bottom: 40px;
        }
        .section-title {
            font-size: 20px;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4CAF50;
        }
        .absence-row {
            display: grid;
            grid-template-columns: 150px 1fr 200px 60px;
            gap: 15px;
            margin-bottom: 15px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
            align-items: center;
        }
        .absence-row input, .absence-row select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .absence-row input:focus, .absence-row select:focus {
            outline: none;
            border-color: #4CAF50;
        }
        .student-display {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .student-name {
            font-weight: bold;
            color: #333;
        }
        .student-class {
            color: #666;
            font-size: 12px;
        }
        .no-match {
            color: #999;
            font-style: italic;
        }
        .remove-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }
        .remove-btn:hover {
            background-color: #d32f2f;
        }
        .add-row-btn {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .add-row-btn:hover {
            background-color: #1976D2;
        }
        .add-row-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        .add-row-section label {
            font-weight: bold;
            color: #333;
        }
        .add-row-section select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
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
        .existing-records {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .record-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin-bottom: 10px;
            background: white;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .record-info {
            flex: 1;
        }
        .record-name {
            font-weight: bold;
            color: #333;
        }
        .record-type {
            display: inline-block;
            padding: 4px 8px;
            margin-left: 10px;
            border-radius: 4px;
            font-size: 12px;
            background-color: #e3f2fd;
            color: #1976D2;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .delete-btn:hover {
            background-color: #d32f2f;
        }
        .no-records {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 20px;
        }
        .autocomplete-list {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            width: 300px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: none;
        }
        .autocomplete-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            display: none;
        }
        .autocomplete-item:hover {
            background-color: #f0f0f0;
        }
        .short-name-input-wrapper {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">← Back to Home</a>
        <h1>📅 Record Student Absences</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Date Selector -->
        <form method="GET" action="absence_records.php" style="margin: 0;">
            <div class="date-selector">
                <label for="select_date">Select Date:</label>
                <input type="date" id="select_date" name="date" value="<?php echo htmlspecialchars($selectedDate); ?>" 
                       onchange="this.form.submit()">
            </div>
        </form>
        
        <!-- Existing Records -->
        <?php if (!empty($existingRecords)): ?>
            <div class="existing-records">
                <h3 class="section-title">📋 Recorded Absences for <?php echo date('F j, Y', strtotime($selectedDate)); ?> (<?php echo count($existingRecords); ?> student<?php echo count($existingRecords) > 1 ? 's' : ''; ?>)</h3>
                <?php foreach($existingRecords as $record): ?>
                    <div class="record-item">
                        <div class="record-info">
                            <span class="record-name">
                                <?php echo htmlspecialchars($record['name_chi']); ?> 
                                (<?php echo htmlspecialchars($record['name_eng']); ?>)
                            </span>
                            <?php if (!empty($record['short_name'])): ?>
                                - <?php echo htmlspecialchars($record['short_name']); ?>
                            <?php endif; ?>
                            <span class="record-type"><?php echo htmlspecialchars($record['absence_type']); ?></span>
                            <span style="color: #666; font-size: 12px; margin-left: 10px;">
                                Class: <?php echo htmlspecialchars($record['class'] ?? 'N/A'); ?>
                            </span>
                        </div>
                        <a href="?delete=<?php echo $record['id']; ?>&date=<?php echo $selectedDate; ?>" 
                           class="delete-btn" 
                           onclick="return confirm('Are you sure you want to delete this absence record?')">Delete</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Add New Absences -->
        <div class="section">
            <h3 class="section-title">➕ Add New Absences</h3>
            
            <form method="POST" action="">
                <input type="hidden" name="absence_date" value="<?php echo htmlspecialchars($selectedDate); ?>">
                
                <div id="absence-rows-container">
                    <div class="absence-row" data-row="0">
                        <div class="short-name-input-wrapper">
                            <input type="text" 
                                   name="short_name[]" 
                                   placeholder="Short name..." 
                                   class="short-name-input"
                                   data-row="0"
                                   autocomplete="off">
                        </div>
                        <div class="student-display">
                            <span class="no-match">Enter short name to search</span>
                            <input type="hidden" name="student_id[]" value="">
                        </div>
                        <select name="absence_type[]" class="absence-type-select" required>
                            <option value="">Select Type</option>
                            <option value="Sick Leave" selected>Sick Leave</option>
                            <option value="Personal Leave">Personal Leave</option>
                            <option value="Medical Appointment">Medical Appointment</option>
                            <option value="Outside Hong Kong">Outside Hong Kong</option>
                        </select>
                        <button type="button" class="remove-btn" onclick="removeRow(this)">✕</button>
                    </div>
                </div>
                
                <div class="add-row-section">
                    <button type="button" class="add-row-btn" onclick="addRow()">+ Add Another Student</button>
                    <label for="default-absence-type">Default Type:</label>
                    <select id="default-absence-type">
                        <option value="Sick Leave" selected>Sick Leave</option>
                        <option value="Personal Leave">Personal Leave</option>
                        <option value="Medical Appointment">Medical Appointment</option>
                        <option value="Outside Hong Kong">Outside Hong Kong</option>
                    </select>
                </div>
                
                <button type="submit" name="submit_absence" class="submit-btn">💾 Save All Absences</button>
            </form>
        </div>
    </div>
    
    <script>
        const students = <?php echo json_encode($allStudents); ?>;
        let rowCounter = 1;
        
        // Create a map for faster lookup by short_name
        const studentByShortName = {};
        students.forEach(student => {
            if (student.short_name) {
                studentByShortName[student.short_name.toLowerCase()] = student;
            }
        });
        
        // Add event listeners to existing short name inputs
        document.querySelectorAll('.short-name-input').forEach(input => {
            setupShortNameInput(input);
        });
        
        function setupShortNameInput(input) {
            const studentDisplay = input.closest('.absence-row').querySelector('.student-display');
            const studentIdInput = input.closest('.absence-row').querySelector('input[name="student_id[]"]');
            
            input.addEventListener('input', function() {
                const value = this.value.trim();
                
                if (!value) {
                    studentDisplay.innerHTML = '<span class="no-match">Enter short name to search</span>';
                    studentIdInput.value = '';
                    return;
                }
                
                // Find student by exact short name match (case insensitive)
                const student = studentByShortName[value.toLowerCase()];
                
                if (student) {
                    // Found matching student
                    studentIdInput.value = student.id;
                    studentDisplay.innerHTML = `
                        <div>
                            <div class="student-name">${student.name_chi} (${student.name_eng})</div>
                            <div class="student-class">Class: ${student.class || 'N/A'}</div>
                        </div>
                    `;
                    // Add green border to indicate valid input
                    input.style.borderColor = '#4CAF50';
                    input.style.borderWidth = '2px';
                } else {
                    // No matching student
                    studentIdInput.value = '';
                    studentDisplay.innerHTML = '<span class="no-match">No student found with this short name</span>';
                    // Add red border to indicate invalid input
                    input.style.borderColor = '#f44336';
                    input.style.borderWidth = '2px';
                }
            });
            
            // Reset border on focus
            input.addEventListener('focus', function() {
                if (!this.value) {
                    this.style.borderColor = '#ddd';
                    this.style.borderWidth = '1px';
                }
            });
        }
        
        function addRow() {
            const container = document.getElementById('absence-rows-container');
            const newRow = document.createElement('div');
            newRow.className = 'absence-row';
            newRow.dataset.row = rowCounter;
            
            // Get default absence type from dropdown
            const defaultType = document.getElementById('default-absence-type').value;
            
            newRow.innerHTML = `
                <div class="short-name-input-wrapper">
                    <input type="text" 
                           name="short_name[]" 
                           placeholder="Short name..." 
                           class="short-name-input"
                           data-row="${rowCounter}"
                           autocomplete="off">
                </div>
                <div class="student-display">
                    <span class="no-match">Enter short name to search</span>
                    <input type="hidden" name="student_id[]" value="">
                </div>
                <select name="absence_type[]" class="absence-type-select" required>
                    <option value="">Select Type</option>
                    <option value="Sick Leave" ${defaultType === 'Sick Leave' ? 'selected' : ''}>Sick Leave</option>
                    <option value="Personal Leave" ${defaultType === 'Personal Leave' ? 'selected' : ''}>Personal Leave</option>
                    <option value="Medical Appointment" ${defaultType === 'Medical Appointment' ? 'selected' : ''}>Medical Appointment</option>
                    <option value="Outside Hong Kong" ${defaultType === 'Outside Hong Kong' ? 'selected' : ''}>Outside Hong Kong</option>
                </select>
                <button type="button" class="remove-btn" onclick="removeRow(this)">✕</button>
            `;
            
            container.appendChild(newRow);
            const newInput = newRow.querySelector('.short-name-input');
            setupShortNameInput(newInput);
            newInput.focus();
            
            rowCounter++;
        }
        
        function removeRow(button) {
            const row = button.closest('.absence-row');
            row.remove();
        }
    </script>
</body>
</html>
