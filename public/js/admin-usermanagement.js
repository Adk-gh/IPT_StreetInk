document.addEventListener('DOMContentLoaded', function() {
    console.log('[ADMIN] DOM fully loaded, initializing user management system');

    // Initialize Bootstrap components
    const viewUserModal = new bootstrap.Modal(document.getElementById('viewUserModal'));
    const editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    const deleteUserModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    const toast = new bootstrap.Toast(document.getElementById('toast'));

    console.log('[ADMIN] Modals initialized:', {
        viewModal: viewUserModal,
        editModal: editUserModal,
        deleteModal: deleteUserModal
    });

    // Track selected user
    let selectedUserId = null;

    // Tab functionality
    const tabs = document.querySelectorAll('.tabs .tab');
    const tabContents = document.querySelectorAll('.tab-content');
    console.log('[ADMIN] Found tabs:', tabs.length, 'Tab contents:', tabContents.length);

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            console.log('[TAB] Clicked tab:', this.dataset.target);
            tabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            this.classList.add('active');
            const targetId = this.getAttribute('data-target');
            document.getElementById(targetId).classList.add('active');

            // Clear selection when switching tabs
            clearSelection();
        });
    });

    // Checkbox functionality
    document.getElementById('selectAllCheckbox')?.addEventListener('change', function() {
        console.log('[CHECKBOX] Select all changed:', this.checked);
        const checkboxes = document.querySelectorAll('.user-checkbox');

        // For select-first approach, we only allow single selection
        if (this.checked) {
            // Uncheck all others
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            // Check just the first one (or handle differently)
            if (checkboxes.length > 0) {
                checkboxes[0].checked = true;
                selectedUserId = checkboxes[0].value;
                console.log('[CHECKBOX] Selected first user ID:', selectedUserId);
            }
        } else {
            selectedUserId = null;
            console.log('[CHECKBOX] Cleared selected user ID');
        }

        updateViewButtons();
        updateSelectedCount();
    });

    // Update view buttons based on selection
    function updateViewButtons() {
        const viewButtons = document.querySelectorAll('.view-user-btn');
        console.log('[BUTTONS] Updating view buttons, selectedUserId:', selectedUserId);
        viewButtons.forEach(btn => {
            const row = btn.closest('tr');
            const checkbox = row.querySelector('.user-checkbox');

            if (checkbox && checkbox.checked) {
                btn.disabled = false;
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-primary');
                btn.title = "View selected user";
            } else {
                btn.disabled = true;
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
                btn.title = "Select user first";
            }
        });
    }

    // Update selected count
    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.user-checkbox:checked').length;
        const countElement = document.getElementById('selectedCount');
        const deleteBtn = document.getElementById('deleteSelectedBtn');
        console.log('[SELECTION] Selected users count:', selectedCount);

        if (countElement) countElement.textContent = selectedCount;
        if (deleteBtn) deleteBtn.disabled = selectedCount === 0;
    }

    // Clear all selections
    function clearSelection() {
        document.querySelectorAll('.user-checkbox').forEach(cb => {
            cb.checked = false;
        });
        selectedUserId = null;
        console.log('[SELECTION] Cleared all selections');
        updateViewButtons();
        updateSelectedCount();
    }

    // Handle checkbox changes for single selection
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('user-checkbox')) {
            console.log('[CHECKBOX] User checkbox changed:', e.target.value, e.target.checked);
            if (e.target.checked) {
                // Uncheck all other checkboxes
                document.querySelectorAll('.user-checkbox').forEach(cb => {
                    if (cb !== e.target) cb.checked = false;
                });
                selectedUserId = e.target.value;
                console.log('[CHECKBOX] Set selectedUserId to:', selectedUserId);
            } else {
                selectedUserId = null;
                console.log('[CHECKBOX] Cleared selectedUserId');
            }
            updateViewButtons();
            updateSelectedCount();
        }
    });

    // Event delegation for user actions
    document.addEventListener('click', async function(e) {
        // View User - now requires selection first
        if (e.target.closest('.view-user-btn')) {
            console.log('[USER] View button clicked, selectedUserId:', selectedUserId);
            if (!selectedUserId) {
                console.warn('[WARNING] No user selected for viewing');
                showToast('Please select a user first', 'warning');
                return;
            }

            try {
                console.log('[API] Fetching user data for ID:', selectedUserId);
                const userData = await fetchUserData(selectedUserId);
                console.log('[API] Received user data:', userData);

                if (!userData) {
                    console.error('[ERROR] No user data received for ID:', selectedUserId);
                    throw new Error('No user data received');
                }

                populateViewModal(userData);
                viewUserModal.show();
                console.log('[MODAL] View modal shown for user:', selectedUserId);
            } catch (error) {
                console.error('[ERROR] View user error:', error);
                showToast(error.message || 'Failed to load user data', 'danger');
            }
        }

        // Edit User - still works with direct click
        if (e.target.closest('.edit-user-btn')) {
            const userId = e.target.closest('.edit-user-btn').dataset.userId;
            console.log('[USER] Edit button clicked for user ID:', userId);

            try {
                const userData = await fetchUserData(userId);
                console.log('[API] Received user data for edit:', userData);

                if (!userData) {
                    console.error('[ERROR] No user data received for edit');
                    throw new Error('No user data received');
                }

                populateEditForm(userData);
                editUserModal.show();
                console.log('[MODAL] Edit modal shown for user:', userId);
            } catch (error) {
                console.error('[ERROR] Edit user error:', error);
                showToast(error.message || 'Failed to load user data', 'danger');
            }
        }

        // Delete User - still works with direct click
        if (e.target.closest('.delete-user-btn')) {
            const userId = e.target.closest('.delete-user-btn').dataset.userId;
            console.log('[USER] Delete button clicked for user ID:', userId);

            document.getElementById('deleteUserForm').action = `/admin/users/${userId}`;
            console.log('[FORM] Delete form action set to:', `/admin/users/${userId}`);
            deleteUserModal.show();
        }
    });

    // Bulk Delete
    document.getElementById('deleteSelectedBtn')?.addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.user-checkbox:checked'))
                               .map(checkbox => checkbox.value);
        console.log('[BULK] Delete selected clicked. IDs:', selectedIds);

        if (selectedIds.length === 0) {
            console.warn('[WARNING] No users selected for deletion');
            showToast('Please select at least one user to delete', 'warning');
            return;
        }

        if (confirm(`Are you sure you want to delete ${selectedIds.length} selected users?`)) {
            console.log('[BULK] Confirmed deletion of', selectedIds.length, 'users');
            deleteUsers(selectedIds);
        }
    });

    // Form submissions
    document.getElementById('editUserForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('[FORM] Edit user form submitted');
        await handleFormSubmit(this, 'PUT', 'User updated successfully!');
    });

    document.getElementById('deleteUserForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('[FORM] Delete user form submitted');
        await handleFormSubmit(this, 'DELETE', 'User deactivated successfully!');
    });

    // Search functionality
    document.getElementById('userSearch')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        console.log('[SEARCH] Searching for:', searchTerm);

        const rows = document.querySelectorAll('#usersTableBody tr');
        rows.forEach(row => {
            const name = row.querySelector('td:nth-child(2) .fw-bold')?.textContent.toLowerCase() || '';
            const email = row.querySelector('td:nth-child(2) .text-muted')?.textContent.toLowerCase() || '';
            const location = row.querySelector('td:nth-child(6)')?.textContent.toLowerCase() || '';

            if (name.includes(searchTerm) || email.includes(searchTerm) || location.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
                // Also uncheck if hidden
                const checkbox = row.querySelector('.user-checkbox');
                if (checkbox && checkbox.checked) {
                    checkbox.checked = false;
                    updateViewButtons();
                    updateSelectedCount();
                }
            }
        });
    });
});

