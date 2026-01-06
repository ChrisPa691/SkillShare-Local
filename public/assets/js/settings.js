/**
 * Settings Page JavaScript
 * Handles AJAX form submission for preferences and immediate UI updates
 */

(function() {
    'use strict';

    /**
     * Sync theme from database to localStorage and apply immediately
     */
    function syncThemeFromDB() {
        // Get theme value from hidden input or data attribute
        const themeSelect = document.getElementById('themeMode');
        if (themeSelect) {
            const dbTheme = themeSelect.value;
            if (dbTheme) {
                localStorage.setItem('theme', dbTheme);
                applyThemeImmediate(dbTheme);
            }
        }
    }

    /**
     * Apply theme immediately without page reload
     * @param {string} theme - Theme name (light, dark, system)
     */
    function applyThemeImmediate(theme) {
        let appliedTheme = theme;
        
        if (theme === 'system') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            appliedTheme = prefersDark ? 'dark' : 'light';
        }
        
        // Apply to documentElement and body
        document.documentElement.setAttribute('data-theme', appliedTheme);
        if (document.body) {
            document.body.setAttribute('data-theme', appliedTheme);
            document.body.classList.remove('theme-light', 'theme-dark');
            document.body.classList.add('theme-' + appliedTheme);
        }
    }

    /**
     * Apply font size immediately
     * @param {number} fontSize - Font size in pixels
     */
    function applyFontSize(fontSize) {
        document.documentElement.style.fontSize = fontSize + 'px';
    }

    /**
     * Apply contrast mode immediately
     * @param {string} mode - Contrast mode (normal, high)
     */
    function applyContrastMode(mode) {
        document.body.classList.remove('contrast-normal', 'contrast-high');
        document.body.classList.add('contrast-' + mode);
    }

    /**
     * Handle preferences form submission via AJAX
     */
    function initPreferencesForm() {
        const form = document.getElementById('preferencesForm');
        const submitBtn = document.getElementById('savePreferencesBtn');
        
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Disable submit button
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            // Collect form data
            const formData = new FormData(form);

            // Send AJAX request
            fetch('settings.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Apply changes immediately without reload
                    const theme = formData.get('theme_mode');
                    const fontSize = formData.get('font_size');
                    const contrastMode = formData.get('contrast_mode');
                    const currency = formData.get('currency');

                    if (theme) {
                        applyThemeImmediate(theme);
                        localStorage.setItem('theme', theme);
                    }

                    if (fontSize) {
                        applyFontSize(fontSize);
                        localStorage.setItem('fontSize', fontSize);
                    }

                    if (contrastMode) {
                        applyContrastMode(contrastMode);
                        localStorage.setItem('contrastMode', contrastMode);
                    }
                    
                    if (currency) {
                        localStorage.setItem('preferredCurrency', currency);
                        // Update all prices on page if updateAllPrices exists
                        if (typeof updateAllPrices === 'function') {
                            updateAllPrices();
                        }
                    }

                    // Show success message
                    if (typeof showToast === 'function') {
                        showToast('Preferences updated successfully', 'success');
                    } else {
                        alert('Preferences updated successfully');
                    }
                } else {
                    // Show error message
                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Failed to update preferences', 'error');
                    } else {
                        alert(data.message || 'Failed to update preferences');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof showToast === 'function') {
                    showToast('An error occurred while saving preferences', 'error');
                } else {
                    alert('An error occurred while saving preferences');
                }
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    /**
     * Initialize real-time preview for theme changes
     */
    function initThemePreview() {
        const themeSelect = document.getElementById('themeMode');
        if (themeSelect) {
            themeSelect.addEventListener('change', function() {
                // Preview theme change immediately
                applyThemeImmediate(this.value);
            });
        }
    }

    /**
     * Initialize real-time preview for font size changes
     */
    function initFontSizePreview() {
        const fontSizeSelect = document.querySelector('select[name="font_size"]');
        if (fontSizeSelect) {
            fontSizeSelect.addEventListener('change', function() {
                // Preview font size change immediately
                applyFontSize(this.value);
            });
        }
    }

    /**
     * Initialize real-time preview for contrast mode changes
     */
    function initContrastModePreview() {
        const contrastSelect = document.querySelector('select[name="contrast_mode"]');
        if (contrastSelect) {
            contrastSelect.addEventListener('change', function() {
                // Preview contrast mode change immediately
                applyContrastMode(this.value);
            });
        }
    }

    /**
     * Initialize real-time preview for currency changes
     */
    function initCurrencyPreview() {
        const currencySelect = document.getElementById('preferredCurrency');
        if (currencySelect) {
            currencySelect.addEventListener('change', function() {
                // Update localStorage and refresh prices
                localStorage.setItem('preferredCurrency', this.value);
                if (typeof updateAllPrices === 'function') {
                    updateAllPrices();
                }
            });
        }
    }

    /**
     * Initialize on page load
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Sync theme from database on load
        syncThemeFromDB();
        
        // Initialize AJAX form submission
        initPreferencesForm();
        
        // Initialize real-time previews
        initThemePreview();
        initFontSizePreview();
        initContrastModePreview();
        initCurrencyPreview();
    });

})();
