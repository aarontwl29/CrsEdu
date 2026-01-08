<?php
// Get current month and year from URL parameters, or use current date
$displayMonth = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$displayYear = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Ensure valid month
if ($displayMonth < 1) {
    $displayMonth = 12;
    $displayYear--;
} elseif ($displayMonth > 12) {
    $displayMonth = 1;
    $displayYear++;
}

// Fetch absence records for the student (current month)
$stmt = $conn->prepare("
    SELECT absence_date, absence_type 
    FROM absence_records 
    WHERE student_id = ? 
    AND YEAR(absence_date) = ? 
    AND MONTH(absence_date) = ?
    ORDER BY absence_date ASC
");
$stmt->bind_param("iii", $student_id, $displayYear, $displayMonth);
$stmt->execute();
$result = $stmt->get_result();

// Store absences in an associative array for quick lookup
$absences = [];
$currentMonthAbsences = [];
while ($row = $result->fetch_assoc()) {
    $day = intval(date('j', strtotime($row['absence_date'])));
    $absences[$day] = $row['absence_type'];
    $currentMonthAbsences[] = [
        'date' => $row['absence_date'],
        'type' => $row['absence_type'],
        'day' => date('j', strtotime($row['absence_date'])),
        'weekday' => date('D', strtotime($row['absence_date']))
    ];
}
$stmt->close();

// Fetch all absence records grouped by month for summary
$summaryStmt = $conn->prepare("
    SELECT 
        DATE_FORMAT(absence_date, '%Y-%m') as month_key,
        DATE_FORMAT(absence_date, '%Y') as year,
        DATE_FORMAT(absence_date, '%M %Y') as month_name,
        DATE_FORMAT(absence_date, '%m') as month_num,
        absence_date,
        absence_type,
        COUNT(*) OVER (PARTITION BY DATE_FORMAT(absence_date, '%Y-%m')) as month_count
    FROM absence_records 
    WHERE student_id = ?
    ORDER BY absence_date DESC
");
$summaryStmt->bind_param("i", $student_id);
$summaryStmt->execute();
$summaryResult = $summaryStmt->get_result();

// Group absences by month
$monthlySummary = [];
while ($row = $summaryResult->fetch_assoc()) {
    $monthKey = $row['month_key'];
    if (!isset($monthlySummary[$monthKey])) {
        $monthlySummary[$monthKey] = [
            'month_name' => $row['month_name'],
            'year' => $row['year'],
            'month_num' => $row['month_num'],
            'count' => $row['month_count'],
            'absences' => []
        ];
    }
    $monthlySummary[$monthKey]['absences'][] = [
        'date' => $row['absence_date'],
        'type' => $row['absence_type'],
        'day' => date('j', strtotime($row['absence_date'])),
        'weekday' => date('D, M j', strtotime($row['absence_date']))
    ];
}
$summaryStmt->close();

// Calculate calendar information
$firstDayOfMonth = mktime(0, 0, 0, $displayMonth, 1, $displayYear);
$daysInMonth = date('t', $firstDayOfMonth);
$dayOfWeek = date('w', $firstDayOfMonth); // 0 (Sunday) to 6 (Saturday)
$monthName = date('F Y', $firstDayOfMonth);

// Calculate previous and next month
$prevMonth = $displayMonth - 1;
$prevYear = $displayYear;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $displayMonth + 1;
$nextYear = $displayYear;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

// Count total absences for this month
$absenceCount = count($absences);
?>

<div class="section-card">
    <div class="section-header">
        <h2>📅 Absence Records</h2>
        <div class="absence-summary">
            <?php if ($absenceCount > 0): ?>
                <span class="absence-badge"><?php echo $absenceCount; ?> absence<?php echo $absenceCount > 1 ? 's' : ''; ?> this month</span>
            <?php else: ?>
                <span class="perfect-attendance">✓ Perfect Attendance</span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="absence-layout">
        <!-- Left: Calendar -->
        <div class="calendar-container">
            <!-- Month Navigation -->
            <div class="calendar-nav">
                <a href="?id=<?php echo $student_id; ?>&month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>#absences" class="nav-btn">
                    ◀ Previous
                </a>
                <h3 class="current-month"><?php echo $monthName; ?></h3>
                <a href="?id=<?php echo $student_id; ?>&month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>#absences" class="nav-btn">
                    Next ▶
                </a>
            </div>

            <!-- Calendar Grid -->
            <div class="calendar">
                <!-- Day Headers -->
                <div class="calendar-header">Sun</div>
                <div class="calendar-header">Mon</div>
                <div class="calendar-header">Tue</div>
                <div class="calendar-header">Wed</div>
                <div class="calendar-header">Thu</div>
                <div class="calendar-header">Fri</div>
                <div class="calendar-header">Sat</div>

                <?php
                // Empty cells for days before the first day of month
                for ($i = 0; $i < $dayOfWeek; $i++) {
                    echo '<div class="calendar-day empty"></div>';
                }

                // Generate calendar days
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $isAbsent = isset($absences[$day]);
                    $isToday = ($day == date('j') && $displayMonth == date('n') && $displayYear == date('Y'));
                    
                    $classes = 'calendar-day';
                    if ($isAbsent) {
                        $classes .= ' absent';
                    }
                    if ($isToday) {
                        $classes .= ' today';
                    }
                    
                    echo '<div class="' . $classes . '"';
                    if ($isAbsent) {
                        $absenceType = htmlspecialchars($absences[$day]);
                        echo ' data-tooltip="' . $absenceType . '"';
                    }
                    echo '>';
                    echo '<span class="day-number">' . $day . '</span>';
                    if ($isAbsent) {
                        echo '<span class="absence-indicator">●</span>';
                    }
                    echo '</div>';
                }

                // Empty cells to complete the last week
                $remainingCells = (7 - (($dayOfWeek + $daysInMonth) % 7)) % 7;
                for ($i = 0; $i < $remainingCells; $i++) {
                    echo '<div class="calendar-day empty"></div>';
                }
                ?>
            </div>

            <!-- Legend -->
            <div class="calendar-legend">
                <div class="legend-item">
                    <span class="legend-color absent-color"></span>
                    <span>Absent</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color today-color"></span>
                    <span>Today</span>
                </div>
            </div>
        </div>

        <!-- Right: Summary -->
        <div class="summary-container">
            <h3 class="summary-title">📊 Monthly Summary</h3>
            
            <?php if (empty($monthlySummary)): ?>
                <div class="no-absences">
                    <div class="no-absences-icon">✓</div>
                    <p>No absence records found</p>
                </div>
            <?php else: ?>
                <div class="summary-list">
                    <?php foreach ($monthlySummary as $monthKey => $monthData): ?>
                        <div class="summary-month">
                            <div class="month-header" onclick="toggleMonth('<?php echo $monthKey; ?>')">
                                <div class="month-info">
                                    <span class="month-name"><?php echo htmlspecialchars($monthData['month_name']); ?></span>
                                    <span class="month-count"><?php echo $monthData['count']; ?> absence<?php echo $monthData['count'] > 1 ? 's' : ''; ?></span>
                                </div>
                                <span class="toggle-icon" id="icon-<?php echo $monthKey; ?>">▼</span>
                            </div>
                            <div class="month-details" id="details-<?php echo $monthKey; ?>" style="display: none;">
                                <?php foreach ($monthData['absences'] as $absence): ?>
                                    <div class="absence-item">
                                        <div class="absence-date">
                                            <span class="date-day"><?php echo $absence['day']; ?></span>
                                            <span class="date-info"><?php echo $absence['weekday']; ?></span>
                                        </div>
                                        <div class="absence-type-badge">
                                            <?php echo htmlspecialchars($absence['type']); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleMonth(monthKey) {
    const details = document.getElementById('details-' + monthKey);
    const icon = document.getElementById('icon-' + monthKey);
    
    if (details.style.display === 'none') {
        details.style.display = 'block';
        icon.textContent = '▲';
        icon.style.transform = 'rotate(180deg)';
    } else {
        details.style.display = 'none';
        icon.textContent = '▼';
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>

<style>
.absence-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-top: 20px;
}

.calendar-container {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.summary-container {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    max-height: 600px;
    overflow-y: auto;
}

.summary-title {
    margin: 0 0 20px 0;
    font-size: 18px;
    font-weight: 600;
    color: #2d3748;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.no-absences {
    text-align: center;
    padding: 40px 20px;
    color: #94a3b8;
}

.no-absences-icon {
    font-size: 48px;
    color: #51cf66;
    margin-bottom: 10px;
}

.no-absences p {
    margin: 0;
    font-size: 15px;
}

.summary-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.summary-month {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.summary-month:hover {
    border-color: #667eea;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.month-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 15px 18px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    user-select: none;
    transition: all 0.3s ease;
}

.month-header:hover {
    background: linear-gradient(135deg, #5568d3 0%, #6a4294 100%);
}

.month-header:active {
    transform: scale(0.98);
}

.month-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.month-name {
    font-size: 16px;
    font-weight: 700;
    color: white;
}

.month-count {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
}

.toggle-icon {
    font-size: 14px;
    color: white;
    transition: transform 0.3s ease;
    font-weight: bold;
}

.month-details {
    background: #f8f9fa;
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.absence-item {
    background: white;
    padding: 12px 15px;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 4px solid #ff6b6b;
    transition: all 0.2s ease;
}

.absence-item:hover {
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.absence-date {
    display: flex;
    align-items: center;
    gap: 12px;
}

.date-day {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
    font-size: 18px;
    font-weight: 700;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(255, 107, 107, 0.3);
}

.date-info {
    font-size: 14px;
    color: #64748b;
    font-weight: 500;
}

.absence-type-badge {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #fbbf24;
}

.absence-summary {
    display: inline-block;
}

.absence-badge {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(255, 107, 107, 0.3);
}

.perfect-attendance {
    background: linear-gradient(135deg, #51cf66 0%, #37b24d 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(81, 207, 102, 0.3);
}

.calendar-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.current-month {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #2d3748;
}

.nav-btn {
    padding: 8px 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);
}

.nav-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.calendar {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-bottom: 20px;
}

.calendar-header {
    text-align: center;
    font-weight: 700;
    font-size: 13px;
    color: #667eea;
    padding: 12px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 10px;
    position: relative;
    cursor: default;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.calendar-day:not(.empty):hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.calendar-day.empty {
    background: transparent;
}

.calendar-day.today {
    border-color: #667eea;
    background: linear-gradient(135deg, #e0e7ff 0%, #f0f4ff 100%);
}

.calendar-day.absent {
    background: linear-gradient(135deg, #ffe0e0 0%, #ffcccc 100%);
    border-color: #ff6b6b;
    font-weight: 600;
}

.calendar-day.absent:hover {
    background: linear-gradient(135deg, #ffd0d0 0%, #ffb8b8 100%);
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
}

.day-number {
    font-size: 15px;
    color: #2d3748;
    font-weight: 500;
}

.calendar-day.absent .day-number {
    color: #c92a2a;
    font-weight: 700;
}

.absence-indicator {
    position: absolute;
    top: 6px;
    right: 6px;
    color: #ff6b6b;
    font-size: 8px;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.7;
        transform: scale(1.2);
    }
}

/* Tooltip */
.calendar-day[data-tooltip] {
    cursor: pointer;
}

.calendar-day[data-tooltip]::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: calc(100% + 10px);
    left: 50%;
    transform: translateX(-50%) translateY(5px);
    background: rgba(0, 0, 0, 0.9);
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s ease;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.calendar-day[data-tooltip]::before {
    content: '';
    position: absolute;
    bottom: calc(100% + 2px);
    left: 50%;
    transform: translateX(-50%) translateY(5px);
    border: 6px solid transparent;
    border-top-color: rgba(0, 0, 0, 0.9);
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s ease;
    z-index: 1000;
}

.calendar-day[data-tooltip]:hover::after,
.calendar-day[data-tooltip]:hover::before {
    opacity: 1;
    transform: translateX(-50%) translateY(0);
}

.calendar-legend {
    display: flex;
    gap: 20px;
    justify-content: center;
    padding-top: 15px;
    border-top: 2px solid #f0f0f0;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #64748b;
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 6px;
    border: 2px solid #e2e8f0;
}

.legend-color.absent-color {
    background: linear-gradient(135deg, #ffe0e0 0%, #ffcccc 100%);
    border-color: #ff6b6b;
}

.legend-color.today-color {
    background: linear-gradient(135deg, #e0e7ff 0%, #f0f4ff 100%);
    border-color: #667eea;
}

/* Scrollbar Styling */
.summary-container::-webkit-scrollbar {
    width: 8px;
}

.summary-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.summary-container::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

.summary-container::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5568d3 0%, #6a4294 100%);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .absence-layout {
        grid-template-columns: 1fr;
    }
    
    .summary-container {
        max-height: 400px;
    }
}

@media (max-width: 768px) {
    .calendar-nav {
        flex-direction: column;
        gap: 15px;
    }
    
    .current-month {
        font-size: 18px;
    }
    
    .nav-btn {
        width: 100%;
        text-align: center;
    }
    
    .calendar {
        gap: 4px;
    }
    
    .calendar-header {
        font-size: 11px;
        padding: 8px 0;
    }
    
    .day-number {
        font-size: 13px;
    }
    
    .calendar-legend {
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    
    .absence-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}
</style>
