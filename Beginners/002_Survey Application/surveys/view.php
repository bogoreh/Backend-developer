<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

$survey_id = $_GET['id'] ?? null;
$survey = $survey_id ? getSurvey($survey_id) : null;
$surveys = getSurveys();
$results = $survey_id ? getSurveyResults($survey_id) : [];
$response_count = $survey_id ? getResponseCount($survey_id) : 0;
$questions = $survey_id ? getQuestions($survey_id) : [];

// Get detailed results for charts
$detailed_results = [];
if ($survey_id) {
    $stmt = $pdo->prepare("
        SELECT 
            q.id as question_id,
            q.question_text,
            q.question_type,
            r.answer,
            COUNT(r.answer) as count
        FROM responses r
        JOIN questions q ON r.question_id = q.id
        WHERE r.survey_id = ?
        GROUP BY q.id, r.answer
        ORDER BY q.id, count DESC
    ");
    $stmt->execute([$survey_id]);
    $raw_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group by question
    foreach ($raw_results as $result) {
        $question_id = $result['question_id'];
        if (!isset($detailed_results[$question_id])) {
            $detailed_results[$question_id] = [
                'question_text' => $result['question_text'],
                'question_type' => $result['question_type'],
                'answers' => []
            ];
        }
        $detailed_results[$question_id]['answers'][] = [
            'answer' => $result['answer'],
            'count' => $result['count']
        ];
    }
}
?>
<?php include '../includes/header.php'; ?>

<div class="results-container">
    <?php if (!$survey_id): ?>
        <!-- Survey Selection View -->
        <div class="section-header">
            <h1>Survey Results</h1>
            <p>Select a survey to view its results and analytics</p>
        </div>

        <div class="survey-selector">
            <?php if (empty($surveys)): ?>
                <div class="no-surveys">
                    <p>No surveys available yet.</p>
                    <a href="../surveys/create.php" class="btn btn-primary">Create First Survey</a>
                </div>
            <?php else: ?>
                <div class="grid">
                    <?php foreach ($surveys as $s): ?>
                        <?php 
                        $response_count = getResponseCount($s['id']);
                        $questions_count = count(getQuestions($s['id']));
                        ?>
                        <div class="survey-card">
                            <h3><?php echo htmlspecialchars($s['title']); ?></h3>
                            <p><?php echo htmlspecialchars($s['description']); ?></p>
                            
                            <div class="survey-meta">
                                <span><?php echo $questions_count; ?> questions</span>
                                <span><?php echo $response_count; ?> responses</span>
                                <span><?php echo date('M j, Y', strtotime($s['created_at'])); ?></span>
                            </div>
                            
                            <div class="survey-actions">
                                <a href="?id=<?php echo $s['id']; ?>" class="btn btn-primary">
                                    View Results
                                </a>
                                <a href="../surveys/view.php?id=<?php echo $s['id']; ?>" class="btn btn-outline">
                                    Take Survey
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- Detailed Results View -->
        <div class="results-header">
            <div>
                <h1><?php echo htmlspecialchars($survey['title']); ?></h1>
                <p class="survey-description"><?php echo htmlspecialchars($survey['description']); ?></p>
            </div>
            <a href="?" class="btn btn-outline">← Back to Surveys</a>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $response_count; ?></div>
                <div class="stat-label">Total Responses</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo count($questions); ?></div>
                <div class="stat-label">Questions</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php echo $survey ? date('M j, Y', strtotime($survey['created_at'])) : 'N/A'; ?>
                </div>
                <div class="stat-label">Created On</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">
                    <?php 
                    $latest_response = $pdo->prepare("SELECT MAX(submitted_at) as latest FROM responses WHERE survey_id = ?");
                    $latest_response->execute([$survey_id]);
                    $latest = $latest_response->fetch(PDO::FETCH_ASSOC);
                    echo $latest['latest'] ? date('M j, Y', strtotime($latest['latest'])) : 'No responses';
                    ?>
                </div>
                <div class="stat-label">Latest Response</div>
            </div>
        </div>

        <?php if (empty($detailed_results)): ?>
            <div class="no-results">
                <h3>No Results Yet</h3>
                <p>This survey hasn't received any responses yet.</p>
                <div class="action-buttons">
                    <a href="../surveys/view.php?id=<?php echo $survey_id; ?>" class="btn btn-primary">
                        Take Survey
                    </a>
                    <a href="../index.php" class="btn btn-outline">
                        Share Survey
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Detailed Results -->
            <div class="detailed-results">
                <?php foreach ($detailed_results as $question_id => $question_data): ?>
                    <div class="result-card">
                        <h3><?php echo htmlspecialchars($question_data['question_text']); ?></h3>
                        <p class="question-type">
                            Type: 
                            <span class="type-badge">
                                <?php 
                                $type_map = [
                                    'text' => 'Text Answer',
                                    'radio' => 'Multiple Choice',
                                    'checkbox' => 'Checkboxes'
                                ];
                                echo $type_map[$question_data['question_type']] ?? $question_data['question_type'];
                                ?>
                            </span>
                        </p>

                        <div class="answers-list">
                            <?php if ($question_data['question_type'] === 'text'): ?>
                                <!-- Text Answers -->
                                <div class="text-answers">
                                    <h4>Responses (<?php echo array_sum(array_column($question_data['answers'], 'count')); ?>):</h4>
                                    <?php 
                                    $text_responses = [];
                                    foreach ($question_data['answers'] as $answer_data) {
                                        $responses = json_decode($answer_data['answer']) ?: [$answer_data['answer']];
                                        foreach ($responses as $response) {
                                            if (!empty(trim($response))) {
                                                $text_responses[] = trim($response);
                                            }
                                        }
                                    }
                                    ?>
                                    <?php if (empty($text_responses)): ?>
                                        <p class="no-answers">No text responses yet.</p>
                                    <?php else: ?>
                                        <div class="text-responses">
                                            <?php foreach (array_slice($text_responses, 0, 10) as $response): ?>
                                                <div class="text-response">
                                                    "<?php echo htmlspecialchars($response); ?>"
                                                </div>
                                            <?php endforeach; ?>
                                            <?php if (count($text_responses) > 10): ?>
                                                <div class="more-responses">
                                                    and <?php echo count($text_responses) - 10; ?> more responses...
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            <?php else: ?>
                                <!-- Multiple Choice Results -->
                                <div class="chart-results">
                                    <?php
                                    $total_votes = array_sum(array_column($question_data['answers'], 'count'));
                                    $max_votes = $total_votes > 0 ? max(array_column($question_data['answers'], 'count')) : 0;
                                    ?>
                                    
                                    <div class="chart-bars">
                                        <?php foreach ($question_data['answers'] as $answer_data): 
                                            $answer_text = $answer_data['answer'];
                                            $count = $answer_data['count'];
                                            $percentage = $total_votes > 0 ? ($count / $total_votes) * 100 : 0;
                                            $bar_width = $max_votes > 0 ? ($count / $max_votes) * 100 : 0;
                                            
                                            // Handle JSON encoded arrays (for checkboxes)
                                            if (json_decode($answer_text)) {
                                                $decoded = json_decode($answer_text, true);
                                                if (is_array($decoded)) {
                                                    $answer_text = implode(', ', $decoded);
                                                }
                                            }
                                        ?>
                                            <div class="chart-bar">
                                                <div class="bar-label">
                                                    <span class="answer-text"><?php echo htmlspecialchars($answer_text); ?></span>
                                                    <span class="answer-stats">
                                                        <span class="count"><?php echo $count; ?></span>
                                                        <span class="percentage">(<?php echo number_format($percentage, 1); ?>%)</span>
                                                    </span>
                                                </div>
                                                <div class="bar-track">
                                                    <div class="bar-fill" style="width: <?php echo $bar_width; ?>%">
                                                        <div class="bar-percentage"><?php echo number_format($percentage, 1); ?>%</div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="chart-summary">
                                        <strong>Total votes: <?php echo $total_votes; ?></strong>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Export Options -->
            <div class="export-section">
                <div class="result-card">
                    <h3>Export Results</h3>
                    <p>Download the survey results for further analysis.</p>
                    <div class="export-actions">
                        <button onclick="exportToCSV()" class="btn btn-secondary">
                            📊 Export to CSV
                        </button>
                        <button onclick="window.print()" class="btn btn-outline">
                            🖨️ Print Results
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function exportToCSV() {
    alert('CSV export functionality would be implemented here.\nThis would generate a downloadable CSV file with all survey results.');
    // In a real implementation, this would make an AJAX call to generate and download a CSV file
}

// Add some interactive features
document.addEventListener('DOMContentLoaded', function() {
    // Animate chart bars when they come into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateX(0)';
            }
        });
    });

    document.querySelectorAll('.bar-fill').forEach(bar => {
        bar.style.opacity = '0';
        bar.style.transform = 'translateX(-20px)';
        bar.style.transition = 'all 0.6s ease';
        observer.observe(bar);
    });
});
</script>

