<!-- Daily Reports Section -->
<div class="section">
    <h2 class="section-title">Daily Reports</h2>
    
    <?php if (!empty($dailyReports)): ?>
        <?php foreach($dailyReports as $report): ?>
            <div class="report-card">
                <div class="report-date">
                    <?php echo date('l, F j, Y', strtotime($report['report_date'])); ?>
                </div>
                <div class="report-content">
                    <?php echo htmlspecialchars($report['content']); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-data">
            No daily reports available for this student yet.
        </div>
    <?php endif; ?>
</div>
