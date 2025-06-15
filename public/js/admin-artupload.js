document.addEventListener('DOMContentLoaded', function() {
    console.log('Art uploads management initialized');

    // Initialize Bootstrap components
    const artModal = new bootstrap.Modal(document.getElementById('artModal'));
    const toast = new bootstrap.Toast(document.getElementById('toast')); // Assume a toast element exists

    // View Toggle
    const tableViewBtn = document.getElementById('tableViewBtn');
    const gridViewBtn = document.getElementById('gridViewBtn');
    const tableView = document.getElementById('tableView');
    const gridView = document.getElementById('gridView');

    tableViewBtn?.addEventListener('click', function() {
        tableViewBtn.classList.add('active');
        gridViewBtn.classList.remove('active');
        tableView.style.display = 'block';
        gridView.style.display = 'none';
    });

    gridViewBtn?.addEventListener('click', function() {
        gridViewBtn.classList.add('active');
        tableViewBtn.classList.remove('active');
        tableView.style.display = 'none';
        gridView.style.display = 'grid';
    });

    // Select All Checkbox
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.posts-table input[type="checkbox"]');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        updateSelectedCount();
    });

    // Update selected count
    document.querySelectorAll('.posts-table input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.posts-table input[type="checkbox"]:checked').length;
        const deleteBtn = document.querySelector('.bulk-actions .btn.btn-secondary.btn-sm');
        if (deleteBtn) deleteBtn.disabled = selectedCount === 0;
    }

    // Event delegation for post actions
    document.addEventListener('click', async function(e) {
        // Helper to get postId safely
        const getPostId = (selector) => {
            const btn = e.target.closest(selector);
            if (!btn || !btn.dataset.postId) {
                console.warn(`No valid ${selector} button or postId found`);
                return null;
            }
            return btn.dataset.postId;
        };

        // View Post
        if (e.target.closest('.action-btn.view')) {
            const postId = getPostId('.action-btn.view');
            if (!postId) {
                showToast('Invalid post selection', 'danger');
                return;
            }
            console.log(`Viewing post ID: ${postId}`);
            try {
                const postData = await fetchPostData(postId);
                if (!postData) throw new Error('No post data received');
                populateArtModal(postData);
                artModal.show();
            } catch (error) {
                console.error('View post error:', error);
                showToast(error.message || 'Failed to load post data', 'danger');
            }
        }

        // Approve Post
        if (e.target.closest('.action-btn.approve')) {
            const postId = getPostId('.action-btn.approve');
            if (!postId) {
                showToast('Invalid post selection', 'danger');
                return;
            }
            console.log(`Approving post ID: ${postId}`);
            try {
                await updatePostStatus(postId, 'approved');
                showToast('Post approved successfully', 'success');
                location.reload();
            } catch (error) {
                console.error('Approve post error:', error);
                showToast(error.message || 'Failed to approve post', 'danger');
            }
        }

        // Reject Post
        if (e.target.closest('.action-btn.reject')) {
            const postId = getPostId('.action-btn.reject');
            if (!postId) {
                showToast('Invalid post selection', 'danger');
                return;
            }
            console.log(`Rejecting post ID: ${postId}`);
            try {
                await updatePostStatus(postId, 'rejected');
                showToast('Post rejected successfully', 'success');
                location.reload();
            } catch (error) {
                console.error('Reject post error:', error);
                showToast(error.message || 'Failed to reject post', 'danger');
            }
        }
    });

    // Bulk Delete
    document.querySelector('.bulk-actions .btn.btn-secondary.btn-sm')?.addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.posts-table input[type="checkbox"]:checked'))
                               .map(checkbox => checkbox.value);
        if (selectedIds.length === 0) {
            showToast('Please select at least one post to delete', 'warning');
            return;
        }
        if (confirm(`Are you sure you want to delete ${selectedIds.length} selected posts?`)) {
            deletePosts(selectedIds);
        }
    });
});