<style>
.type-badge {
    background: var(--gray-200);
    color: var(--gray-700);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.chart-bars {
    margin: 20px 0;
}

.chart-bar {
    margin: 15px 0;
}

.bar-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.answer-text {
    flex: 1;
    font-weight: 500;
}

.answer-stats {
    color: var(--gray-600);
    font-size: 0.8rem;
}

.bar-track {
    width: 100%;
    height: 30px;
    background: var(--gray-200);
    border-radius: 15px;
    overflow: hidden;
    position: relative;
}

.bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    border-radius: 15px;
    transition: width 1s ease;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 0 15px;
    min-width: 40px;
}

.bar-percentage {
    color: white;
    font-weight: 600;
    font-size: 0.8rem;
    text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
}

.chart-summary {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid var(--gray-300);
    color: var(--gray-600);
}

.text-answers {
    margin-top: 20px;
}

.text-responses {
    display: grid;
    gap: 10px;
    margin-top: 15px;
}

.text-response {
    background: var(--gray-100);
    padding: 15px;
    border-radius: var(--radius);
    border-left: 4px solid var(--primary);
    font-style: italic;
    color: var(--gray-700);
}

.more-responses {
    text-align: center;
    color: var(--gray-500);
    font-style: italic;
    padding: 10px;
}

.no-answers {
    color: var(--gray-500);
    font-style: italic;
    text-align: center;
    padding: 20px;
}

.export-section {
    margin-top: 40px;
}

.export-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.question-type {
    color: var(--gray-600);
    margin-bottom: 20px;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .bar-label {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .export-actions {
        flex-direction: column;
    }
    
    .bar-fill {
        min-width: 60px;
    }
}
</style>

<?php include '../includes/footer.php'; ?>