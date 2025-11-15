<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

$success = false;
$error = null;

if ($_POST) {
    try {
        $pdo->beginTransaction();

        // Insert survey
        $stmt = $pdo->prepare("INSERT INTO surveys (title, description, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([
            trim($_POST['title']),
            trim($_POST['description'])
        ]);
        $survey_id = $pdo->lastInsertId();

        // Insert questions
        if (isset($_POST['questions']) && is_array($_POST['questions'])) {
            foreach ($_POST['questions'] as $index => $questionData) {
                if (!empty(trim($questionData['text']))) {
                    $question_text = trim($questionData['text']);
                    $question_type = $questionData['type'];
                    $is_required = isset($questionData['required']) ? 1 : 0;
                    
                    $options = null;
                    if (in_array($question_type, ['radio', 'checkbox']) && !empty(trim($questionData['options']))) {
                        $options_array = array_map('trim', explode(',', $questionData['options']));
                        $options_array = array_filter($options_array); // Remove empty options
                        $options = json_encode($options_array);
                    }

                    $stmt = $pdo->prepare("INSERT INTO questions (survey_id, question_text, question_type, options, is_required, display_order) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $survey_id,
                        $question_text,
                        $question_type,
                        $options,
                        $is_required,
                        $index
                    ]);
                }
            }
        }

        $pdo->commit();
        $success = true;
        
        // Redirect to success page
        header("Location: view.php?id=" . $survey_id . "&created=1");
        exit;

    } catch(PDOException $e) {
        $pdo->rollBack();
        $error = "Error creating survey: " . $e->getMessage();
    }
}
?>
<?php include '../includes/header.php'; ?>

<div class="form-container">
    <div class="form-header">
        <h1>Create New Survey</h1>
        <p>Design your survey by adding questions and customizing options</p>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-error">
            <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            Survey created successfully! Redirecting...
        </div>
    <?php endif; ?>

    <form method="POST" id="surveyForm" class="<?php echo $success ? 'loading' : ''; ?>">
        <!-- Survey Basic Info -->
        <div class="survey-basic-info">
            <div class="form-group">
                <label for="title">Survey Title *</label>
                <input type="text" id="title" name="title" required 
                       placeholder="Enter a descriptive title for your survey"
                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="description">Survey Description</label>
                <textarea id="description" name="description" rows="3" 
                          placeholder="Describe the purpose of this survey (optional)"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>
        </div>

        <!-- Questions Section -->
        <div class="questions-section">
            <div class="section-header">
                <h3>Survey Questions</h3>
                <p>Add questions to your survey. You can choose from different question types.</p>
            </div>

            <div id="questions-container">
                <!-- Question template will be added here by JavaScript -->
                <div class="question" data-index="0">
                    <div class="question-header">
                        <span class="question-number">Question 1</span>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion(this)" disabled>
                            Remove
                        </button>
                    </div>
                    
                    <div class="form-group">
                        <label>Question Text *</label>
                        <input type="text" name="questions[0][text]" required 
                               placeholder="Enter your question here">
                    </div>
                    
                    <div class="form-group">
                        <label>Question Type *</label>
                        <select name="questions[0][type]" class="question-type" required onchange="toggleOptions(this)">
                            <option value="text">Text Answer</option>
                            <option value="radio">Multiple Choice</option>
                            <option value="checkbox">Checkboxes</option>
                        </select>
                    </div>
                    
                    <div class="form-group options-group" style="display: none;">
                        <label>Options (comma separated) *</label>
                        <input type="text" name="questions[0][options]" 
                               placeholder="Option 1, Option 2, Option 3">
                        <small class="help-text">Separate each option with a comma</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="questions[0][required]" checked>
                            <span class="checkmark"></span>
                            Required question
                        </label>
                    </div>
                </div>
            </div>

            <div class="add-question-section">
                <button type="button" class="btn btn-secondary" onclick="addQuestion()">
                    <span>+</span>
                    Add Another Question
                </button>
                <span class="question-count" id="questionCount">1 question added</span>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">
                <span class="btn-text">Create Survey</span>
                <span class="btn-loading" style="display: none;">Creating...</span>
            </button>
            <a href="../index.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
let questionIndex = 1;

function updateQuestionCount() {
    const count = document.querySelectorAll('.question').length;
    const countElement = document.getElementById('questionCount');
    countElement.textContent = count + ' question' + (count !== 1 ? 's' : '') + ' added';
}

