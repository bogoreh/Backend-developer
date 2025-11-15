<?php
include 'config/database.php';
include 'includes/header.php';

if ($_POST) {
    try {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $schedule_date = $_POST['schedule_date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];

        $query = "INSERT INTO schedules (title, description, schedule_date, start_time, end_time) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$title, $description, $schedule_date, $start_time, $end_time]);

        $success = "Schedule added successfully!";
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<div class="form-container">
    <h2>Create New Schedule</h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" class="schedule-form">
        <div class="form-group">
            <label for="title">Title *</label>
            <input type="text" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="schedule_date">Date *</label>
                <input type="date" id="schedule_date" name="schedule_date" required>
            </div>

            <div class="form-group">
                <label for="start_time">Start Time *</label>
                <input type="time" id="start_time" name="start_time" required>
            </div>

            <div class="form-group">
                <label for="end_time">End Time *</label>
                <input type="time" id="end_time" name="end_time" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Create Schedule</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>