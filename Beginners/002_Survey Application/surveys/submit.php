<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

if ($_POST) {
    try {
        $survey_id = $_POST['survey_id'];
        
        // Validate survey exists
        $survey = getSurvey($survey_id);
        if (!$survey) {
            header("Location: ../index.php?error=invalid_survey");
            exit;
        }

        // Process each answer
        foreach ($_POST['answers'] as $question_id => $answer) {
            // Skip empty answers
            if (empty($answer) || (is_array($answer) && empty(array_filter($answer)))) {
                continue;
            }

            // Handle array answers (checkboxes)
            if (is_array($answer)) {
                $answer = json_encode(array_values(array_filter($answer)));
            }

            // Insert response
            $stmt = $pdo->prepare("INSERT INTO responses (survey_id, question_id, answer) VALUES (?, ?, ?)");
            $stmt->execute([$survey_id, $question_id, $answer]);
        }

        // Redirect to thank you page
        header("Location: thank_you.php?survey_id=" . $survey_id);
        exit;

    } catch(PDOException $e) {
        $error = "Error submitting survey: " . $e->getMessage();
        header("Location: view.php?id=" . $survey_id . "&error=" . urlencode($error));
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
?>