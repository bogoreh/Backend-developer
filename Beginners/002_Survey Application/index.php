<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
?>
<?php include 'includes/header.php'; ?>

<div class="hero">
    <h1>Create Amazing Surveys</h1>
    <p>Collect feedback, understand your audience, and make data-driven decisions</p>
    <a href="surveys/create.php" class="btn btn-primary">Create Your First Survey</a>
</div>

<div class="surveys-grid">
    <h2>Available Surveys</h2>
    <div class="grid">
        <?php
        $surveys = getSurveys();
        if (empty($surveys)): ?>
            <div class="no-surveys">
                <p>No surveys available yet. <a href="surveys/create.php">Create the first one!</a></p>
            </div>
        <?php else: ?>
            <?php foreach ($surveys as $survey): ?>
                <div class="survey-card">
                    <h3><?php echo htmlspecialchars($survey['title']); ?></h3>
                    <p><?php echo htmlspecialchars($survey['description']); ?></p>
                    <div class="survey-actions">
                        <a href="surveys/view.php?id=<?php echo $survey['id']; ?>" class="btn btn-secondary">Take Survey</a>
                        <a href="results/view.php?id=<?php echo $survey['id']; ?>" class="btn btn-outline">View Results</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>