// Fetch single post data from API
async function fetchPostData(postId) {
    try {
        const response = await fetch(`/admin/posts/${postId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Failed to fetch post data');
        }
        const data = await response.json();
        if (!data || !data.data) {
            throw new Error('Invalid post data structure received');
        }
        return data.data;
    } catch (error) {
        console.error('Error fetching post data:', error);
        throw error;
    }
}

// Populate art modal with post data
function populateArtModal(post) {
    document.getElementById('modalArtTitle').textContent = post.caption || 'Untitled';
    document.getElementById('modalArtImage').src = post.image_url ?
        `/storage/${post.image_url}` : '/img/default-artwork.jpg';

    document.getElementById('modalArtist').innerHTML = `
        <div class="user-cell">
            <div class="user-avatar-sm">
                <img src="${post.user.profile_picture ? `/storage/${post.user.profile_picture}` : '/img/default.jpg'}" alt="User Avatar">
            </div>
            <div class="user-info">
                <div class="user-name-sm">${post.user.name || 'Unknown'}</div>
                <div class="user-email">${post.user.email || 'unknown'}</div>
            </div>
        </div>
    `;

    document.getElementById('modalLocation').textContent = post.location_name || 'Unknown';
    document.getElementById('modalCoordinates').textContent =
        post.latitude && post.longitude ? `${post.latitude}° N, ${post.longitude}° E` : 'Not specified';
    document.getElementById('modalUploadDate').textContent =
        new Date(post.created_at).toLocaleString();

    const statusElement = document.getElementById('modalStatus');
    let statusClass = 'pending';
    let statusText = 'Pending';
    if (post.is_featured) {
        statusClass = 'featured';
        statusText = 'Featured';
    } else if (post.is_approved) {
        statusClass = 'approved';
        statusText = 'Approved';
    } else if (post.status === 'rejected') {
        statusClass = 'rejected';
        statusText = 'Rejected';
    }
    statusElement.innerHTML = `<span class="status ${statusClass}">${statusText}</span>`;

    document.getElementById('modalTags').innerHTML = `
        <span class="tag">#${post.shared_by ? 'shared' : 'original'}</span>
        ${post.image_url ? '<span class="tag">#photo</span>' : ''}
        ${post.tags ? post.tags.split(',').map(tag => `<span class="tag">#${tag.trim()}</span>`).join('') : ''}
    `;

    document.getElementById('modalDescription').textContent = post.description || 'No description provided';
    document.getElementById('modalReports').textContent = post.reports_count || 'No reports';

    ['Approve', 'Feature', 'Edit', 'Reject'].forEach(action => {
        const btn = document.getElementById(`modal${action}Btn`);
        if (btn) btn.dataset.postId = post.id;
    });
}

// Update post status
async function updatePostStatus(postId, status) {
    try {
        const response = await fetch(`/admin/posts/${postId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status })
        });
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || `Failed to ${status} post`);
        }
    } catch (error) {
        console.error(`Error updating post status to ${status}:`, error);
        throw error;
    }
}

// Bulk delete posts
async function deletePosts(postIds) {
    try {
        const response = await fetch('/admin/posts/bulk-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ids: postIds })
        });
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Bulk delete failed');
        }
        showToast(`${postIds.length} posts deleted successfully`, 'success');
        location.reload();
    } catch (error) {
        console.error('Bulk delete error:', error);
        showToast(error.message, 'danger');
    }
}

// Show toast notification (assumes toast element exists)
function showToast(message, type = 'success') {
    const toastEl = document.getElementById('toast');
    if (!toastEl) {
        console.warn('Toast element not found');
        return;
    }
    const toastBody = toastEl.querySelector('.toast-body');
    toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
    toastBody.textContent = message;
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
}

