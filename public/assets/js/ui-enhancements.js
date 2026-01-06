/**
 * UI/UX Enhancements
 * Loading spinners, confirmation dialogs, tooltips, and improved feedback
 */

// ==========================================
// LOADING SPINNER
// ==========================================

/**
 * Show loading spinner overlay
 * @param {string} message - Optional message to display
 */
function showLoadingSpinner(message = 'Loading...') {
    // Remove existing spinner if any
    hideLoadingSpinner();
    
    const spinner = document.createElement('div');
    spinner.id = 'loadingSpinner';
    spinner.className = 'loading-spinner-overlay';
    spinner.innerHTML = `
        <div class="loading-spinner-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 mb-0">${message}</p>
        </div>
    `;
    
    document.body.appendChild(spinner);
    // Prevent body scroll when spinner is active
    document.body.style.overflow = 'hidden';
}

/**
 * Hide loading spinner
 */
function hideLoadingSpinner() {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.remove();
        document.body.style.overflow = '';
    }
}

/**
 * Show inline loading spinner in element
 * @param {HTMLElement} element - Element to show spinner in
 * @param {string} size - Size: 'sm', 'md', 'lg'
 */
function showInlineSpinner(element, size = 'sm') {
    if (!element) return;
    
    const sizeClass = size === 'sm' ? 'spinner-border-sm' : '';
    const spinner = document.createElement('span');
    spinner.className = `inline-spinner spinner-border ${sizeClass} text-primary ms-2`;
    spinner.setAttribute('role', 'status');
    spinner.innerHTML = '<span class="visually-hidden">Loading...</span>';
    
    element.appendChild(spinner);
}

/**
 * Remove inline spinner from element
 * @param {HTMLElement} element - Element to remove spinner from
 */
function hideInlineSpinner(element) {
    if (!element) return;
    
    const spinner = element.querySelector('.inline-spinner');
    if (spinner) {
        spinner.remove();
    }
}

// ==========================================
// CONFIRMATION DIALOGS
// ==========================================

/**
 * Show modern confirmation dialog
 * @param {Object} options - Configuration options
 * @param {string} options.title - Dialog title
 * @param {string} options.message - Dialog message
 * @param {string} options.confirmText - Confirm button text (default: 'Confirm')
 * @param {string} options.cancelText - Cancel button text (default: 'Cancel')
 * @param {string} options.confirmClass - Confirm button class (default: 'btn-danger')
 * @param {Function} options.onConfirm - Callback on confirm
 * @param {Function} options.onCancel - Callback on cancel
 */
