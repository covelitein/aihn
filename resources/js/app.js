import './bootstrap';
import '../css/app.scss';

// Import Bootstrap JS
import * as bootstrap from 'bootstrap';
// Expose Bootstrap to global scope for inline scripts (e.g., bootstrap.Modal, bootstrap.Toast)
window.bootstrap = bootstrap;
// Notify listeners that Bootstrap is available
try {
    document.dispatchEvent(new Event('bootstrap:ready'));
} catch (_) {
    // no-op
}

import Alpine from 'alpinejs';
import './csrf';

window.Alpine = Alpine;

Alpine.start();

function bindConfirmForms() {
	const forms = document.querySelectorAll('form[data-confirm]');
	forms.forEach(form => {
		if (form.__boundConfirm) return;
		form.__boundConfirm = true;
		form.addEventListener('submit', async (e) => {
			const msg = form.getAttribute('data-confirm') || 'Are you sure?';
			const title = form.getAttribute('data-confirm-title') || 'Please Confirm';
			if (window.AppUI) {
				e.preventDefault();
				const ok = await window.AppUI.confirm(msg, title);
				if (!ok) return;
				if (form.hasAttribute('data-remote-delete')) {
					// Submit via fetch to avoid full reload
					const action = form.getAttribute('action');
					const token = document.querySelector('meta[name="csrf-token"]').content;
					try {
						const res = await fetch(action, { method: 'DELETE', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token }});
						if (res.ok || res.status === 204) {
							if (window.AppUI) window.AppUI.showToast('Deleted', 'success');
							// Remove the closest table row
							const row = form.closest('tr');
							if (row) row.remove();
							return;
						}
					} catch (err) {
						console.error('Delete failed', err);
					}
				}
				form.submit();
			}
		});
	});
}

// Notifications UI wiring
document.addEventListener('DOMContentLoaded', () => {
    bindConfirmForms();
    const bellBtn = document.getElementById('notificationToggle');
    // Bind remote delete buttons on load and after any alpine init
    bindRemoteDeleteButtons();
	const dropdown = document.getElementById('notificationDropdown');
	const listEl = dropdown ? dropdown.querySelector('.notification-list') : null;
	const headerBadge = document.querySelector('.notification-badge');
	const headerBadgeHeader = document.querySelector('.notification-badge-header');

	async function fetchNotifications() {
		try {
			const res = await fetch('/notifications', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
			if (!res.ok) return;
			const data = await res.json();
			if (headerBadge) {
				headerBadge.textContent = data.unread > 99 ? '99+' : data.unread;
				headerBadge.style.display = data.unread > 0 ? 'flex' : 'none';
			}
			if (headerBadgeHeader) {
				headerBadgeHeader.textContent = data.unread > 0 ? `${data.unread} new` : 'No new';
				headerBadgeHeader.style.display = data.unread > 0 ? 'inline-block' : 'none';
			}
			if (listEl) {
				listEl.innerHTML = '';
				data.items.forEach(item => {
					const row = document.createElement('div');
					row.className = 'notification-item';
					row.innerHTML = `
						<div class="notification-icon ${item.read_at ? 'primary' : 'warning'}">
							<i class="bi ${item.read_at ? 'bi-bell' : 'bi-bell-fill'}"></i>
						</div>
						<div class="notification-content">
							<p class="notification-message">${item.message}</p>
							<span class="notification-time">${item.created_at}</span>
						</div>
					`;
					row.addEventListener('click', async () => {
						await fetch(`/notifications/${item.id}/read`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': getCsrf() } });
						fetchNotifications();
					});
					listEl.appendChild(row);
				});
			}
		} catch (e) {
			console.error('Failed to load notifications', e);
		}
	}

	function getCsrf() {
		const el = document.querySelector('meta[name="csrf-token"]');
		return el ? el.getAttribute('content') : '';
	}

	if (bellBtn && dropdown) {
		bellBtn.addEventListener('click', () => {
			fetchNotifications();
		});
	}

	// Periodic refresh
	setInterval(fetchNotifications, 60000);

	// Initial fetch
	fetchNotifications();
});

// Disable buttons on form submit to prevent double submits (non-AJAX)
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    const buttons = form.querySelectorAll('button, input[type=submit]');
    buttons.forEach((btn) => {
        btn.setAttribute('disabled', 'disabled');
        if (btn.dataset && !btn.dataset.originalText) {
            btn.dataset.originalText = btn.innerHTML;
        }
        if (btn.innerHTML && !btn.classList.contains('no-loading')) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + (btn.dataset.loadingText || 'Processing...');
        }
    });
}, true);

function bindRemoteDeleteButtons() {
    const buttons = document.querySelectorAll('[data-remote-delete-url]');
    buttons.forEach(btn => {
        if (btn.__boundRemoteDelete) return;
        btn.__boundRemoteDelete = true;
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const url = btn.getAttribute('data-remote-delete-url');
            const name = btn.getAttribute('data-name') || 'this item';
            const row = btn.closest('tr');
            if (!window.AppUI) {
                console.warn('AppUI.confirm not available');
                return;
            }
            const ok = await window.AppUI.confirm(`Delete ${name}?`, 'Confirm Deletion');
            if (!ok) return;
            const token = document.querySelector('meta[name="csrf-token"]').content;
            try {
                const res = await fetch(url, { method: 'DELETE', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token }});
                if (res.ok || res.status === 204) {
                    if (row) row.remove();
                    if (window.AppUI) window.AppUI.showToast('Deleted', 'success');
                } else {
                    if (window.AppUI) window.AppUI.showToast('Delete failed', 'danger');
                }
            } catch (err) {
                console.error(err);
                if (window.AppUI) window.AppUI.showToast('Network error', 'danger');
            }
        });
    });
}
