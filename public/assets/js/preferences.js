/**
 * ==========================================
 * CURRENCY UTILITIES
 * ==========================================
 * Helper functions for currency formatting and management
 */

/**
 * Get user's preferred currency from localStorage
 * @returns {string} Currency code (e.g., 'USD', 'GBP')
 */
function getPreferredCurrency() {
    return localStorage.getItem('preferredCurrency') || 'GBP';
}

/**
 * Set user's preferred currency
 * @param {string} currency - Currency code
 * @param {boolean} saveToDb - Whether to save to database (default: true)
 */
function setPreferredCurrency(currency, saveToDb = true) {
    const allowedCurrencies = ['GBP', 'USD', 'EUR', 'CAD', 'AUD', 'JPY', 'INR'];
    
    if (allowedCurrencies.includes(currency)) {
        localStorage.setItem('preferredCurrency', currency);
        
        // Save to database if requested
        if (saveToDb) {
            savePreferenceToDb('user.currency', currency);
        }
        
        return true;
    }
    
    return false;
}

/**
 * Currency symbols mapping
 */
const currencySymbols = {
    'GBP': '£',
    'USD': '$',
    'EUR': '€',
    'CAD': 'C$',
    'AUD': 'A$',
    'JPY': '¥',
    'INR': '₹'
};

/**
 * Format a price in the user's preferred currency
 * @param {number} amount - The price amount
 * @param {string} [currency] - Optional currency override
 * @returns {string} Formatted price string
 */
function formatPrice(amount, currency) {
    currency = currency || getPreferredCurrency();
    
    try {
        const formatter = new Intl.NumberFormat('en-GB', {
            style: 'currency',
            currency: currency,
            minimumFractionDigits: currency === 'JPY' ? 0 : 2,
            maximumFractionDigits: currency === 'JPY' ? 0 : 2
        });
        
        return formatter.format(amount);
    } catch (e) {
        // Fallback if Intl.NumberFormat fails
        const symbol = currencySymbols[currency] || '£';
        if (currency === 'JPY') {
            return symbol + Math.round(amount).toLocaleString();
        }
        return symbol + parseFloat(amount).toFixed(2);
    }
}

/**
 * Update all price elements on the page
 * Looks for elements with data-amount attribute
 */
function updateAllPrices() {
    const priceElements = document.querySelectorAll('[data-amount]');
    
    priceElements.forEach(element => {
        const amount = parseFloat(element.getAttribute('data-amount'));
        const currency = element.getAttribute('data-currency');
        
        if (!isNaN(amount)) {
            element.textContent = formatPrice(amount, currency);
        }
    });
}

/**
 * Get currency symbol
 * @param {string} [currency] - Currency code, defaults to user preference
 * @returns {string} Currency symbol
 */
function getCurrencySymbol(currency) {
    currency = currency || getPreferredCurrency();
    return currencySymbols[currency] || '£';
}


/**
 * ==========================================
 * THEME UTILITIES
 * ==========================================
 * Helper functions for theme management
 */

/**
 * Get user's preferred theme from localStorage
 * @returns {string} Theme name ('light', 'dark', or 'auto')
 */
function getPreferredTheme() {
    return localStorage.getItem('theme') || 'light';
}

/**
 * Set user's preferred theme
 * @param {string} theme - Theme name ('light', 'dark', or 'system')
 * @param {boolean} saveToDb - Whether to save to database (default: true)
 */
function setPreferredTheme(theme, saveToDb = true) {
    const allowedThemes = ['light', 'dark', 'system'];
    
    if (allowedThemes.includes(theme)) {
        localStorage.setItem('theme', theme);
        applyTheme(theme);
        
        // Save to database if requested
        if (saveToDb) {
            savePreferenceToDb('theme', theme);
        }
        
        return true;
    }
    
    return false;
}

/**
 * Apply theme to the page
 * @param {string} theme - Theme name ('light', 'dark', or 'auto')
 */
function applyTheme(theme) {
    const body = document.body;
    
    // Resolve 'auto' to actual theme
    if (theme === 'auto') {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        theme = prefersDark ? 'dark' : 'light';
    }
    
    // Remove all theme classes
    body.classList.remove('theme-light', 'theme-dark');
    
    // Add the appropriate theme class
    body.classList.add(`theme-${theme}`);
    
    // Update data attribute for CSS targeting
    body.setAttribute('data-theme', theme);
}

