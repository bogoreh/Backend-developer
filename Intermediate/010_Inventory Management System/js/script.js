// Inventory Management System JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Confirm before deleting
    const deleteButtons = document.querySelectorAll('a[href*="delete"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });

    // Form validation enhancement
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });

    // Price formatting
    const priceInputs = document.querySelectorAll('input[name="price"]');
    priceInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });

    // Search functionality enhancement
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            // You can add debounced search here if needed
        });
    }
});

// Stock level indicators
function updateStockIndicators() {
    const quantityCells = document.querySelectorAll('td:nth-child(5)');
    quantityCells.forEach(cell => {
        const quantity = parseInt(cell.textContent);
        if (quantity === 0) {
            cell.innerHTML = '<span class="badge bg-danger">Out of Stock</span>';
        } else if (quantity < 10) {
            cell.innerHTML = '<span class="badge bg-warning">Low Stock</span>';
        } else {
            cell.innerHTML = '<span class="badge bg-success">In Stock</span>';
        }
    });
}