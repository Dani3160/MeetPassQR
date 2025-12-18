import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.baseURL = '/api';

// Import jQuery first and make it globally available
import $ from 'jquery';
// Ensure jQuery is available globally before Select2 loads
window.$ = window.jQuery = $;

// Set up global for UMD modules
if (typeof global === 'undefined') {
    window.global = window;
}

// Import Select2 - the UMD wrapper should detect jQuery from window
// Store module reference for manual initialization if needed
import select2Factory from 'select2/dist/js/select2.js';
window.select2Module = select2Factory;

// Manually call the factory function
// Select2 exports: module.exports = function(root, jQuery) { factory(jQuery); return jQuery; }
if (typeof select2Factory === 'function') {
    // Call with window and jQuery - this executes the factory
    const result = select2Factory(window, window.jQuery);
    if (result && typeof result.fn === 'object') {
        // Factory returned jQuery with select2 attached
        window.jQuery = result;
        window.$ = result;
    }
}

// Also do side-effect import to trigger UMD wrapper (fallback)
import 'select2';

// Initialize Select2 loaded flag
window.select2Loaded = false;

// Check if Select2 is attached
const checkSelect2 = () => {
    if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
        window.select2Loaded = true;
        return true;
    }
    return false;
};

// Check immediately and after delay
if (!checkSelect2()) {
    setTimeout(() => {
        checkSelect2();
    }, 200);
}