function addQuestion() {
    const container = document.getElementById('questions-container');
    const newQuestion = document.createElement('div');
    newQuestion.className = 'question';
    newQuestion.setAttribute('data-index', questionIndex);
    newQuestion.innerHTML = `
        <div class="question-header">
            <span class="question-number">Question ${questionIndex + 1}</span>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion(this)">
                Remove
            </button>
        </div>
        
        <div class="form-group">
            <label>Question Text *</label>
            <input type="text" name="questions[${questionIndex}][text]" required 
                   placeholder="Enter your question here">
        </div>
        
        <div class="form-group">
            <label>Question Type *</label>
            <select name="questions[${questionIndex}][type]" class="question-type" required onchange="toggleOptions(this)">
                <option value="text">Text Answer</option>
                <option value="radio">Multiple Choice</option>
                <option value="checkbox">Checkboxes</option>
            </select>
        </div>
        
        <div class="form-group options-group" style="display: none;">
            <label>Options (comma separated) *</label>
            <input type="text" name="questions[${questionIndex}][options]" 
                   placeholder="Option 1, Option 2, Option 3">
            <small class="help-text">Separate each option with a comma</small>
        </div>
        
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="questions[${questionIndex}][required]" checked>
                <span class="checkmark"></span>
                Required question
            </label>
        </div>
    `;
    
    container.appendChild(newQuestion);
    questionIndex++;
    updateQuestionCount();
    updateRemoveButtons();
}

function removeQuestion(button) {
    const questions = document.querySelectorAll('.question');
    if (questions.length > 1) {
        button.closest('.question').remove();
        renumberQuestions();
        updateQuestionCount();
        updateRemoveButtons();
    }
}

function renumberQuestions() {
    const questions = document.querySelectorAll('.question');
    questions.forEach((question, index) => {
        const numberElement = question.querySelector('.question-number');
        numberElement.textContent = 'Question ' + (index + 1);
        
        // Update all input names
        const inputs = question.querySelectorAll('input, select');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/questions\[\d+\]/, `questions[${index}]`));
            }
        });
    });
}

function updateRemoveButtons() {
    const questions = document.querySelectorAll('.question');
    const removeButtons = document.querySelectorAll('.question .btn-danger');
    
    removeButtons.forEach(button => {
        button.disabled = questions.length <= 1;
    });
}

function toggleOptions(select) {
    const questionDiv = select.closest('.question');
    const optionsGroup = questionDiv.querySelector('.options-group');
    const optionsInput = optionsGroup.querySelector('input');
    
    if (select.value === 'text') {
        optionsGroup.style.display = 'none';
        optionsInput.removeAttribute('required');
    } else {
        optionsGroup.style.display = 'block';
        optionsInput.setAttribute('required', 'required');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateQuestionCount();
    updateRemoveButtons();
    
    // Add form submission handler
    const form = document.getElementById('surveyForm');
    const submitBtn = form.querySelector('.btn-primary');
    
    form.addEventListener('submit', function(e) {
        // Basic validation
        const questions = document.querySelectorAll('.question');
        let isValid = true;
        
        questions.forEach((question, index) => {
            const textInput = question.querySelector('input[type="text"]');
            const typeSelect = question.querySelector('.question-type');
            const optionsInput = question.querySelector('.options-group input');
            
            if (!textInput.value.trim()) {
                isValid = false;
                textInput.style.borderColor = 'var(--danger)';
            } else {
                textInput.style.borderColor = '';
            }
            
            if (typeSelect.value !== 'text' && (!optionsInput.value.trim() || optionsInput.value.split(',').filter(opt => opt.trim()).length < 2)) {
                isValid = false;
                optionsInput.style.borderColor = 'var(--danger)';
            } else {
                optionsInput.style.borderColor = '';
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields and ensure multiple choice questions have at least 2 options.');
            return;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.querySelector('.btn-text').style.display = 'none';
        submitBtn.querySelector('.btn-loading').style.display = 'inline';
    });
});

// Add some helpful keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        document.getElementById('surveyForm').dispatchEvent(new Event('submit'));
    }
});
</script>

<style>
.help-text {
    color: var(--gray-500);
    font-size: 0.9rem;
    display: block;
    margin-top: 5px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: normal;
}

.checkbox-label input[type="checkbox"] {
    width: auto;
    margin-right: 10px;
}

.add-question-section {
    text-align: center;
    padding: 30px;
    border: 2px dashed var(--gray-300);
    border-radius: var(--radius);
    margin: 20px 0;
}

.add-question-section .btn {
    margin-bottom: 10px;
}

.question-count {
    color: var(--gray-500);
    font-size: 0.9rem;
}

.survey-basic-info {
    background: var(--gray-50);
    padding: 30px;
    border-radius: var(--radius);
    margin-bottom: 30px;
    border: 1px solid var(--gray-200);
}

.questions-section {
    margin: 40px 0;
}
</style>

<?php include '../includes/footer.php'; ?>