/**
 * Toggle between light and dark theme
 * @returns {string} New theme name
 */
function toggleTheme() {
    const currentTheme = getPreferredTheme();
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    setPreferredTheme(newTheme);
    return newTheme;
}

/**
 * Get system color scheme preference
 * @returns {string} 'light' or 'dark'
 */
function getSystemTheme() {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    return prefersDark ? 'dark' : 'light';
}

/**
 * Initialize theme on page load
 * Call this as early as possible to prevent flash
 */
function initializeTheme() {
    const savedTheme = getPreferredTheme();
    applyTheme(savedTheme);
}


/**
 * ==========================================
 * TOAST NOTIFICATIONS
 * ==========================================
 * Simple toast notifications for user feedback
 */

/**
 * Show a toast notification
 * @param {string} message - Message to display
 * @param {string} [type='info'] - Toast type: 'success', 'error', 'warning', 'info'
 * @param {number} [duration=3000] - Duration in milliseconds
 */
function showToast(message, type = 'info', duration = 3000) {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.custom-toast');
    existingToasts.forEach(toast => toast.remove());
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `custom-toast toast-${type}`;
    
    // Icon based on type
    const icons = {
        'success': '<i class="fas fa-check-circle"></i>',
        'error': '<i class="fas fa-exclamation-circle"></i>',
        'warning': '<i class="fas fa-exclamation-triangle"></i>',
        'info': '<i class="fas fa-info-circle"></i>'
    };
    
    toast.innerHTML = `
        ${icons[type] || icons.info}
        <span>${message}</span>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to page
    document.body.appendChild(toast);
    
    // Trigger animation
    setTimeout(() => toast.classList.add('show'), 10);
    
    // Auto-remove
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}


/**
 * ==========================================
 * LOCAL STORAGE UTILITIES
 * ==========================================
 * Helper functions for localStorage management
 */

/**
 * Get item from localStorage with JSON parsing
 * @param {string} key - Storage key
 * @param {*} [defaultValue] - Default value if key doesn't exist
 * @returns {*} Stored value or default
 */
function getStorageItem(key, defaultValue = null) {
    try {
        const item = localStorage.getItem(key);
        return item ? JSON.parse(item) : defaultValue;
    } catch (e) {
        return defaultValue;
    }
}

/**
 * Set item in localStorage with JSON stringification
 * @param {string} key - Storage key
 * @param {*} value - Value to store
 */
function setStorageItem(key, value) {
    try {
        localStorage.setItem(key, JSON.stringify(value));
        return true;
    } catch (e) {
        console.error('Failed to save to localStorage:', e);
        return false;
    }
}

/**
 * Remove item from localStorage
 * @param {string} key - Storage key
 */
function removeStorageItem(key) {
    try {
        localStorage.removeItem(key);
        return true;
    } catch (e) {
        return false;
    }
}

/**
 * Clear all items from localStorage
 */
function clearStorage() {
    try {
        localStorage.clear();
        return true;
    } catch (e) {
        return false;
    }
}


/**
 * ==========================================
 * PAGE PREFERENCES
 * ==========================================
 * Helper functions for page-specific preferences
 */

/**
 * Get items per page preference
 * @returns {number} Number of items per page
 */
function getItemsPerPage() {
    return parseInt(localStorage.getItem('itemsPerPage')) || 10;
}

/**
 * Set items per page preference
 * @param {number} count - Number of items per page
 */
function setItemsPerPage(count) {
    localStorage.setItem('itemsPerPage', count);
}

/**
 * Get show impact badges preference
 * @returns {boolean} Whether to show impact badges
 */
function getShowImpactBadges() {
    const value = localStorage.getItem('showImpactBadges');
    return value === null ? true : value === 'true';
}

/**
 * Set show impact badges preference
 * @param {boolean} show - Whether to show impact badges
 */
function setShowImpactBadges(show) {
    localStorage.setItem('showImpactBadges', show ? 'true' : 'false');
}


/**
 * ==========================================
 * INITIALIZATION
 * ==========================================
 * Auto-initialize on page load
 */

// Initialize theme as early as possible (also in header.php inline script)
if (document.readyState === 'loading') {
    initializeTheme();
} else {
    // DOM already loaded
    initializeTheme();
}

// Initialize other features when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Sync preferences from database on page load
    syncPreferencesFromDb();
    
    // Update prices if elements exist
    updateAllPrices();
    
    // Initialize currency selector if it exists
    const currencySelect = document.getElementById('preferredCurrency');
    if (currencySelect) {
        const savedCurrency = getPreferredCurrency();
        currencySelect.value = savedCurrency;
        
        // Listen for currency changes
        currencySelect.addEventListener('change', function() {
            const newCurrency = this.value;
            setPreferredCurrency(newCurrency, true); // true = save to database
            updateAllPrices();
            
            // Show confirmation
            if (typeof showToast === 'function') {
                showToast('Currency preference saved to ' + newCurrency, 'success');
            }
            
            console.log('Currency changed to:', newCurrency);
        });
    }
    
    // Initialize theme selector if it exists
    const themeSelect = document.getElementById('themeMode');
    if (themeSelect) {
        const savedTheme = getPreferredTheme();
        themeSelect.value = savedTheme;
        
        // Listen for theme changes
        themeSelect.addEventListener('change', function() {
            const newTheme = this.value;
            setPreferredTheme(newTheme, true); // true = save to database
            
            // Show confirmation
            if (typeof showToast === 'function') {
                showToast('Theme changed to ' + newTheme + ' mode', 'success');
            }
            
            console.log('Theme changed to:', newTheme);
        });
    }
    
    // Listen for system theme changes (for auto mode)
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        const currentTheme = getPreferredTheme();
        if (currentTheme === 'auto') {
            applyTheme('auto');
        }
    });
    
    // Listen for storage changes from other tabs
    window.addEventListener('storage', function(e) {
        if (e.key === 'preferredCurrency') {
            updateAllPrices();
        }
        
        if (e.key === 'theme') {
            applyTheme(e.newValue);
        }
    });
    /*DATABASE SYNC
    * ==========================================
    * Functions for syncing preferences with database
    */

    /**
     * Save a preference to the database via AJAX
 * @param {string} key - Setting key (e.g., 'theme', 'language')
     */
    function savePreferenceToDb(key, value) {
    return fetch('ajax/save_preference.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ key: key, value: value })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('Failed to save preference to database:', data.message);
        }
        return data;
    })
    .catch(error => {
        console.error('Error saving preference:', error);
        return { success: false, error: error.message };
    });
}

/**
 * Load user preferences from database
 * @returns {Promise} Promise that resolves with preferences object
 */
function loadPreferencesFromDb() {
    return fetch('ajax/get_user_preferences.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.preferences) {
                return data.preferences;
            }
            return null;
        })
        .catch(error => {
            console.error('Error loading preferences:', error);
            return null;
        });
}

/**
 * Sync localStorage with database preferences
 * Call this on page load to ensure consistency
 */
function syncPreferencesFromDb() {
    loadPreferencesFromDb().then(prefs => {
        if (prefs) {
            // Update localStorage with database values
            if (prefs.theme) {
                localStorage.setItem('theme', prefs.theme);
                applyTheme(prefs.theme);
            }
            if (prefs.language) {
                localStorage.setItem('language', prefs.language);
            }
            if (prefs.currency) {
                localStorage.setItem('preferredCurrency', prefs.currency);
            }
            if (prefs.font_size) {
                localStorage.setItem('fontSize', prefs.font_size);
            }
            
            // Update UI if elements exist
            updateAllPrices();
            
            console.log('Preferences synced from database');
        }
    });
}});

/**
 * ==========================================
 * EXPORT FOR ES6 MODULES (OPTIONAL)
 * ==========================================
 */

// If using ES6 modules, uncomment this:
/*
export {
    // Currency
    getPreferredCurrency,
    setPreferredCurrency,
    formatPrice,
    updateAllPrices,
    getCurrencySymbol,
    
    // Theme
    getPreferredTheme,
    setPreferredTheme,
    applyTheme,
    toggleTheme,
    getSystemTheme,
    initializeTheme,
    
    // Notifications
    showToast,
    
    // Storage
    getStorageItem,
    setStorageItem,
    removeStorageItem,
    clearStorage,
    
    // Preferences
    getItemsPerPage,
    setItemsPerPage,
    getShowImpactBadges,
    setShowImpactBadges,
    
    // Database Sync
    savePreferenceToDb,
    loadPreferencesFromDb,
    syncPreferencesFromDb
    clearStorage,
    
    // Preferences
    getItemsPerPage,
    setItemsPerPage,
    getShowImpactBadges,
    setShowImpactBadges
};
*/
