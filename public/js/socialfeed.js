document.addEventListener('DOMContentLoaded', () => {
    // Toast Notification Function
    function showToast(message, isError = false) {
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.backgroundColor = isError ? '#e63946' : '#28a745';
        toast.style.color = 'white';
        toast.style.padding = '10px 20px';
        toast.style.borderRadius = '5px';
        toast.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
        toast.style.zIndex = '10000';
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';

        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '1'; }, 100);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.addEventListener('transitionend', () => toast.remove());
        }, 3000);
    }

    // Function to get user ID
    function getUserId() {
        const userIdMeta = document.querySelector('meta[name="user-id"]');
        return userIdMeta ? userIdMeta.getAttribute('content') : 'unknown';
    }

    // Like Button Functionality (unchanged)
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const postId = button.getAttribute('data-post-id');
            const isLiked = button.getAttribute('data-liked') === 'true';
            const url = isLiked ? `/posts/${postId}/unlike` : `/posts/${postId}/like`;
            const method = isLiked ? 'DELETE' : 'POST';

            if (!postId) {
                console.error('Error: Missing post ID for like/unlike action');
               
                return;
            }

            console.log(`Initiating ${isLiked ? 'unlike' : 'like'} for post ${postId}, user ${getUserId()}`);

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    const likeCountSpan = button.querySelector('.like-count');
                    likeCountSpan.textContent = data.likes_count;

                    const icon = button.querySelector('i');
                    if (data.liked) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        button.setAttribute('data-liked', 'true');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        button.setAttribute('data-liked', 'false');
                    }

                    button.style.transform = 'scale(1.2)';
                    setTimeout(() => button.style.transform = 'scale(1)', 300);

                    console.log(`Success: ${data.message}, Likes: ${data.likes_count}, Liked: ${data.liked}`);
                } else {
                    console.error(`Error: ${data.message || 'Unknown error'}`);
                    showToast(`Error ${data.message || 'Failed to process like action'}`, true);
                }
            } catch (error) {
                console.error(`Fetch error: ${error.message}`);
                showToast('Failed to process like/unlike action', true);
            }
        });
    });

    // Mobile Menu Toggle (unchanged)
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const nav = document.getElementById('nav');
    if (mobileMenuBtn && nav) {
        mobileMenuBtn.addEventListener('click', () => {
            nav.classList.toggle('active');
            mobileMenuBtn.innerHTML = nav.classList.contains('active') ?
                '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        });
    }

    // Header Scroll Effect (unchanged)
    const header = document.getElementById('header');
    if (header) {
        window.addEventListener('scroll', () => {
            header.classList.toggle('scrolled', window.scrollY > 100);
        });
    }

    // Smooth Scrolling for Anchor Links (unchanged)
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = anchor.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });

                if (nav && nav.classList.contains('active')) {
                    nav.classList.remove('active');
                    mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
                }
            }
        });
    });

    // Feed Tabs (unchanged)
    document.querySelectorAll('.feed-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.feed-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            console.log(`Feed tab switched to ${tab.textContent}`);
        });
    });

    // Dark Mode Toggle (unchanged)
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const newTheme = body.dataset.theme === 'dark' ? 'light' : 'dark';
            body.dataset.theme = newTheme;
            localStorage.setItem('theme', newTheme);
            themeToggle.innerHTML = newTheme === 'dark' ?
                '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
            console.log(`Theme toggled to ${newTheme}`);
        });

        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            body.dataset.theme = savedTheme;
            themeToggle.innerHTML = savedTheme === 'dark' ?
                '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
        }
    }

    // Share Button Functionality (unchanged)
    document.querySelectorAll('.share-btn').forEach(button => {
        button.addEventListener('click', () => {
            console.log('Share button clicked');
            showToast('Share functionality would open a dialog with sharing options.');
            button.style.transform = 'scale(1.2)';
            setTimeout(() => button.style.transform = 'scale(1)', 300);
        });
    });

    // Save Button Functionality (unchanged)
    document.querySelectorAll('.save-btn').forEach(button => {
        button.addEventListener('click', () => {
            const icon = button.querySelector('i');
            const isSaved = button.classList.contains('saved');

            button.classList.toggle('saved');
            icon.classList.toggle('fas', !isSaved);
            icon.classList.toggle('far', isSaved);
            console.log(isSaved ? 'Post unsaved' : 'Post saved');

            button.style.transform = 'scale(1.2)';
            setTimeout(() => button.style.transform = 'scale(1)', 300);
        });
    });

    // Follow Button Functionality (unchanged)
    document.querySelectorAll('.follow-btn').forEach(button => {
        button.addEventListener('click', () => {
            const isFollowing = button.classList.contains('following');
            button.classList.toggle('following');
            button.textContent = isFollowing ? 'Follow' : 'Following';
            console.log(isFollowing ? 'User unfollowed' : 'User followed');

            button.style.transform = 'scale(1.1)';
            setTimeout(() => button.style.transform = 'scale(1)', 300);
        });
    });

    // Comment Submission (unchanged)
    document.querySelectorAll('.add-comment').forEach(form => {
        const input = form.querySelector('.comment-input');
        const submit = form.querySelector('.comment-submit');

        if (submit) {
            submit.addEventListener('click', () => {
                if (input.value.trim()) {
                    console.log(`Comment submitted: ${input.value}`);
                    input.value = '';
                }
            });
        }

        if (input) {
            input.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && input.value.trim()) {
                    console.log(`Comment submitted via Enter: ${input.value}`);
                    input.value = '';
                }
            });
        }
    });

    // Comment Button Toggle (unchanged)
    document.querySelectorAll('.comment-btn').forEach(button => {
        button.addEventListener('click', () => {
            const postCard = button.closest('.post-card');
            const commentsSection = postCard.querySelector('.comments-section');
            if (commentsSection) {
                commentsSection.style.display = commentsSection.style.display === 'none' || !commentsSection.style.display ? 'block' : 'none';
            }
        });
    });

    // Comments Modal Functionality (unchanged)
    document.querySelectorAll('.show-comments-btn').forEach(button => {
        button.addEventListener('click', () => {
            const postId = button.getAttribute('data-post-id');
            const modal = document.getElementById(`comments-modal-${postId}`);
            const overlay = document.querySelector('.modal-overlay');
            if (modal && overlay) {
                modal.style.display = 'block';
                overlay.style.display = 'block';
                console.log(`Comments modal opened for post ${postId}`);
            }
        });
    });

    document.querySelectorAll('.close-modal').forEach(button => {
        button.addEventListener('click', () => {
            const modal = button.closest('.custom-modal');
            const overlay = document.querySelector('.modal-overlay');
            if (modal && overlay) {
                modal.style.display = 'none';
                overlay.style.display = 'none';
                console.log('Comments modal closed');
            }
        });
    });

    // Post Options Menu (unchanged)
    document.querySelectorAll('.post-options').forEach(option => {
        option.addEventListener('click', () => {
            console.log('Post options menu clicked');
            showToast('Post options menu would appear with options to report, save, or edit post.');
        });
    });

    // Clipboard Copy for Post URLs (unchanged)
    function copyPostUrl(postId) {
        const url = `${window.location.origin}/posts/${postId}`;
        navigator.clipboard.writeText(url).then(() => {
            showToast('Post link copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy:', err);
            showToast('Failed to copy link', true);
        });
    }

    // Tags Checkbox Functionality
    const checkboxes = document.querySelectorAll('.tag-checkbox');
    const selectedTagsInput = document.getElementById('selectedTags');
    if (checkboxes && selectedTagsInput) {
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const selected = Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value.replace(/^#/, '')); // Strip # from tags
                selectedTagsInput.value = selected.join(',');
                updateTagsPreview(selected);
                console.log(`Selected tags: ${selected.join(',')}`);
            });
        });
    }

    // Tags Dropdown Toggle
    const tagsButton = document.getElementById('tagsButton');
    const tagsDropdown = document.getElementById('tagsDropdown');
    if (tagsButton && tagsDropdown) {
        tagsButton.addEventListener('click', (e) => {
            e.preventDefault();
            tagsDropdown.style.display = tagsDropdown.style.display === 'block' ? 'none' : 'block';
            console.log(tagsDropdown.style.display === 'block' ? 'Tags dropdown opened' : 'Tags dropdown closed');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!tagsButton.contains(e.target) && !tagsDropdown.contains(e.target)) {
                tagsDropdown.style.display = 'none';
                console.log('Tags dropdown closed by clicking outside');
            }
        });
    }

    // Update Tags Preview
    function updateTagsPreview(tagsArray) {
        const tagsPreview = document.getElementById('tagsPreview');
        if (!tagsPreview || !selectedTagsInput) {
            console.error('tagsPreview or selectedTagsInput not found');
            return;
        }

        if (tagsArray.length === 0) {
            tagsPreview.style.display = 'none';
            tagsPreview.innerHTML = '';
            selectedTagsInput.value = '';
            return;
        }

        tagsPreview.style.display = 'block';
        tagsPreview.innerHTML = tagsArray.map(tag => `<span style="background:#e63946; color:#fff; padding: 3px 8px; border-radius: 12px; margin-right: 5px;">${tag}</span>`).join('');
        selectedTagsInput.value = tagsArray.join(',');
    }

    // Create Post Functionality with AJAX
    const postSubmitBtn = document.getElementById('postSubmitBtn');
    if (postSubmitBtn) {
        postSubmitBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            const form = document.getElementById('postForm');
            if (!form) {
                console.error('Form not found for post creation');
                showToast('Error: Form not found', true);
                return;
            }

            const captionInput = form.querySelector('.post-input');
            const photoInput = document.getElementById('photoInput');
            const locationNameInput = document.getElementById('location_name');
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            const selectedTagsInput = document.getElementById('selectedTags');

            if (!captionInput || !captionInput.value.trim()) {
                showToast('Please enter a caption for your post', true);
                return;
            }

            if (!photoInput || !photoInput.files[0]) {
                showToast('Please upload an image for your post', true);
                return;
            }

            const formData = new FormData();
            formData.append('caption', captionInput.value.trim());
            formData.append('image_url', photoInput.files[0]);
            if (locationNameInput && locationNameInput.value) {
                formData.append('location_name', locationNameInput.value);
            }
            if (latitudeInput && latitudeInput.value) {
                formData.append('latitude', latitudeInput.value);
            }
            if (longitudeInput && longitudeInput.value) {
                formData.append('longitude', longitudeInput.value);
            }
            if (selectedTagsInput && selectedTagsInput.value) {
                selectedTagsInput.value.split(',').forEach(tag => {
                    const cleanTag = tag.trim().replace(/^#/, '');
                    if (cleanTag) formData.append('tags[]', cleanTag);
                });
            }
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            // Log FormData for debugging
            for (let [key, value] of formData.entries()) {
                console.log(`FormData: ${key} = ${value}`);
            }

            try {
                const response = await fetch('/posts', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (response.ok) {
                    showToast('Post created successfully!');
                    captionInput.value = '';
                    if (photoInput) photoInput.value = '';
                    if (locationNameInput) locationNameInput.value = '';
                    if (latitudeInput) latitudeInput.value = '';
                    if (longitudeInput) longitudeInput.value = '';
                    if (selectedTagsInput) {
                        selectedTagsInput.value = '';
                        checkboxes.forEach(cb => cb.checked = false);
                        updateTagsPreview([]);
                    }
                    const previewContainer = document.getElementById('photoPreviewContainer');
                    if (previewContainer) previewContainer.style.display = 'none';
                    const locationPreview = document.getElementById('locationPreview');
                    if (locationPreview) {
                        locationPreview.style.display = 'none';
                        locationPreview.textContent = '';
                    }
                    console.log('Post created:', data);
                } else {
                    console.error(`Error: ${data.message || 'Failed to create post'}`);
                    showToast(`Error: ${data.message || 'Failed to create post'}`, true);
                }
            } catch (error) {
                console.error(`Fetch error: ${error.message}`);
                showToast('Failed to create post. Please try again.', true);
            }
        });
    }

    // Photo Upload Preview (unchanged)
    const photoInput = document.getElementById('photoInput');
    if (photoInput) {
        photoInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            const previewContainer = document.getElementById('photoPreviewContainer');
            const previewImage = document.getElementById('photoPreview');

            if (!previewContainer || !previewImage) {
                console.error('Photo preview elements not found');
                showToast('Error: Photo preview elements not found', true);
                return;
            }

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    previewImage.src = event.target.result;
                    previewContainer.style.display = 'block';
                    console.log(`Photo selected: ${file.name}`);
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
                console.log('No valid image selected');
            }
        });
    }

    // Map Location Functionality (unchanged)
    const locationToggle = document.getElementById('locationToggle');
    const locationModal = document.getElementById('locationModal');
    const closeModal = document.getElementById('closeModal');
    const saveLocation = document.getElementById('saveLocation');
    const clearLocation = document.getElementById('clearLocation');
    const locationNameInput = document.getElementById('location_name');
    let map, marker;
    let mapInitialized = false;

    if (locationToggle) {
        locationToggle.addEventListener('click', (e) => {
            e.preventDefault();
            if (locationModal) {
                locationModal.style.display = 'flex';
                console.log('Location modal opened');
                if (!mapInitialized) {
                    initializeMap();
                    mapInitialized = true;
                }
            } else {
                console.error('Location modal not found');
                showToast('Error: Location modal not found', true);
            }
        });
    }

    if (closeModal) {
        closeModal.addEventListener('click', (e) => {
            e.preventDefault();
            if (locationModal) {
                locationModal.style.display = 'none';
                console.log('Location modal closed');
            }
        });
    }

    if (locationModal) {
        locationModal.addEventListener('click', (e) => {
            if (e.target === locationModal) {
                locationModal.style.display = 'none';
                console.log('Location modal closed by clicking outside');
            }
        });
    }

    if (saveLocation) {
        saveLocation.addEventListener('click', (e) => {
            e.preventDefault();
            if (locationModal && locationNameInput) {
                locationModal.style.display = 'none';
                const locationName = locationNameInput.value;
                const latitudeInput = document.getElementById('latitude');
                const longitudeInput = document.getElementById('longitude');
                const lat = latitudeInput ? latitudeInput.value : '';
                const lng = longitudeInput ? longitudeInput.value : '';
                console.log(`Location saved: ${locationName}, Lat: ${lat}, Lng: ${lng}`);
                const locationPreview = document.getElementById('locationPreview');
                if (locationPreview) {
                    if (locationName) {
                        locationPreview.textContent = locationName;
                        locationPreview.style.display = 'block';
                    } else {
                        locationPreview.style.display = 'none';
                    }
                }
            } else {
                console.error('Location inputs not found');
                showToast('Error: Location inputs not found', true);
            }
        });
    }

    if (clearLocation) {
        clearLocation.addEventListener('click', (e) => {
            e.preventDefault();
            if (marker && map) {
                map.removeLayer(marker);
                marker = null;
            }
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            if (latitudeInput) latitudeInput.value = '';
            if (longitudeInput) longitudeInput.value = '';
            if (locationNameInput) locationNameInput.value = '';
            const locationPreview = document.getElementById('locationPreview');
            if (locationPreview) {
                locationPreview.style.display = 'none';
                locationPreview.textContent = '';
            }
            if (map) {
                map.setView([14.0555, 121.3250], 13);
            }
            console.log('Location cleared');
        });
    }

    function initializeMap() {
        const mapContainer = document.getElementById('map');
        if (!mapContainer) {
            console.error('Map container not found');
            showToast('Error: Map container not found', true);
            return;
        }
        if (typeof L === 'undefined') {
            console.error('Leaflet library not loaded');
            showToast('Map functionality unavailable', true);
            return;
        }
        map = L.map('map').setView([14.0555, 121.3250], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        map.on('click', (e) => {
            const { lat, lng } = e.latlng;
            placeMarker([lat, lng]);
            reverseGeocode(lat, lng);
            console.log(`Map clicked: Lat ${lat.toFixed(4)}, Lng ${lng.toFixed(4)}`);
        });
    }

    function placeMarker(coords) {
        if (!map) return;
        if (marker) {
            marker.setLatLng(coords);
        } else {
            marker = L.marker(coords, { draggable: true }).addTo(map);
            marker.on('dragend', (event) => {
                const position = event.target.getLatLng();
                updateLatLng(position.lat, position.lng);
                reverseGeocode(position.lat, position.lng);
                console.log(`Marker dragged: Lat ${position.lat.toFixed(4)}, Lng ${position.lng.toFixed(4)}`);
            });
        }
        updateLatLng(coords[0], coords[1]);
        map.panTo(coords);
    }

    function updateLatLng(lat, lng) {
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        if (latitudeInput) latitudeInput.value = lat;
        if (longitudeInput) longitudeInput.value = lng;
    }

    async function reverseGeocode(lat, lng) {
        if (!locationNameInput) {
            console.error('Location name input not found');
            showToast('Error: Location name input not found', true);
            return;
        }
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await response.json();
            locationNameInput.value = data.display_name || `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
            console.log(`Reverse geocoded: ${locationNameInput.value}`);
        } catch (error) {
            locationNameInput.value = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
            console.error(`Geocoding error: ${error.message}`);
            showToast('Failed to fetch location name', true);
        }
    }

    // Image Modal (unchanged)
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        const modalImg = document.getElementById('modalImage');
        const closeBtn = imageModal.querySelector('.close-modal');

        document.querySelectorAll('.clickable-image').forEach(img => {
            img.addEventListener('click', () => {
                imageModal.style.display = 'flex';
                if (modalImg) {
                    modalImg.src = img.src;
                    console.log(`Image modal opened for: ${img.src}`);
                }
            });
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                imageModal.style.display = 'none';
                if (modalImg) modalImg.src = '';
                console.log('Image modal closed');
            });
        }

        imageModal.addEventListener('click', (e) => {
            if (e.target === imageModal) {
                imageModal.style.display = 'none';
                if (modalImg) modalImg.src = '';
                console.log('Image modal closed by clicking outside');
            }
        });
    }

    // Show Success Toast from Session (unchanged)
    const successInput = document.getElementById('postSuccessMessage');
    if (successInput && successInput.value) {
        showToast(successInput.value);
        console.log(`Post success message: ${successInput.value}`);
    }

    // Passive Touchstart Listener (unchanged)
    document.addEventListener('touchstart', () => {}, { passive: true });
});
