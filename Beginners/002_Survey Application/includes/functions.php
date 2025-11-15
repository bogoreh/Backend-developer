<?php
function getSurveys() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM surveys ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSurvey($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM surveys WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getQuestions($survey_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE survey_id = ? ORDER BY id ASC");
    $stmt->execute([$survey_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSurveyResults($survey_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT q.question_text, r.answer, COUNT(r.answer) as count
        FROM responses r
        JOIN questions q ON r.question_id = q.id
        WHERE r.survey_id = ?
        GROUP BY q.question_text, r.answer
        ORDER BY q.id, count DESC
    ");
    $stmt->execute([$survey_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getResponseCount($survey_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT submitted_at) as response_count 
        FROM responses 
        WHERE survey_id = ?
    ");
    $stmt->execute([$survey_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['response_count'];
}

function validateSurveySubmission($survey_id, $answers) {
    global $pdo;
    
    // Check if survey exists
    $survey = getSurvey($survey_id);
    if (!$survey) {
        return "Invalid survey.";
    }
    
    // Get all questions for this survey
    $questions = getQuestions($survey_id);
    
    // Validate that all required questions are answered
    foreach ($questions as $question) {
        if ($question['is_required'] && !isset($answers[$question['id']])) {
            return "Please answer all required questions.";
        }
    }
    
    return true;
}
?>