import axios from 'axios';

// Intercept 419 responses and handle them gracefully
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 419) {
      // Option: reload the page to get a fresh CSRF token and session
      try {
        // If this is an SPA or AJAX-heavy app, you might show a modal instead
        // window.location.reload();
        // For now, return a clear error that front-end can catch
        return Promise.reject({
          ...error,
          handled: true,
          message: 'Session expired. Please refresh the page.'
        });
      } catch (e) {
        // swallow
      }
    }
    return Promise.reject(error);
  }
);

// Lightweight heartbeat to keep session alive (call this when user is active)
export function startHeartbeat(interval = 5 * 60 * 1000) {
  if (!document.querySelector('meta[name="csrf-token"]')) return;

  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  if (!token) return;

  setInterval(() => {
    axios.post('/_heartbeat', {}, { headers: { 'X-CSRF-TOKEN': token } }).catch(() => {});
  }, interval);
}

// Optionally auto-start heartbeat if a session cookie exists
if (document.cookie.includes('laravel_session')) {
  startHeartbeat();
}
