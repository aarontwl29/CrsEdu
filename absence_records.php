<?php
// Include required files
require_once 'db/students.php';
require_once 'db/connection.php';

// Initialize variables
$message = '';
$messageType = '';
$selectedDate = $_GET['date'] ?? $_POST['absence_date'] ?? date('Y-m-d');

// ============================================
// HANDLE FORM SUBMISSION
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_absence'])) {
    $absenceDate = $_POST['absence_date'];
    $conn = getDB();
    $results = [
        'success' => 0,
        'skipped' => 0,
        'empty' => 0
    ];
    
    if (isset($_POST['student_id']) && is_array($_POST['student_id'])) {
        foreach ($_POST['student_id'] as $index => $studentId) {
            $absenceType = $_POST['absence_type'][$index] ?? '';
            
            // Skip empty rows
            if (empty($studentId) || empty($absenceType)) {
                $results['empty']++;
                continue;
            }
            
            // Check if record already exists
            $checkStmt = $conn->prepare("SELECT id FROM absence_records WHERE student_id = ? AND absence_date = ?");
            $checkStmt->bind_param("is", $studentId, $absenceDate);
            $checkStmt->execute();
            $exists = $checkStmt->get_result()->num_rows > 0;
            $checkStmt->close();
            
            if ($exists) {
                $results['skipped']++;
                continue;
            }
            
            // Insert new record
            $insertStmt = $conn->prepare("INSERT INTO absence_records (student_id, absence_date, absence_type) VALUES (?, ?, ?)");
            $insertStmt->bind_param("iss", $studentId, $absenceDate, $absenceType);
            
            if ($insertStmt->execute()) {
                $results['success']++;
            }
            $insertStmt->close();
        }
    }
    
    // Generate message
    if ($results['success'] > 0) {
        $message = "✅ Successfully recorded {$results['success']} absence(s) for " . date('M d, Y', strtotime($absenceDate));
        $messageType = "success";
    } elseif ($results['skipped'] > 0) {
        $message = "⚠️ No new absences added. {$results['skipped']} record(s) already exist for this date.";
        $messageType = "warning";
    } elseif ($results['empty'] > 0) {
        $message = "❌ Please fill in all fields. {$results['empty']} empty row(s) were skipped.";
        $messageType = "error";
    } else {
        $message = "❌ No absences were added. Please check your input.";
        $messageType = "error";
    }
}

// ============================================
// HANDLE DELETION
// ============================================
if (isset($_GET['delete']) && isset($_GET['date'])) {
    $deleteId = intval($_GET['delete']);
    $selectedDate = $_GET['date'];
    $conn = getDB();
    $deleteStmt = $conn->prepare("DELETE FROM absence_records WHERE id = ?");
    $deleteStmt->bind_param("i", $deleteId);
    $deleteStmt->execute();
    $deleteStmt->close();
    header("Location: absence_records.php?date=" . $selectedDate);
    exit;
}

