<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

$survey_id = $_GET['survey_id'] ?? null;
$survey = $survey_id ? getSurvey($survey_id) : null;

if (!$survey) {
    header("Location: ../index.php");
    exit;
}
?>
<?php include '../includes/header.php'; ?>

<div class="thank-you-container">
    <div class="thank-you-card">
        <div class="success-icon">✓</div>
        <h1>Thank You!</h1>
        <p class="success-message">Your response has been recorded successfully.</p>
        
        <div class="survey-info">
            <h3><?php echo htmlspecialchars($survey['title']); ?></h3>
            <p>We appreciate you taking the time to complete this survey.</p>
        </div>

        <div class="action-buttons">
            <a href="../index.php" class="btn btn-primary">Return to Home</a>
            <a href="view.php?id=<?php echo $survey_id; ?>" class="btn btn-outline">Take Survey Again</a>
            <a href="../results/view.php?id=<?php echo $survey_id; ?>" class="btn btn-secondary">View Results</a>
        </div>

        <div class="share-section">
            <p>Share this survey with others:</p>
            <div class="share-link">
                <input type="text" value="<?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?id=' . $survey_id; ?>" readonly>
                <button onclick="copyShareLink()" class="btn btn-outline">Copy Link</button>
            </div>
        </div>
    </div>
</div>

<script>
function copyShareLink() {
    const input = document.querySelector('.share-link input');
    input.select();
    document.execCommand('copy');
    alert('Survey link copied to clipboard!');
}
</script>

<style>
.thank-you-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 70vh;
    padding: 40px 20px;
}

.thank-you-card {
    background: white;
    padding: 50px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    text-align: center;
    max-width: 600px;
    width: 100%;
}

.success-icon {
    font-size: 4rem;
    color: #10b981;
    margin-bottom: 20px;
}

.success-message {
    font-size: 1.2rem;
    color: #64748b;
    margin-bottom: 30px;
}

.survey-info {
    background: #f8fafc;
    padding: 25px;
    border-radius: 10px;
    margin: 30px 0;
    border-left: 4px solid #3b82f6;
}

.survey-info h3 {
    color: #1e293b;
    margin-bottom: 10px;
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
    margin: 30px 0;
}

.share-section {
    margin-top: 30px;
    padding-top: 30px;
    border-top: 1px solid #e2e8f0;
}

.share-link {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.share-link input {
    flex: 1;
    padding: 10px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    background: #f8fafc;
}

@media (max-width: 768px) {
    .thank-you-card {
        padding: 30px 20px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .share-link {
        flex-direction: column;
    }
}
</style>

<?php include '../includes/footer.php'; ?>