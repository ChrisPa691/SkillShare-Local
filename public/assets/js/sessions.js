/**
 * Sessions JavaScript - Session Create, Edit, and View Pages
 * Handles session form interactions and utilities
 */

// ====================================
// SESSION FORM FUNCTIONS (Create & Edit)
// ====================================

function initSessionForm() {
    const feeTypeRadios = document.querySelectorAll('input[name="fee_type"]');
    const feeAmountContainer = document.getElementById('fee_amount_container');
    const feeAmountInput = document.getElementById('fee_amount');
    
    const locationTypeRadios = document.querySelectorAll('input[name="location_type"]');
    const onlineLinkContainer = document.getElementById('online_link_container');
    const onlineLinkInput = document.getElementById('online_link');
    const inpersonContainer = document.getElementById('inperson_location_container');
    const cityInput = document.getElementById('city');
    const addressInput = document.getElementById('address');
    
    // Fee type toggle function
    function toggleFeeAmount() {
        const checkedRadio = document.querySelector('input[name="fee_type"]:checked');
        if (!checkedRadio) return;
        
        const isPaid = checkedRadio.value === 'paid';
        if (feeAmountContainer) {
            feeAmountContainer.style.display = isPaid ? 'block' : 'none';
        }
        if (feeAmountInput) {
            feeAmountInput.required = isPaid;
            feeAmountInput.disabled = !isPaid; // Disable when not paid to prevent validation
            if (!isPaid) {
                feeAmountInput.value = '';
            }
        }
    }
    
    // Location type toggle function
    function toggleLocation() {
        const checkedRadio = document.querySelector('input[name="location_type"]:checked');
        if (!checkedRadio) return;
        
        const isOnline = checkedRadio.value === 'online';
        
        if (onlineLinkContainer) {
            onlineLinkContainer.style.display = isOnline ? 'block' : 'none';
        }
        if (inpersonContainer) {
            inpersonContainer.style.display = isOnline ? 'none' : 'block';
        }
        
        if (onlineLinkInput) {
            onlineLinkInput.required = isOnline;
            onlineLinkInput.disabled = !isOnline; // Disable when not online
        }
        if (cityInput) {
            cityInput.required = !isOnline;
            cityInput.disabled = isOnline; // Disable when online
        }
        if (addressInput) {
            addressInput.required = !isOnline;
            addressInput.disabled = isOnline; // Disable when online
        }
        
        // Clear unused fields
        if (isOnline) {
            if (cityInput) cityInput.value = '';
            if (addressInput) addressInput.value = '';
        } else {
            if (onlineLinkInput) onlineLinkInput.value = '';
        }
    }
    
    // Event listeners for fee type
    if (feeTypeRadios.length > 0) {
        feeTypeRadios.forEach(radio => radio.addEventListener('change', toggleFeeAmount));
    }
        feeAmountInput.disabled = true; // Default to disabled
    }
    if (onlineLinkInput) {
        onlineLinkInput.required = true; // Default to online
        onlineLinkInput.disabled = false;
    }
    if (cityInput) {
        cityInput.required = false;
        cityInput.disabled = true;
    }
    if (addressInput) {
        addressInput.required = false;
        addressInput.disabled = trulse; // Default to not required
    }
    if (onlineLinkInput) {
        onlineLinkInput.required = true; // Default to online
    }
    if (cityInput) {
        cityInput.required = false;
    }
    if (addressInput) {
        addressInput.required = false;
    }
    
    // Apply initial toggles based on current selections
    toggleFeeAmount();
    toggleLocation();
    
    // Form validation for create session
    const createForm = document.getElementById('createSessionForm');
    if (createForm) {
        createForm.addEventListener('submit', function(e) {
            const eventDateTime = document.getElementById('event_datetime').value;
            const now = new Date();
            const selectedDate = new Date(eventDateTime);
            
            if (selectedDate <= now) {
                e.preventDefault();
                alert('Event date must be in the future');
                return false;
            }
        });
    }
    
    // Form validation for edit session
    const editForm = document.getElementById('editSessionForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            const eventDateTime = document.getElementById('event_datetime').value;
            const now = new Date();
            const selectedDate = new Date(eventDateTime);
            
            if (selectedDate <= now) {
                e.preventDefault();
                alert('Event date must be in the future');
                return false;
            }
        });
    }
}

// ====================================
// SESSION VIEW FUNCTIONS
// ====================================

function initSessionView() {
    // Copy to clipboard function is available globally
    // No initialization needed
}

// Copy to clipboard function (used in session view)
function copyToClipboard() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('Link copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy:', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Link copied to clipboard!');
        } catch (err) {
            alert('Failed to copy link');
        }
        document.body.removeChild(textArea);
    });
}

// ====================================
// PAGE INITIALIZATION
// ====================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize session form if create or edit form exists
    if (document.getElementById('createSessionForm') || document.getElementById('editSessionForm')) {
        initSessionForm();
    }
    
    // Initialize session view if on view page
    if (document.querySelector('.session-view-page')) {
        initSessionView();
    }
});
