<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

$survey_id = $_GET['id'] ?? null;
$survey = $survey_id ? getSurvey($survey_id) : null;
$surveys = getSurveys();
$results = $survey_id ? getSurveyResults($survey_id) : [];
?>
<?php include '../includes/header.php'; ?>

<div class="results-container">
    <h1>Survey Results</h1>

    <?php if (!$survey_id): ?>
        <div class="survey-selector">
            <h2>Select a Survey</h2>
            <div class="grid">
                <?php foreach ($surveys as $s): ?>
                    <div class="survey-card">
                        <h3><?php echo htmlspecialchars($s['title']); ?></h3>
                        <p><?php echo htmlspecialchars($s['description']); ?></p>
                        <a href="?id=<?php echo $s['id']; ?>" class="btn btn-primary">View Results</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="survey-header">
            <h2><?php echo htmlspecialchars($survey['title']); ?></h2>
            <p><?php echo htmlspecialchars($survey['description']); ?></p>
        </div>

        <?php if (empty($results)): ?>
            <div class="no-results">
                <p>No responses yet for this survey.</p>
            </div>
        <?php else: ?>
            <div class="results-grid">
                <?php
                $groupedResults = [];
                foreach ($results as $result) {
                    $groupedResults[$result['question_text']][] = $result;
                }
                
                foreach ($groupedResults as $question => $answers): ?>
                    <div class="result-card">
                        <h3><?php echo htmlspecialchars($question); ?></h3>
                        <div class="answers">
                            <?php foreach ($answers as $answer): ?>
                                <div class="answer-item">
                                    <span class="answer-text"><?php echo htmlspecialchars($answer['answer']); ?></span>
                                    <span class="answer-count"><?php echo $answer['count']; ?> responses</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="results-actions">
            <a href="?">← Back to Survey List</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>