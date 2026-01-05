/**
 * Main JavaScript File
 * Handles global functionality including cookie consent banner
 */

// Handle cookie consent
function handleCookieConsent(accepted) {
    // Send AJAX request to set consent
    fetch('ajax/set_cookie_consent.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ accepted: accepted })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide banner
            const banner = document.getElementById('cookieConsentBanner');
            if (banner) {
                banner.style.display = 'none';
            }
            
            // Show notification
            if (accepted) {
                showNotification('Cookie preferences saved', 'success');
            } else {
                showNotification('Only essential cookies will be used', 'info');
            }
        }
    })
    .catch(error => {
        console.error('Error setting cookie consent:', error);
    });
}

// Show notification helper
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Document ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize header shrink on scroll
    initHeaderShrink();
    
    // Initialize smooth scroll
    initSmoothScroll();
    
    // Initialize tooltips
    initTooltips();
});

// Header shrink effect
function initHeaderShrink() {
    const header = document.getElementById('mainHeader');
    if (!header) return;
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('shrink');
        } else {
            header.classList.remove('shrink');
        }
    });
}

// Smooth scroll for anchor links
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#' || href === '#!') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Initialize Bootstrap tooltips
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Apply user preferences (theme, language, etc.)
function applyUserPreferences() {
    // This function can be extended to apply user preferences from cookies
    // For now, it's a placeholder for future implementation
}

// Save user preference
function savePreference(name, value) {
    fetch('ajax/save_preference.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name: name, value: value })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(`Preference "${name}" saved successfully`, 'success');
            // Reload page to apply preference if needed
            if (data.reload) {
                setTimeout(() => location.reload(), 500);
            }
        } else {
            showNotification('Failed to save preference', 'danger');
        }
    })
    .catch(error => {
        console.error('Error saving preference:', error);
        showNotification('Error saving preference', 'danger');
    });
}
