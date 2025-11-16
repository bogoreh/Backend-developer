document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            const checkIn = document.getElementById('check_in').value;
            const checkOut = document.getElementById('check_out').value;
            const today = new Date().toISOString().split('T')[0];
            
            if (checkIn < today) {
                alert('Check-in date cannot be in the past');
                e.preventDefault();
                return;
            }
            
            if (checkOut <= checkIn) {
                alert('Check-out date must be after check-in date');
                e.preventDefault();
                return;
            }
        });
    }

    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});