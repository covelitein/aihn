import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// CSRF token header
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Global response handler for AJAX requests
window.axios.interceptors.response.use(
    function (response) {
        // If the server returns a flash message in headers or data, optionally show it
        return response;
    },
    function (error) {
        if (error.response) {
            if (error.response.status === 419) {
                // CSRF/token expired - prompt reload
                alert('Your session has expired. Please refresh and try again.');
            }
            if (error.response.status === 422 && error.response.data?.errors) {
                // Validation errors - let page components render them; also scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
        return Promise.reject(error);
    }
);