// Fetch single user data
async function fetchUserData(userId) {
    try {
        const response = await fetch(`/admin/users/${userId}`, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin' // Important for sessions/cookies
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Failed to fetch user');
        }

        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}

// Populate view modal
function populateViewModal(userData) {
    console.log('[MODAL] Populating view modal with:', userData);

    // Basic info
    document.getElementById('viewUserName').textContent = userData?.name || 'N/A';
    document.getElementById('viewUserEmail').textContent = userData?.email || 'N/A';
    document.getElementById('viewUserPhone').textContent = userData?.phone || 'N/A';
    document.getElementById('viewUserLocation').textContent = userData?.location || 'Unknown';
    document.getElementById('viewUserBio').textContent = userData?.bio || 'No bio provided';

    // Dates
    document.getElementById('viewUserCreatedAt').textContent =
        userData?.created_at ? new Date(userData.created_at).toLocaleDateString() : 'Unknown';
    document.getElementById('viewUserLastActive').textContent =
        userData?.last_active_at ? new Date(userData.last_active_at).toLocaleString() : 'Never';

    // Counts
    document.getElementById('viewUserArtworks').textContent = userData?.artworks_count || 0;

    // Role and status
    const role = userData?.role || 'user';
    document.getElementById('viewUserRole').textContent = role.charAt(0).toUpperCase() + role.slice(1);

    const status = userData?.status || 'inactive';
    const statusBadge = document.getElementById('viewUserStatus');
    statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    statusBadge.className = 'badge ' + (
        status === 'active' ? 'bg-success' :
        status === 'banned' ? 'bg-danger' : 'bg-warning'
    );

    // Avatar
    const avatar = document.getElementById('viewUserAvatar');
    avatar.src = userData?.profile_picture ? `/storage/${userData.profile_picture}` : '/img/default.jpg';
    console.log('[MODAL] View modal populated');
}

// Populate edit form
function populateEditForm(userData) {
    console.log('[FORM] Populating edit form with:', userData);

    document.getElementById('editName').value = userData?.name || '';
    document.getElementById('editEmail').value = userData?.email || '';
    document.getElementById('editPhone').value = userData?.phone || '';
    document.getElementById('editLocation').value = userData?.location || '';
    document.getElementById('editBio').value = userData?.bio || '';
    document.getElementById('editRole').value = userData?.role || 'user';
    document.getElementById('editStatus').value = userData?.status || 'active';
    document.getElementById('editVerifiedArtist').checked = userData?.verified_artist || false;

    // Avatar
    const avatar = document.getElementById('editUserAvatar');
    avatar.src = userData?.profile_picture ? `/storage/${userData.profile_picture}` : '/img/default.jpg';

    // Update form action
    document.getElementById('editUserForm').action = `/admin/users/${userData?.user_id || ''}`;
    console.log('[FORM] Edit form populated');
}

// Handle form submissions
async function handleFormSubmit(form, method, successMessage) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const spinner = submitBtn.querySelector('.spinner-border');
    console.log('[FORM] Handling submit for:', form.id, 'Method:', method);

    try {
        submitBtn.disabled = true;
        if (spinner) spinner.classList.remove('d-none');

        const formData = new FormData(form);
        console.log('[FORM] Form data:', Object.fromEntries(formData));

        const response = await fetch(form.action, {
            method: method === 'DELETE' ? 'DELETE' : 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: method === 'DELETE' ? null : formData
        });
        console.log('[FORM] Response status:', response.status);

        if (!response.ok) {
            const errorData = await response.json();
            console.error('[FORM] Error response:', errorData);
            let errorMessage = errorData.message || 'Action failed';
            if (errorData.errors) {
                errorMessage = Object.values(errorData.errors).flat().join(', ');
            }
            throw new Error(errorMessage);
        }

        const data = await response.json();
        console.log('[FORM] Success response:', data);
        showToast(successMessage, 'success');

        setTimeout(() => {
            bootstrap.Modal.getInstance(form.closest('.modal'))?.hide();
            console.log('[MODAL] Closing modal after successful submission');
            location.reload();
        }, 1500);
    } catch (error) {
        console.error('[FORM] Submission error:', error);
        showToast(error.message, 'danger');
    } finally {
        submitBtn.disabled = false;
        if (spinner) spinner.classList.add('d-none');
    }
}

// Bulk delete users
async function deleteUsers(userIds) {
    console.log('[BULK] Deleting users with IDs:', userIds);
    try {
        const response = await fetch('/admin/users/bulk-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ ids: userIds })
        });
        console.log('[BULK] Response status:', response.status);

        if (!response.ok) {
            const errorData = await response.json();
            console.error('[BULK] Error response:', errorData);
            throw new Error(errorData.message || 'Bulk delete failed');
        }

        const data = await response.json();
        console.log('[BULK] Success response:', data);
        showToast(`${userIds.length} users deleted successfully`, 'success');
        location.reload();
    } catch (error) {
        console.error('[BULK] Delete error:', error);
        showToast(error.message, 'danger');
    }
}

// Show toast notification
function showToast(message, type = 'success') {
    console.log('[TOAST] Showing toast:', type, message);
    const toastEl = document.getElementById('toast');
    const toastBody = toastEl.querySelector('.toast-body');

    toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
    toastBody.textContent = message;

    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}
