<?php
include 'config/database.php';
include 'includes/header.php';

// Fetch all schedules
$query = "SELECT * FROM schedules ORDER BY schedule_date DESC, start_time ASC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="schedules-container">
    <h2>Your Schedules</h2>
    
    <?php if (empty($schedules)): ?>
        <div class="empty-state">
            <h3>No schedules found</h3>
            <p>Create your first schedule to get started!</p>
            <a href="index.php" class="btn btn-primary">Create Schedule</a>
        </div>
    <?php else: ?>
        <div class="schedules-grid">
            <?php foreach ($schedules as $schedule): ?>
                <div class="schedule-card">
                    <div class="schedule-header">
                        <h3><?php echo htmlspecialchars($schedule['title']); ?></h3>
                        <span class="schedule-date">
                            <?php echo date('M j, Y', strtotime($schedule['schedule_date'])); ?>
                        </span>
                    </div>
                    
                    <?php if (!empty($schedule['description'])): ?>
                        <p class="schedule-description"><?php echo htmlspecialchars($schedule['description']); ?></p>
                    <?php endif; ?>
                    
                    <div class="schedule-time">
                        <span class="time-badge">
                            <?php echo date('g:i A', strtotime($schedule['start_time'])); ?> - 
                            <?php echo date('g:i A', strtotime($schedule['end_time'])); ?>
                        </span>
                    </div>
                    
                    <div class="schedule-actions">
                        <a href="delete_schedule.php?id=<?php echo $schedule['id']; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this schedule?')">
                            Delete
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>