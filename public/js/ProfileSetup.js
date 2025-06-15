document.addEventListener('DOMContentLoaded', function() {
    // Modal Functionality
    const modalOverlay = document.getElementById('modalOverlay');
    const modalClose = document.getElementById('modalClose');
    const openModalBtn = document.getElementById('openModalBtn');

    if (openModalBtn) {
        openModalBtn.addEventListener('click', () => {
            modalOverlay.style.display = 'flex';
        });
    }

    if (modalClose) {
        modalClose.addEventListener('click', () => {
            modalOverlay.style.display = 'none';
        });
    }

    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                modalOverlay.style.display = 'none';
            }
        });
    }

    // Step Navigation
    const steps = document.querySelectorAll('.step');
    const formSteps = document.querySelectorAll('.form-step');
    const progressBar = document.getElementById('progressBar');
    const nextBtn1 = document.getElementById('nextBtn1');
    const nextBtn2 = document.getElementById('nextBtn2');
    const backBtn1 = document.getElementById('backBtn1');
    const backBtn2 = document.getElementById('backBtn2');

    function updateStep(stepNumber) {
        steps.forEach((step, index) => {
            if (index < stepNumber) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });

        formSteps.forEach((step, index) => {
            if (index === stepNumber - 1) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });

        if (progressBar) {
            progressBar.style.width = `${(stepNumber / 3) * 100}%`;
        }
    }

    if (nextBtn1) {
        nextBtn1.addEventListener('click', () => {
            const displayName = document.getElementById('displayName').value;
            if (!displayName) {
                showNotification('Please enter your display name');
                return;
            }
            updateStep(2);
        });
    }

    if (nextBtn2) nextBtn2.addEventListener('click', () => updateStep(3));
    if (backBtn1) backBtn1.addEventListener('click', () => updateStep(1));
    if (backBtn2) backBtn2.addEventListener('click', () => updateStep(2));

    // Enhanced Image Upload Functionality
    function setupImageUpload(uploadBtnId, inputId, previewId, imageId, removeBtnId, removeHiddenId) {
        const uploadBtn = document.getElementById(uploadBtnId);
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        const image = document.getElementById(imageId);
        const removeBtn = document.getElementById(removeBtnId);
        const removeHiddenField = document.getElementById(removeHiddenId);
        const placeholder = preview.querySelector('.avatar-placeholder i, i');

        if (!uploadBtn || !input || !preview || !image || !removeBtn || !placeholder) return;

        // Initialize with existing image
        if (image.src && image.src !== window.location.href) {
            image.style.display = 'block';
            placeholder.style.display = 'none';
            removeBtn.style.display = 'inline-flex';
        }

        uploadBtn.addEventListener('click', () => input.click());

        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    image.src = e.target.result;
                    image.style.display = 'block';
                    placeholder.style.display = 'none';
                    removeBtn.style.display = 'inline-flex';
                    if (removeHiddenField) removeHiddenField.value = '0';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        removeBtn.addEventListener('click', function() {
            input.value = '';
            image.src = '';
            image.style.display = 'none';
            placeholder.style.display = 'block';
            removeBtn.style.display = 'none';
            if (removeHiddenField) removeHiddenField.value = '1';
        });
    }

    // Initialize image uploaders
    setupImageUpload('uploadBtn', 'avatarInput', 'avatarPreview', 'avatarImage', 'removeBtn', 'removeProfilePicture');
    setupImageUpload('uploadBannerBtn', 'bannerInput', 'bannerPreview', 'bannerImage', 'removeBannerBtn', 'removeCoverPhoto');

    // Tag Selection with Limit
    const tagCheckboxes = document.querySelectorAll('input[name="style-tag"], input[name="style_tag[]"]');
    const tagCounter = document.getElementById('tagCounter');
    const maxTags = 3;

    if (tagCheckboxes.length && tagCounter) {
        // Initialize counter
        const checkedCount = document.querySelectorAll('input[name="style-tag"]:checked, input[name="style_tag[]"]:checked').length;
        tagCounter.textContent = `${checkedCount}/${maxTags} tags selected`;

        tagCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCount = document.querySelectorAll('input[name="style-tag"]:checked, input[name="style_tag[]"]:checked').length;
                tagCounter.textContent = `${checkedCount}/${maxTags} tags selected`;

                if (checkedCount >= maxTags) {
                    tagCheckboxes.forEach(cb => {
                        if (!cb.checked) cb.disabled = true;
                    });
                    tagCounter.style.color = 'var(--accent)';
                    tagCounter.style.fontWeight = '600';
                } else {
                    tagCheckboxes.forEach(cb => cb.disabled = false);
                    tagCounter.style.color = 'var(--text-light)';
                    tagCounter.style.fontWeight = 'normal';
                }
            });
        });
    }

    // Form Submission
    const artIdentityForm = document.getElementById('profileForm');
    if (artIdentityForm) {
        artIdentityForm.addEventListener('submit', function(e) {
            // Validation can be added here if needed
            showNotification('Profile setup complete!');
        });
    }

    // Notification Function
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Initialize date picker
    flatpickr("#birthdate", {
        theme: "none",
        dateFormat: "Y-m-d",
        maxDate: "today",
        disableMobile: true,
        nextArrow: '<i class="fas fa-chevron-right"></i>',
        prevArrow: '<i class="fas fa-chevron-left"></i>',
        defaultDate: "{{ Auth::user()->birthdate ?? '' }}"
    });
});