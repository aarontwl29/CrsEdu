<!-- Basic Information Section -->
<div class="section">
    <h2 class="section-title">Basic Information</h2>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Student ID</div>
            <div class="info-value"><?php echo htmlspecialchars($student['id']); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Chinese Name</div>
            <div class="info-value"><?php echo htmlspecialchars($student['name_chi']); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">English Name</div>
            <div class="info-value"><?php echo htmlspecialchars($student['name_eng']); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Nickname</div>
            <div class="info-value"><?php echo htmlspecialchars($student['nickname'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Gender</div>
            <div class="info-value"><?php echo $student['gender'] === 'M' ? 'Male' : 'Female'; ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Class</div>
            <div class="info-value"><?php echo htmlspecialchars($student['class'] ?? 'N/A'); ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Status</div>
            <div class="info-value"><?php echo htmlspecialchars($student['status']); ?></div>
        </div>
    </div>
</div>

<div class="section-divider"></div>
