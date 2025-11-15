<?php
include 'config/database.php';
include 'includes/header.php';

// Initialize variables
$title = $description = $schedule_date = $start_time = $end_time = '';
$success = $error = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $schedule_date = $_POST['schedule_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    
    // Validate required fields
    if (empty($title) || empty($schedule_date) || empty($start_time) || empty($end_time)) {
        $error = "Please fill in all required fields (Title, Date, Start Time, End Time)";
    } 
    // Validate date
    elseif (strtotime($schedule_date) < strtotime(date('Y-m-d'))) {
        $error = "Schedule date cannot be in the past";
    }
    // Validate time
    elseif (strtotime($start_time) >= strtotime($end_time)) {
        $error = "End time must be after start time";
    }
    else {
        try {
            // Insert into database
            $query = "INSERT INTO schedules (title, description, schedule_date, start_time, end_time) 
                      VALUES (:title, :description, :schedule_date, :start_time, :end_time)";
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':schedule_date', $schedule_date);
            $stmt->bindParam(':start_time', $start_time);
            $stmt->bindParam(':end_time', $end_time);
            
            if ($stmt->execute()) {
                $success = "Schedule added successfully!";
                
                // Clear form fields
                $title = $description = $schedule_date = $start_time = $end_time = '';
            } else {
                $error = "Failed to add schedule. Please try again.";
            }
        } catch(PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<div class="page-header">
    <div class="header-content">
        <h1>Add New Schedule</h1>
        <p>Create a new event or appointment in your schedule</p>
    </div>
    <a href="view_schedules.php" class="btn btn-secondary">
        ← Back to Schedules
    </a>
</div>

<div class="form-container">
    <?php if ($success): ?>
        <div class="alert alert-success">
            <span class="alert-icon">✓</span>
            <?php echo $success; ?>
            <a href="view_schedules.php" class="alert-link">View all schedules</a>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <span class="alert-icon">⚠</span>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="schedule-form" id="scheduleForm">
        <div class="form-section">
            <h3>Basic Information</h3>
            
            <div class="form-group">
                <label for="title">Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" 
                       required maxlength="255" placeholder="Enter schedule title">
                <small class="form-help">Give your schedule a clear, descriptive title</small>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" 
                          placeholder="Add details about your schedule (optional)"><?php echo htmlspecialchars($description); ?></textarea>
                <small class="form-help">Add any additional information about this schedule</small>
            </div>
        </div>

        <div class="form-section">
            <h3>Date & Time</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="schedule_date">Date <span class="required">*</span></label>
                    <input type="date" id="schedule_date" name="schedule_date" 
                           value="<?php echo htmlspecialchars($schedule_date); ?>" required>
                    <small class="form-help">Select the date for your schedule</small>
                </div>

                <div class="form-group">
                    <label for="start_time">Start Time <span class="required">*</span></label>
                    <input type="time" id="start_time" name="start_time" 
                           value="<?php echo htmlspecialchars($start_time); ?>" required>
                    <small class="form-help">When does it start?</small>
                </div>

                <div class="form-group">
                    <label for="end_time">End Time <span class="required">*</span></label>
                    <input type="time" id="end_time" name="end_time" 
                           value="<?php echo htmlspecialchars($end_time); ?>" required>
                    <small class="form-help">When does it end?</small>
                </div>
            </div>
            
            <div class="time-validation" id="timeValidation" style="display: none;">
                <span class="validation-message" id="validationMessage"></span>
            </div>
        </div>

        <div class="form-actions">
            <button type="reset" class="btn btn-outline">Clear Form</button>
            <button type="submit" class="btn btn-primary">
                <span class="btn-icon">+</span>
                Create Schedule
            </button>
        </div>
    </form>
</div>

<div class="quick-tips">
    <h3>💡 Quick Tips</h3>
    <ul>
        <li>Use clear, descriptive titles for easy identification</li>
        <li>Add detailed descriptions for important events</li>
        <li>Double-check dates and times before saving</li>
        <li>You can always edit schedules later if needed</li>
    </ul>
</div>

<?php include 'includes/footer.php'; ?>