document.addEventListener('DOMContentLoaded', () => {
    const viewButtons = document.querySelectorAll('.action-btn.view');
    const approveButtons = document.querySelectorAll('.action-btn.approve');
    const rejectButtons = document.querySelectorAll('.action-btn.reject');

    // View artwork details
    viewButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const postId = button.getAttribute('data-post-id');
            try {
                // Placeholder for API call to fetch artwork details
                const response = await fetch(`/api/artworks/${postId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        // Add authorization header if needed, e.g., 'Authorization': `Bearer ${token}`
                    }
                });
                if (!response.ok) throw new Error('Failed to fetch artwork details');
                const artwork = await response.json();

                // Display artwork details (e.g., in a modal)
                showArtworkModal(artwork);
            } catch (error) {
                console.error('Error viewing artwork:', error);
                alert('Failed to load artwork details. Please try again.');
            }
        });
    });

    // Approve artwork
    approveButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const postId = button.getAttribute('data-post-id');
            if (!confirm('Are you sure you want to approve this artwork?')) return;

            try {
                const response = await fetch(`/api/artworks/${postId}/approve`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        // Add authorization header if needed
                    }
                });
                if (!response.ok) throw new Error('Failed to approve artwork');
                alert('Artwork approved successfully!');
                button.closest('tr')?.remove(); // Remove row from UI (adjust selector as needed)
            } catch (error) {
                console.error('Error approving artwork:', error);
                alert('Failed to approve artwork. Please try again.');
            }
        });
    });

    // Reject artwork
    rejectButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const postId = button.getAttribute('data-post-id');
            if (!confirm('Are you sure you want to reject this artwork?')) return;

            try {
                const response = await fetch(`/api/artworks/${postId}/reject`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        // Add authorization header if needed
                    }
                });
                if (!response.ok) throw new Error('Failed to reject artwork');
                alert('Artwork rejected successfully!');
                button.closest('tr')?.remove(); // Remove row from UI (adjust selector as needed)
            } catch (error) {
                console.error('Error rejecting artwork:', error);
                alert('Failed to reject artwork. Please try again.');
            }
        });
    });

    // Function to display artwork details in a modal
    function showArtworkModal(artwork) {
        // Create modal dynamically (simplified example)
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <h2>Artwork Details</h2>
                <p><strong>ID:</strong> ${artwork.id}</p>
                <p><strong>Title:</strong> ${artwork.title || 'N/A'}</p>
                <p><strong>Artist:</strong> ${artwork.artist || 'N/A'}</p>
                <img src="${artwork.imageUrl || ''}" alt="Artwork" style="max-width: 300px; max-height: 300px;">
                <button onclick="this.closest('.modal').remove()">Close</button>
            </div>
        `;
        document.body.appendChild(modal);

        // Basic modal styling (add to your CSS)
        const style = document.createElement('style');
        style.textContent = `
            .modal {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1000;
            }
            .modal-content {
                background: white;
                padding: 20px;
                border-radius: 5px;
                max-width: 500px;
                width: 90%;
            }
        `;
        document.head.appendChild(style);
    }
});

// Function to open modal with post data
function openArtModal(postId) {
    // Show loading state
    showLoading();

    // Fetch post details via AJAX
    fetch(`/admin/posts/${postId}`)
        .then(response => response.json())
        .then(post => {
            // Populate modal with post data
            document.getElementById('modalArtTitle').textContent = post.caption || 'Untitled';
            document.getElementById('modalArtImage').src = post.image_url || '/img/default-artwork.jpg';

            // Set the post ID on delete button
            document.getElementById('modalDeleteBtn').dataset.postId = postId;

            // Populate other fields
            document.getElementById('modalArtist').textContent = post.user?.name || 'Unknown';
            document.getElementById('modalLocation').textContent = post.location_name || 'Unknown';
            document.getElementById('modalUploadDate').textContent =
                new Date(post.created_at).toLocaleString();
            document.getElementById('modalDescription').textContent =
                post.description || 'No description provided';

            // Show modal
            document.getElementById('artModal').style.display = 'block';
            hideLoading();
        })
        .catch(error => {
            console.error('Error fetching post:', error);
            hideLoading();
            alert('Failed to load post details');
        });
}

// Function to confirm post deletion
function confirmDeletePost() {
    const postId = document.getElementById('modalDeleteBtn').dataset.postId;
    if (confirm('Are you sure you want to delete this artwork? This action cannot be undone.')) {
        deletePost(postId);
    }
}

// Function to delete a post
function deletePost(postId) {
    showLoading();

    fetch(`/admin/posts/${postId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        hideLoading();
        alert(data.message || 'Artwork deleted successfully');
        closeArtModal();
        // Refresh the page to update the list
        window.location.reload();
    })
    .catch(error => {
        hideLoading();
        console.error('Error deleting artwork:', error);
        alert('Failed to delete artwork: ' + error.message);
    });
}

// Function to close modal
function closeArtModal() {
    document.getElementById('artModal').style.display = 'none';
}

// Event listeners for view buttons
document.addEventListener('DOMContentLoaded', function() {
    // For table view
    document.querySelectorAll('.action-btn.view').forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.dataset.postId;
            openArtModal(postId);
        });
    });

    // For grid view
    document.querySelectorAll('.art-card .action-btn.view').forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.dataset.postId;
            openArtModal(postId);
        });
    });

    // Close modal when clicking outside content
    document.getElementById('artModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeArtModal();
        }
    });
});
// Add this to your DOMContentLoaded event listener
document.querySelectorAll('.action-btn.delete').forEach(btn => {
    btn.addEventListener('click', function() {
        const postId = this.dataset.postId;
        if (confirm('Are you sure you want to delete this artwork?')) {
            deletePost(postId);
        }
    });
});
