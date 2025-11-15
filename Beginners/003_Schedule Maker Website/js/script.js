// Enhanced validation for add_schedule.php
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('scheduleForm');
    const dateInput = document.getElementById('schedule_date');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const timeValidation = document.getElementById('timeValidation');
    const validationMessage = document.getElementById('validationMessage');

    // Set minimum date to today
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
    }

    // Real-time time validation
    function validateTimes() {
        if (startTimeInput.value && endTimeInput.value) {
            if (startTimeInput.value >= endTimeInput.value) {
                timeValidation.style.display = 'block';
                validationMessage.textContent = 'End time must be after start time';
                validationMessage.style.color = '#e74c3c';
                return false;
            } else {
                timeValidation.style.display = 'block';
                validationMessage.textContent = 'Time range looks good!';
                validationMessage.style.color = '#27ae60';
                setTimeout(() => {
                    timeValidation.style.display = 'none';
                }, 3000);
                return true;
            }
        }
        timeValidation.style.display = 'none';
        return true;
    }

    // Add event listeners for real-time validation
    if (startTimeInput && endTimeInput) {
        startTimeInput.addEventListener('change', validateTimes);
        endTimeInput.addEventListener('change', validateTimes);
        startTimeInput.addEventListener('input', validateTimes);
        endTimeInput.addEventListener('input', validateTimes);
    }

    // Form submission validation
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateTimes()) {
                e.preventDefault();
                alert('Please fix the time validation errors before submitting.');
                return false;
            }

            // Additional validation can be added here
            const title = document.getElementById('title').value.trim();
            if (title.length === 0) {
                e.preventDefault();
                alert('Please enter a title for your schedule.');
                return false;
            }

            return true;
        });
    }

    // Auto-hide success messages after 5 seconds
    const successAlerts = document.querySelectorAll('.alert-success');
    successAlerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 500);
        }, 5000);
    });

    // Clear form validation on reset
    const resetButton = form.querySelector('button[type="reset"]');
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            timeValidation.style.display = 'none';
            // Clear any validation styles
            const inputs = form.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.style.borderColor = '#e1e5e9';
            });
        });
    }
});