// ============================================
// GET EXISTING RECORDS
// ============================================
$conn = getDB();
$existingStmt = $conn->prepare("
    SELECT ar.id, ar.student_id, ar.absence_type, s.name_chi, s.name_eng, s.short_name, s.class 
    FROM absence_records ar 
    JOIN students s ON ar.student_id = s.id 
    WHERE ar.absence_date = ? 
    ORDER BY s.class, s.name_chi
");
$existingStmt->bind_param("s", $selectedDate);
$existingStmt->execute();
$existingRecords = $existingStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$existingStmt->close();

// ============================================
// GET ALL STUDENTS
// ============================================
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #333;
            margin-bottom: 25px;
            font-size: 32px;
        }
        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #666;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        .back-btn:hover {
            background-color: #555;
            transform: translateY(-2px);
        }
        .message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-weight: 500;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .message.warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }
        .date-selector {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 2px solid #e9ecef;
        }
        .date-selector label {
            font-weight: 600;
            margin-right: 15px;
            color: #495057;
        }
        .date-selector input[type="date"] {
            padding: 10px 15px;
            border: 2px solid #ced4da;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .date-selector input[type="date"]:focus {
            outline: none;
            border-color: #667eea;
        }
        .section {
            margin-bottom: 40px;
        }
        .section-title {
            font-size: 22px;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #667eea;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .existing-records {
            background: #e7f3ff;
            border: 2px solid #2196F3;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .record-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background: white;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            transition: transform 0.2s;
        }
        .record-item:hover {
            transform: translateX(5px);
        }
        .record-info {
            flex: 1;
        }
        .record-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .record-type {
            display: inline-block;
            padding: 4px 12px;
            margin-left: 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #e3f2fd;
            color: #1976D2;
        }
        .delete-btn {
            background: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        .delete-btn:hover {
            background: #d32f2f;
            transform: scale(1.05);
        }
        .form-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
        }
        .absence-row {
            display: grid;
            grid-template-columns: 200px 1fr 220px 50px;
            gap: 15px;
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 2px solid #dee2e6;
            align-items: center;
            transition: border-color 0.3s;
        }
        .absence-row:hover {
            border-color: #667eea;
        }
        .absence-row input[type="text"],
        .absence-row select {
            padding: 12px;
            border: 2px solid #ced4da;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .absence-row input[type="text"]:focus,
        .absence-row select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .student-display {
            font-size: 14px;
        }
        .student-name {
            font-weight: 600;
            color: #333;
        }
        .student-class {
            color: #6c757d;
            font-size: 12px;
        }
        .no-match {
            color: #999;
            font-style: italic;
        }
        .matched {
            color: #28a745;
            font-weight: 600;
        }
        .invalid {
            color: #dc3545;
            font-weight: 600;
        }
        .remove-btn {
            background: #f44336;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }
        .remove-btn:hover {
            background: #d32f2f;
            transform: rotate(90deg);
        }
        .controls {
            display: flex;
            gap: 15px;
            align-items: center;
            margin: 20px 0;
        }
        .add-row-btn {
            background: #2196F3;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .add-row-btn:hover {
            background: #1976D2;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
        }
        .submit-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        .border-valid {
            border-color: #28a745 !important;
            border-width: 2px !important;
        }
        .border-invalid {
            border-color: #dc3545 !important;
            border-width: 2px !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-btn">← Back to Home</a>
        <h1>📅 Student Absence Records</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Date Selector (GET Form) -->
        <form method="GET" action="absence_records.php">
            <div class="date-selector">
                <label for="select_date">📆 Select Date:</label>
                <input type="date" 
                       id="select_date" 
                       name="date" 
                       value="<?php echo htmlspecialchars($selectedDate); ?>" 
                       onchange="this.form.submit()">
            </div>
        </form>
        
        <!-- Existing Records -->
        <?php if (!empty($existingRecords)): ?>
            <div class="existing-records">
                <h3 class="section-title">
                    📋 Recorded Absences - <?php echo date('F j, Y', strtotime($selectedDate)); ?>
                    (<?php echo count($existingRecords); ?> students)
                </h3>
                <?php foreach($existingRecords as $record): ?>
                    <div class="record-item">
                        <div class="record-info">
                            <div class="record-name">
                                <?php echo htmlspecialchars($record['name_chi']); ?> 
                                (<?php echo htmlspecialchars($record['name_eng']); ?>)
                                <?php if (!empty($record['short_name'])): ?>
                                    - <?php echo htmlspecialchars($record['short_name']); ?>
                                <?php endif; ?>
                            </div>
                            <span class="record-type"><?php echo htmlspecialchars($record['absence_type']); ?></span>
                            <span style="color: #6c757d; font-size: 12px; margin-left: 10px;">
                                Class: <?php echo htmlspecialchars($record['class'] ?? 'N/A'); ?>
                            </span>
                        </div>
                        <button class="delete-btn" 
                                onclick="if(confirm('Delete this record?')) window.location.href='?delete=<?php echo $record['id']; ?>&date=<?php echo $selectedDate; ?>'">
                            Delete
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Add New Absences (POST Form) -->
        <div class="section">
            <h3 class="section-title">➕ Add New Absences</h3>
            
            <form method="POST" action="absence_records.php" id="absenceForm">
                <input type="hidden" name="absence_date" value="<?php echo htmlspecialchars($selectedDate); ?>">
                
                <div class="form-section">
                    <div id="absence-rows-container">
                        <!-- Initial row -->
                        <div class="absence-row" data-row="0">
                            <input type="text" 
                                   name="short_name[]" 
                                   placeholder="Enter short name" 
                                   class="short-name-input"
                                   autocomplete="off">
                            <div class="student-display">
                                <span class="no-match">Type short name to search...</span>
                                <input type="hidden" name="student_id[]" value="">
                            </div>
                            <select name="absence_type[]" required>
                                <option value="">-- Select Type --</option>
                                <option value="Sick Leave" selected>Sick Leave</option>
                                
                                <option value="Medical Appointment">Medical Appointment</option>
                                <option value="Personal Leave">Personal Leave</option>
                                <option value="Outside Hong Kong">Outside Hong Kong</option>
                            </select>
                            <button type="button" class="remove-btn" onclick="removeRow(this)">✕</button>
                        </div>
                    </div>
                    
                    <div class="controls">
                        <button type="button" class="add-row-btn" onclick="addRow()">+ Add Row</button>
                        <label>Default Type:</label>
                        <select id="default-absence-type">
                            <option value="Sick Leave" selected>Sick Leave</option>
                            
                            <option value="Medical Appointment">Medical Appointment</option>
                            <option value="Personal Leave">Personal Leave</option>
                            <option value="Outside Hong Kong">Outside Hong Kong</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="submit_absence" class="submit-btn">
                        💾 Save All Absences
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Students data from PHP
        const students = <?php echo json_encode($allStudents); ?>;
        let rowCounter = 1;
        
        // Create lookup map
        const studentByShortName = {};
        students.forEach(student => {
            if (student.short_name) {
                studentByShortName[student.short_name.toLowerCase().trim()] = student;
            }
        });
        
        
        // Setup initial input
        document.querySelectorAll('.short-name-input').forEach(input => {
            setupShortNameInput(input);
        });
        
        function setupShortNameInput(input) {
            const row = input.closest('.absence-row');
            const studentDisplay = row.querySelector('.student-display');
            const studentIdInput = row.querySelector('input[name="student_id[]"]');
            
            input.addEventListener('input', function() {
                const value = this.value.trim().toLowerCase();
                
                if (!value) {
                    studentDisplay.innerHTML = '<span class="no-match">Type short name to search...</span><input type="hidden" name="student_id[]" value="">';
                    input.classList.remove('border-valid', 'border-invalid');
                    return;
                }
                
                const student = studentByShortName[value];
                
                if (student) {
                    // IMPORTANT: Include the hidden input in the innerHTML to preserve it!
                    studentDisplay.innerHTML = `
                        <div>
                            <div class="student-name matched">✓ ${student.name_chi} (${student.name_eng})</div>
                            <div class="student-class">Class: ${student.class || 'N/A'} | ID: ${student.id}</div>
                        </div>
                        <input type="hidden" name="student_id[]" value="${student.id}">
                    `;
                    input.classList.remove('border-invalid');
                    input.classList.add('border-valid');
                } else {
                    studentDisplay.innerHTML = '<span class="invalid">✗ No student found with this short name</span><input type="hidden" name="student_id[]" value="">';
                    input.classList.remove('border-valid');
                    input.classList.add('border-invalid');
                }
            });
        }
        
        function addRow() {
            const container = document.getElementById('absence-rows-container');
            const newRow = document.createElement('div');
            newRow.className = 'absence-row';
            newRow.dataset.row = rowCounter;
            
            const defaultType = document.getElementById('default-absence-type').value;
            
            newRow.innerHTML = `
                <input type="text" 
                       name="short_name[]" 
                       placeholder="Enter short name" 
                       class="short-name-input"
                       autocomplete="off">
                <div class="student-display">
                    <span class="no-match">Type short name to search...</span>
                    <input type="hidden" name="student_id[]" value="">
                </div>
                <select name="absence_type[]" required>
                    <option value="">-- Select Type --</option>
                    <option value="Sick Leave" ${defaultType === 'Sick Leave' ? 'selected' : ''}>Sick Leave</option>
                    
                    <option value="Medical Appointment" ${defaultType === 'Medical Appointment' ? 'selected' : ''}>Medical Appointment</option>
                    <option value="Personal Leave" ${defaultType === 'Personal Leave' ? 'selected' : ''}>Personal Leave</option>
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
