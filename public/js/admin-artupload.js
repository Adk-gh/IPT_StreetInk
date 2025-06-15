document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebar = document.getElementById('sidebar');

    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            mobileMenuBtn.innerHTML = sidebar.classList.contains('active') ?
                '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        });
    }

    // Theme Toggle
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const newTheme = body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            themeToggle.innerHTML = newTheme === 'dark' ?
                '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        });

        // Initialize theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        body.setAttribute('data-theme', savedTheme);
        themeToggle.innerHTML = savedTheme === 'dark' ?
            '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    }

    // View Toggle
    const tableViewBtn = document.getElementById('tableViewBtn');
    const gridViewBtn = document.getElementById('gridViewBtn');
    const tableView = document.getElementById('tableView');
    const gridView = document.getElementById('gridView');

    if (tableViewBtn && gridViewBtn && tableView && gridView) {
        tableViewBtn.addEventListener('click', () => {
            tableViewBtn.classList.add('active');
            gridViewBtn.classList.remove('active');
            tableView.style.display = 'block';
            gridView.style.display = 'none';
        });

        gridViewBtn.addEventListener('click', () => {
            gridViewBtn.classList.add('active');
            tableViewBtn.classList.remove('active');
            gridView.style.display = 'grid';
            tableView.style.display = 'none';
        });

        // Initialize view (show table by default)
        tableViewBtn.classList.add('active');
        tableView.style.display = 'block';
        gridView.style.display = 'none';
    }

    // Checkbox functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllHeaderCheckbox = document.getElementById('selectAllHeader');
    const postCheckboxes = document.querySelectorAll('.post-checkbox');

    // Select All checkbox in header
    if (selectAllHeaderCheckbox) {
        selectAllHeaderCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            postCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            selectAllCheckbox.checked = isChecked;
        });
    }

    // Select All checkbox in bulk actions
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            postCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            if (selectAllHeaderCheckbox) {
                selectAllHeaderCheckbox.checked = isChecked;
            }
        });
    }

    // Individual checkbox behavior
    postCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(postCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(postCheckboxes).some(cb => cb.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
            
            if (selectAllHeaderCheckbox) {
                selectAllHeaderCheckbox.checked = allChecked;
                selectAllHeaderCheckbox.indeterminate = someChecked && !allChecked;
            }
        });
    });

    // Delete Selected button functionality
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            const selectedRows = document.querySelectorAll('table tbody tr:has(.post-checkbox:checked)');
            const selectedIds = Array.from(selectedRows).map(row => row.dataset.postId);
            
            if (selectedIds.length === 0) {
                alert('Please select at least one post to delete');
                return;
            }
            
            if (confirm(`Are you sure you want to delete ${selectedIds.length} selected posts?`)) {
                // Show loading indicator
                const loadingIndicator = document.createElement('div');
                loadingIndicator.className = 'loading-overlay';
                loadingIndicator.innerHTML = '<div class="loading-spinner"></div>';
                document.body.appendChild(loadingIndicator);
                
                // Send AJAX request to delete posts
                fetch('/admin/posts/bulk-delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ postIds: selectedIds })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Selected posts deleted successfully');
                        location.reload(); // Refresh the page
                    } else {
                        alert('Error deleting posts: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting posts');
                })
                .finally(() => {
                    // Remove loading indicator
                    document.body.removeChild(loadingIndicator);
                });
            }
        });
    }

    // View buttons functionality
    document.querySelectorAll('.action-btn.view').forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.dataset.postId;
            
            // Show loading indicator
            const loadingIndicator = document.createElement('div');
            loadingIndicator.className = 'loading-overlay';
            loadingIndicator.innerHTML = '<div class="loading-spinner"></div>';
            document.body.appendChild(loadingIndicator);
            
            // Fetch post details via AJAX
            fetch(`/api/posts/${postId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateModal(data.post);
                    } else {
                        alert('Failed to load post details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading post details');
                })
                .finally(() => {
                    // Remove loading indicator
                    document.body.removeChild(loadingIndicator);
                });
        });
    });

    // Modal close button
    window.closeArtModal = function() {
        document.getElementById('artModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    };

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === document.getElementById('artModal')) {
            closeArtModal();
        }
    });

    // Function to populate modal with post data
    function populateModal(post) {
        document.getElementById('modalArtTitle').textContent = post.caption || 'Untitled';
        
        // Set image
        const imgElement = document.getElementById('modalArtImage');
        if (post.image_url) {
            imgElement.src = `/storage/${post.image_url}`;
            imgElement.style.display = 'block';
        } else {
            imgElement.style.display = 'none';
        }
        
        // Set artist info
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
        
        // Set other details
        document.getElementById('modalLocation').textContent = post.location_name || 'Unknown';
        document.getElementById('modalCoordinates').textContent = 
            post.latitude && post.longitude ? 
            `${post.latitude}° N, ${post.longitude}° E` : 'Not specified';
        document.getElementById('modalUploadDate').textContent = 
            new Date(post.created_at).toLocaleString();
        
        // Set tags
        const tagsElement = document.getElementById('modalTags');
        tagsElement.innerHTML = '';
        if (post.tags) {
            post.tags.split(',').forEach(tag => {
                const tagElement = document.createElement('span');
                tagElement.className = 'tag';
                tagElement.textContent = `#${tag.trim()}`;
                tagsElement.appendChild(tagElement);
            });
        }
        
        // Set description
        document.getElementById('modalDescription').textContent = post.caption || 'No description provided';
        
        // Open modal
        document.getElementById('artModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Notification button
    const notificationBtn = document.querySelector('.notification-btn');
    if (notificationBtn) {
        notificationBtn.addEventListener('click', () => {
            alert('Notification center would open here showing recent system alerts and messages.');
        });
    }

    // User dropdown
    const userMenu = document.querySelector('.user-menu');
    const userDropdown = document.querySelector('.user-dropdown');

    if (userMenu && userDropdown) {
        userMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });

        document.addEventListener('click', () => {
            userDropdown.classList.remove('active');
        });
    }
});