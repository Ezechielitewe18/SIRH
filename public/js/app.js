/**
 * SIRH - JavaScript principal
 */

// Confirmation de suppression
document.querySelectorAll('form[onsubmit]').forEach(form => {
    form.addEventListener('submit', function(e) {
        // Géré via onsubmit inline
    });
});

// Fermeture automatique des alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// Empêcher les clics multiples
document.querySelectorAll('button[type="submit"]').forEach(btn => {
    btn.addEventListener('click', function() {
        if (this.form) {
            this.disabled = true;
            setTimeout(() => { this.disabled = false; }, 3000);
        }
    });
});
