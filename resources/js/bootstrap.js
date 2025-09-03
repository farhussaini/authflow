import axios from 'axios';

// Attach axios globally
window.axios = axios;

// Laravel expects this header for AJAX
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Send cookies with every request (important for session-based auth)
window.axios.defaults.withCredentials = true;

// Optional: get CSRF token from meta tag if using forms
const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.warn('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}