function showConfirmDialog(options) {
    const defaults = {
        title: 'Confirm Action',
        message: 'Are you sure you want to proceed?',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        confirmClass: 'btn-danger',
        onConfirm: () => {},
        onCancel: () => {}
    };
    
    const config = { ...defaults, ...options };
    
    // Remove existing dialog
    const existing = document.getElementById('confirmDialog');
    if (existing) existing.remove();
    
    // Create dialog
    const dialog = document.createElement('div');
    dialog.id = 'confirmDialog';
    dialog.className = 'custom-confirm-dialog';
    dialog.innerHTML = `
        <div class="confirm-dialog-backdrop"></div>
        <div class="confirm-dialog-content">
            <div class="confirm-dialog-header">
                <h5>${config.title}</h5>
            </div>
            <div class="confirm-dialog-body">
                <p>${config.message}</p>
            </div>
            <div class="confirm-dialog-footer">
                <button type="button" class="btn btn-secondary" id="confirmCancel">
                    ${config.cancelText}
                </button>
                <button type="button" class="btn ${config.confirmClass}" id="confirmAction">
                    ${config.confirmText}
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(dialog);
    
    // Add event listeners
    const confirmBtn = dialog.querySelector('#confirmAction');
    const cancelBtn = dialog.querySelector('#confirmCancel');
    const backdrop = dialog.querySelector('.confirm-dialog-backdrop');
    
    const closeDialog = () => {
        dialog.classList.add('hiding');
        setTimeout(() => dialog.remove(), 300);
    };
    
    confirmBtn.addEventListener('click', () => {
        closeDialog();
        config.onConfirm();
    });
    
    cancelBtn.addEventListener('click', () => {
        closeDialog();
        config.onCancel();
    });
    
    backdrop.addEventListener('click', () => {
        closeDialog();
        config.onCancel();
    });
    
    // Show with animation
    setTimeout(() => dialog.classList.add('show'), 10);
}

/**
 * Confirmation for delete actions
 * @param {string} itemType - Type of item (e.g., 'session', 'user')
 * @param {string} itemName - Name of the item
 * @param {Function} onConfirm - Callback function
 */
function confirmDelete(itemType, itemName, onConfirm) {
    showConfirmDialog({
        title: `Delete ${itemType}`,
        message: `Are you sure you want to delete "${itemName}"?<br><br>This action cannot be undone.`,
        confirmText: 'Delete',
        confirmClass: 'btn-danger',
        onConfirm: onConfirm
    });
}

// ==========================================
// FORM VALIDATION FEEDBACK
// ==========================================

/**
 * Enhanced form validation with real-time feedback
 * @param {HTMLFormElement} form - Form element
 */
function enhanceFormValidation(form) {
    if (!form) return;
    
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        // Add real-time validation on blur
        input.addEventListener('blur', () => {
            validateField(input);
        });
        
        // Clear error on input
        input.addEventListener('input', () => {
            if (input.classList.contains('is-invalid')) {
                input.classList.remove('is-invalid');
                const feedback = input.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.remove();
                }
            }
        });
    });
    
    // Enhanced submit validation
    form.addEventListener('submit', (e) => {
        let isValid = true;
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });
}

/**
 * Validate individual form field
 * @param {HTMLElement} field - Input field to validate
 * @returns {boolean} - True if valid
 */
function validateField(field) {
    // Remove existing feedback
    const existingFeedback = field.nextElementSibling;
    if (existingFeedback && existingFeedback.classList.contains('invalid-feedback')) {
        existingFeedback.remove();
    }
    field.classList.remove('is-invalid', 'is-valid');
    
    // Skip if field is disabled or not required
    if (field.disabled || (!field.required && !field.value)) {
        return true;
    }
    
    let isValid = field.checkValidity();
    let message = '';
    
    // Custom validation messages
    if (!isValid) {
        if (field.validity.valueMissing) {
            message = `${getFieldLabel(field)} is required.`;
        } else if (field.validity.typeMismatch) {
            if (field.type === 'email') {
                message = 'Please enter a valid email address.';
            } else if (field.type === 'url') {
                message = 'Please enter a valid URL.';
            }
        } else if (field.validity.tooShort) {
            message = `Please enter at least ${field.minLength} characters.`;
        } else if (field.validity.tooLong) {
            message = `Please enter no more than ${field.maxLength} characters.`;
        } else if (field.validity.rangeUnderflow) {
            message = `Please enter a value greater than or equal to ${field.min}.`;
        } else if (field.validity.rangeOverflow) {
            message = `Please enter a value less than or equal to ${field.max}.`;
        } else if (field.validity.patternMismatch) {
            message = field.getAttribute('data-pattern-message') || 'Please match the requested format.';
        } else {
            message = field.validationMessage;
        }
        
        // Add error feedback
        field.classList.add('is-invalid');
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback d-block';
        feedback.textContent = message;
        field.after(feedback);
    } else {
        field.classList.add('is-valid');
    }
    
    return isValid;
}

/**
 * Get field label text
 * @param {HTMLElement} field - Input field
 * @returns {string} - Label text
 */
function getFieldLabel(field) {
    const label = document.querySelector(`label[for="${field.id}"]`);
    if (label) {
        return label.textContent.replace('*', '').trim();
    }
    return field.name || 'This field';
}

// ==========================================
// TOOLTIPS
// ==========================================

/**
 * Initialize Bootstrap tooltips for elements with data-bs-toggle="tooltip"
 */
function initializeTooltips() {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
}

/**
 * Add tooltip to element
 * @param {HTMLElement} element - Element to add tooltip to
 * @param {string} text - Tooltip text
 * @param {string} placement - Placement: 'top', 'bottom', 'left', 'right'
 */
function addTooltip(element, text, placement = 'top') {
    if (!element) return;
    
    element.setAttribute('data-bs-toggle', 'tooltip');
    element.setAttribute('data-bs-placement', placement);
    element.setAttribute('title', text);
    
    new bootstrap.Tooltip(element);
}

/**
 * Add tooltips to all form fields with help text
 */
function addFormTooltips() {
    const fieldsWithHelp = document.querySelectorAll('[aria-describedby], .form-text');
    
    fieldsWithHelp.forEach(element => {
        const helpText = element.querySelector('.form-text') || 
                        document.getElementById(element.getAttribute('aria-describedby'));
        
        if (helpText) {
            const input = element.tagName === 'INPUT' ? element : element.querySelector('input, select, textarea');
            if (input && !input.hasAttribute('data-bs-toggle')) {
                addTooltip(input, helpText.textContent, 'right');
            }
        }
    });
}

// ==========================================
// ENHANCED AJAX WITH LOADING STATES
// ==========================================

/**
 * Enhanced fetch with automatic loading spinner
 * @param {string} url - URL to fetch
 * @param {Object} options - Fetch options
 * @param {string} loadingMessage - Loading message
 * @returns {Promise} - Fetch promise
 */
async function fetchWithLoading(url, options = {}, loadingMessage = 'Loading...') {
    showLoadingSpinner(loadingMessage);
    
    try {
        const response = await fetch(url, options);
        hideLoadingSpinner();
        return response;
    } catch (error) {
        hideLoadingSpinner();
        throw error;
    }
}

/**
 * Submit form via AJAX with loading state
 * @param {HTMLFormElement} form - Form element
 * @param {Function} onSuccess - Success callback
 * @param {Function} onError - Error callback
 */
function submitFormAjax(form, onSuccess, onError) {
    if (!form) return;
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Validate form
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }
        
        const submitBtn = form.querySelector('[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
        
        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: form.method || 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                if (onSuccess) onSuccess(data);
            } else {
                if (onError) onError(data);
            }
        } catch (error) {
            console.error('Form submission error:', error);
            if (onError) onError({ success: false, message: 'An error occurred' });
        } finally {
            // Restore button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

// ==========================================
// AUTO-INITIALIZATION
// ==========================================

document.addEventListener('DOMContentLoaded', () => {
    // Initialize tooltips
    if (typeof bootstrap !== 'undefined') {
        initializeTooltips();
    }
    
    // Enhance all forms with class 'needs-validation'
    document.querySelectorAll('.needs-validation').forEach(form => {
        enhanceFormValidation(form);
    });
    
    console.log('UI enhancements initialized');
});
