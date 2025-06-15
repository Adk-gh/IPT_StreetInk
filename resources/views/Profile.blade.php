<?php
 $isEditing = isset($isEditing) ? $isEditing : false;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ Auth::user()->name }} | Street & Ink Profile</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link href="{{ asset('css/profile.css') }}" rel="stylesheet">
    <script src="{{ asset('js/profile.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- #region -->
<link href="{{ asset('css/loading.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header id="header">
        @include('header')

    </header>

    <!-- Profile Banner -->
    <section class="profile-banner">
    <!-- Cover Photo -->
    <img src="{{ Auth::user()->cover_photo ? asset('storage/' . Auth::user()->cover_photo) : 'https://images.unsplash.com/photo-1516617442634-75371039cb3a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1374&q=80' }}"
     alt="Profile Banner" class="banner-image" id="bannerImage">


<div class="profile-actions dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="profileActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-cog"></i> Profile Actions
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileActionsDropdown">
        <li><a class="dropdown-item" href="{{ route('profile.setup') }}"><i class="fas fa-user-plus me-2"></i>Setup Profile</a></li>
        <li><hr class="dropdown-divider"></li>

    </ul>
</div>


    <!-- Banner Overlay -->
    <div class="banner-overlay">
        <div class="profile-header">
            <!-- Avatar -->
            <div class="profile-avatar-wrapper">
                <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : 'img/default.jpg' }}"
                     alt="{{ Auth::user()->username }}" class="profile-avatar" id="avatarImage">
            </div>

            <!-- Profile Info -->
            <div class="profile-info">
                <!-- Name -->
                <div class="profile-name-container">
                    <h1 class="profile-name" id="profileName">{{ Auth::user()->username }}</h1>

                </div>

                <!-- Username and Verification -->
                <div class="profile-username">
                    @if (Auth::user()->verified)
                        <i class="fas fa-check-circle verified-badge" aria-label="Verified"></i>
                    @endif
                </div>

                <!-- Bio -->
                <div class="profile-bio-container">
                    <p class="profile-bio" id="profileBio">{{ Auth::user()->bio ?? 'Add bio' }}</p>

                </div>

                <!-- Location -->
                <div class="profile-location-container">
                    <div class="profile-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span id="profileLocation">{{ Auth::user()->location ?? 'Manila, Philippines' }}</span>
                    </div>

                </div>

              <!-- Update the Social Links section to include Facebook and Twitter: -->
<div class="social-links">
    @if (Auth::user()->facebook)
        <a href="{{ Auth::user()->facebook }}" class="social-link" target="_blank" aria-label="Facebook">
            <i class="fab fa-facebook-f"></i>
        </a>
    @endif
    @if (Auth::user()->twitter)
        <a href="{{ Auth::user()->twitter }}" class="social-link" target="_blank" aria-label="Twitter">
            <i class="fab fa-twitter"></i>
        </a>
    @endif
    @if (Auth::user()->instagram)
        <a href="{{ Auth::user()->instagram }}" class="social-link" target="_blank" aria-label="Instagram">
            <i class="fab fa-instagram"></i>
        </a>
    @endif
    @if (Auth::user()->tiktok)
        <a href="{{ Auth::user()->tiktok }}" class="social-link" target="_blank" aria-label="TikTok">
            <i class="fab fa-tiktok"></i>
        </a>
    @endif
    @if (Auth::user()->website)
        <a href="{{ Auth::user()->website }}" class="social-link" target="_blank" aria-label="Website">
            <i class="fas fa-globe"></i>
        </a>
    @endif
</div>

    <!-- Edit Form Overlay -->
    <div class="edit-form-overlay" id="editFormOverlay"></div>

</section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">

<div class="stat-item">
    <div class="stat-number">{{ $user->artworks_count ?? 0 }}</div>
    <div class="stat-label">Artworks Uploaded</div>
</div>

<div class="stat-item">
    <div class="stat-number">10.2k</div>
    <div class="stat-label">Total Followers</div>
</div>

<div class="stat-item">
    <div class="stat-number">10.2k</div>
    <div class="stat-label">Total Followings</div>
</div>

<div class="stat-item">
    <div class="stat-number">{{ $user->likes_count ?? 0 }}</div>
    <div class="stat-label">Likes Received</div>
</div>

                <div class="stat-item">
                    <div class="stat-number">10.2k</div>
                    <div class="stat-label">Total Saved</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">12</div>
                    <div class="stat-label">Tagged Locations</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Profile Tabs -->
    <div class="container">
        <div class="profile-tabs">
            <div class="profile-tab active" data-tab="art"><i class="fas fa-paint-brush"></i> My Art</div>
            <div class="profile-tab" data-tab="map"><i class="fas fa-map-marked-alt"></i> Map View</div>
            <div class="profile-tab" data-tab="collections"><i class="fas fa-folder"></i> Collections</div>
            <div class="profile-tab" data-tab="liked"><i class="fas fa-heart"></i> Liked Art</div>
            <div class="profile-tab" data-tab="comments"><i class="fas fa-comment"></i> Comments</div>
        </div>
    </div>

    <!-- Art Gallery -->
    <section class="container" id="art-tab">
    <div class="gallery-grid">
        @forelse ($posts as $post)
            <div class="art-card">
                <div class="art-card-img">
                    <img src="{{ $post->image_url ? asset('storage/' . $post->image_url) : 'https://via.placeholder.com/400x300?text=No+Image' }}" alt="{{ $post->caption }}">
                    <div class="art-card-overlay">
                        <div class="art-card-stats">
                            <div class="art-card-stat"><i class="fas fa-heart"></i> {{ $post->likes_count ?? 0 }}</div>
                            <div class="art-card-stat"><i class="fas fa-comment"></i> {{ $post->comments_count ?? 0 }}</div>
                        </div>
                        <a href="#" class="btn btn-primary" style="margin-top: 10px;">View on Map</a>
                    </div>
                </div>
                <div class="art-card-content">
                    <h3 class="art-card-title">{{ $post->caption }}</h3>
                    <div class="art-card-location">
                        <i class="fas fa-map-marker-alt"></i> {{ $post->location_name ?? 'Unknown Location' }}
                    </div>
                    <div class="art-card-stats">
                        <div class="art-card-stat"><i class="fas fa-calendar-alt"></i> {{ $post->created_at->format('F d, Y') }}</div>
                        <div class="art-card-stat">
                            @foreach ($post->tags as $tag)
                                #{{ $tag->name }}
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p>No posts found.</p>
        @endforelse
    </div>
</section>


    <!-- Replace the Artist Spotlight section with this: -->
<section class="artist-spotlight">
    <div class="container">
        <div class="spotlight-header">
            <h2>Artist Spotlight</h2>
            <p>Get to know the artist behind the artworks</p>
        </div>
        <div class="spotlight-content">
            <div class="spotlight-image">
                <img src="https://images.unsplash.com/photo-1547891654-e66ed7ebb968?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Featured Artwork">
            </div>
            <div class="spotlight-info">
                <h3>Featured Artwork: "Urban Dreams"</h3>
                <p class="spotlight-statement">
                    "My work explores the intersection of urban decay and vibrant renewal. I see walls as blank canvases that tell the stories of our cities. Through bold colors and abstract forms, I aim to transform forgotten spaces into places of beauty and conversation."
                </p>
                <div class="spotlight-tools">
                    <div class="tools-title">Tools & Styles I Use:</div>
                    <div class="tools-list">
                        <span class="tool-tag">Spray Paint</span>
                        <span class="tool-tag">Acrylic</span>
                        <span class="tool-tag">Stencils</span>
                        <span class="tool-tag">Abstract</span>
                        <span class="tool-tag">Geometric</span>
                        <span class="tool-tag">Street Art</span>
                    </div>
                </div>
                <div style="margin-top: 30px;">
                    <a href="#" class="btn btn-primary"><i class="fas fa-play"></i> Watch Process Video</a>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Testimonials -->
    <section class="testimonials-section">
        <div class="container">
            <div class="testimonials-header">
                <h2>Collaborations & Testimonials</h2>
                <p>What others say about working with Juno Art</p>
            </div>
            <div class="testimonials-grid">
                <!-- Testimonial 1 -->
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "Juno transformed our dull warehouse wall into a vibrant masterpiece that has become a local landmark. Their attention to detail and unique style brought our vision to life beyond expectations."
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Maria Santos" class="author-avatar">
                        <div class="author-info">
                            <div class="author-name">Maria Santos <span class="collab-tag">Collaboration</span></div>
                            <div class="author-role">Owner, Santos Warehouse</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

   <!-- Update the Contact Section with better spacing: -->
<section class="contact-section">
    <div class="container contact-container">
        <div class="contact-content">
            <h2 class="contact-title">Contact Juno Art</h2>
            <p class="contact-description">
                Interested in commissioning work or collaborating? Get in touch with Juno for project inquiries, exhibition opportunities, or just to say hello.
            </p>
            <div class="contact-buttons">
                <a href="#" class="btn btn-primary btn-large"><i class="fas fa-envelope"></i> Send Message</a>
                <a href="#" class="btn btn-outline btn-large"><i class="fas fa-calendar-check"></i> Book Commission</a>
            </div>
        </div>
    </div>
</section>

    <!-- Map View (Hidden by default) -->
    <section class="container" id="map-tab" style="display: none;">
        <div class="map-container">
            <div id="profileMap"></div>
        </div>
    </section>

    <!-- Collections (Hidden by default) -->
    <section class="container" id="collections-tab" style="display: none;">
        <h2>Collections</h2>
        <p>Organized galleries of Juno's artwork by theme or project</p>

        <div class="collections-grid">
            <!-- Collection 1 -->
            <div class="collection-card">
                <div class="collection-cover">
                    <img src="https://images.unsplash.com/photo-1578926375602-3ad9e91ec0a1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Urban Series">
                    <span class="collection-count">8 artworks</span>
                </div>
                <div class="collection-info">
                    <h3 class="collection-title">Urban Series</h3>
                    <p class="collection-description">Exploring the textures and rhythms of city life</p>
                    <a href="#" class="btn btn-outline" style="padding: 8px 15px; font-size: 0.9rem;">View Collection</a>
                </div>
            </div>
        </div>

    </section>

    <!-- Liked Art (Hidden by default) -->
    <section class="container" id="liked-tab" style="display: none;">
        <h2>Liked Artworks</h2>
        <p>Artworks that Juno has liked and saved</p>

        <div class="liked-art-grid">
            <!-- Liked Art 1 -->
            <div class="art-card">
                <div class="art-card-img">
                    <img src="https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Abstract Flow">
                </div>
            </div>
        </div>
    </section>

    <!-- Comments (Hidden by default) -->
    <section class="container" id="comments-tab" style="display: none;">
        <h2>Recent Comments</h2>
        <p>Juno's recent activity on Street & Ink</p>

        <div class="comments-section">
            <!-- Comment 1 -->
            <div class="comment-card">
                <img src="https://images.unsplash.com/photo-1531123897727-8f129e1688ce?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1374&q=80" alt="Juno Art" class="comment-avatar">
                <div class="comment-content">
                    <div class="comment-header">
                        <span class="comment-author">Juno Art</span>
                        <span class="comment-time">2 hours ago</span>
                    </div>
                    <div class="comment-text">
                        This piece is incredible! The use of color gradients is exactly what I've been trying to master in my own work.
                    </div>
                    <div class="comment-artwork">
                        <img src="https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Artwork">
                        <span>Comment on "Abstract Flow" by Maya Hayuk</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add this empty div for spacing before footer -->
<div class="pre-footer-spacing" style="height: 60px;"></div>
    </section>

    <!-- Footer -->
    <footer>
    @include('footer')
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script>
      // Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const nav = document.getElementById('nav');

    mobileMenuBtn.addEventListener('click', () => {
        nav.classList.toggle('active');
        mobileMenuBtn.innerHTML = nav.classList.contains('active') ?
            '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
    });

        // Header Scroll Effect
        const header = document.getElementById('header');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Bio Edit Functionality
    const editBioBtn = document.getElementById('editBioBtn');
    const bioEditForm = document.getElementById('bioEditForm');
    const bioForm = document.getElementById('bioForm');
    const cancelBioEdit = document.getElementById('cancelBioEdit');
    const profileBio = document.getElementById('profileBio');
    const bioTextarea = document.getElementById('bioTextarea');

    editBioBtn.addEventListener('click', () => {
        profileBio.style.display = 'none';
        editBioBtn.style.display = 'none';
        bioEditForm.style.display = 'block';
    });

    cancelBioEdit.addEventListener('click', () => {
        profileBio.style.display = 'block';
        editBioBtn.style.display = 'block';
        bioEditForm.style.display = 'none';
        bioTextarea.value = profileBio.textContent;
    });

    bioForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(bioForm);

        fetch('/profile/update-bio', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                profileBio.textContent = bioTextarea.value;
                profileBio.style.display = 'block';
                editBioBtn.style.display = 'block';
                bioEditForm.style.display = 'none';
            } else {
                alert('Failed to update bio. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the bio.');
        });
    });

       // Cover Photo Edit Functionality
    const coverEditBtn = document.getElementById('coverEditBtn');
    const coverEditForm = document.getElementById('coverEditForm');
    const coverPhotoForm = document.getElementById('coverPhotoForm');
    const cancelCoverEdit = document.getElementById('cancelCoverEdit');
    const bannerImage = document.getElementById('bannerImage');

    coverEditBtn.addEventListener('click', () => {
        coverEditForm.style.display = coverEditForm.style.display === 'block' ? 'none' : 'block';
    });

    cancelCoverEdit.addEventListener('click', () => {
        coverEditForm.style.display = 'none';
        coverPhotoForm.reset();
    });

    coverPhotoForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(coverPhotoForm);

        fetch('/profile/update-cover', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bannerImage.src = data.cover_photo_url;
                coverEditForm.style.display = 'none';
                coverPhotoForm.reset();
            } else {
                alert('Failed to update cover photo. Please ensure the file is a valid image (jpeg, png, jpg, gif) and under 2MB.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the cover photo.');
        });
    });



    <!-- Profile Tabs -->
    <div class="container">
        <div class="profile-tabs">
            <div class="profile-tab active" data-tab="art"><i class="fas fa-paint-brush"></i> My Art</div>
            <div class="profile-tab" data-tab="map"><i class="fas fa-map-marked-alt"></i> Map View</div>
            <div class="profile-tab" data-tab="collections"><i class="fas fa-folder"></i> Collections</div>
            <div class="profile-tab" data-tab="liked"><i class="fas fa-heart"></i> Liked Art</div>
            <div class="profile-tab" data-tab="comments"><i class="fas fa-comment"></i> Comments</div>
        </div>
    </div>

    <!-- Art Gallery -->
    <section class="container" id="art-tab">
        <div class="gallery-grid">
            <!-- Artwork 1 -->
            <div class="art-card">
                <div class="art-card-img">
                    <img src="https://images.unsplash.com/photo-1578926375602-3ad9e91ec0a1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Urban Dreams">
                    <div class="art-card-overlay">
                        <div class="art-card-stats">
                            <div class="art-card-stat"><i class="fas fa-heart"></i> 248</div>
                            <div class="art-card-stat"><i class="fas fa-comment"></i> 32</div>
                        </div>
                        <a href="#" class="btn btn-primary" style="margin-top: 10px;">View on Map</a>
                    </div>
                </div>
                <div class="art-card-content">
                    <h3 class="art-card-title">Urban Dreams</h3>
                    <div class="art-card-location">
                        <i class="fas fa-map-marker-alt"></i> Cavite Skatepark, Philippines
                    </div>
                    <div class="art-card-stats">
                        <div class="art-card-stat"><i class="fas fa-calendar-alt"></i> May 15, 2023</div>
                        <div class="art-card-stat">#Graffiti #Abstract</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Artist Spotlight -->
    <section class="artist-spotlight">
        <div class="container">
            <div class="spotlight-header">
                <h2>Artist Spotlight</h2>
                <p>Get to know the artist behind the artworks</p>
            </div>
            <div class="spotlight-content">
                <div class="spotlight-image">
                    <img src="https://images.unsplash.com/photo-1578926375602-3ad9e91ec0a1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Featured Artwork">
                </div>
                <div class="spotlight-info">
                    <h3>Featured Artwork: "Urban Dreams"</h3>
                    <p class="spotlight-statement">
                        "My work explores the intersection of urban decay and vibrant renewal. I see walls as blank canvases that tell the stories of our cities. Through bold colors and abstract forms, I aim to transform forgotten spaces into places of beauty and conversation."
                    </p>
                    <div class="spotlight-tools">
                        <div class="tools-title">Tools & Styles I Use:</div>
                        <div class="tools-list">
                            <span class="tool-tag">Spray Paint</span>
                            <span class="tool-tag">Acrylic</span>
                            <span class="tool-tag">Stencils</span>
                            <span class="tool-tag">Abstract</span>
                            <span class="tool-tag">Geometric</span>
                            <span class="tool-tag">Street Art</span>
                        </div>
                    </div>
                    <div style="margin-top: 30px;">
                        <a href="#" class="btn btn-primary"><i class="fas fa-play"></i> Watch Process Video</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section">
        <div class="container">
            <div class="testimonials-header">
                <h2>Collaborations & Testimonials</h2>
                <p>What others say about working with Juno Art</p>
            </div>
            <div class="testimonials-grid">
                <!-- Testimonial 1 -->
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "Juno transformed our dull warehouse wall into a vibrant masterpiece that has become a local landmark. Their attention to detail and unique style brought our vision to life beyond expectations."
                    </div>
                    <div class="testimonial-author">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Maria Santos" class="author-avatar">
                        <div class="author-info">
                            <div class="author-name">Maria Santos <span class="collab-tag">Collaboration</span></div>
                            <div class="author-role">Owner, Santos Warehouse</div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container contact-container">
            <h2 class="contact-title">Contact Juno Art</h2>
            <p class="contact-description">
                Interested in commissioning work or collaborating? Get in touch with Juno for project inquiries, exhibition opportunities, or just to say hello.
            </p>
            <div class="contact-buttons">
                <a href="#" class="btn btn-primary btn-large"><i class="fas fa-envelope"></i> Send Message</a>
                <a href="#" class="btn btn-outline btn-large"><i class="fas fa-calendar-check"></i> Book Commission</a>
            </div>
        </div>
    </section>

    <!-- Map View (Hidden by default) -->
    <section class="container" id="map-tab" style="display: none;">
        <div class="map-container">
            <div id="profileMap"></div>
        </div>
    </section>

    <!-- Collections (Hidden by default) -->
    <section class="container" id="collections-tab" style="display: none;">
        <h2>Collections</h2>
        <p>Organized galleries of Juno's artwork by theme or project</p>

        <div class="collections-grid">
            <!-- Collection 1 -->
            <div class="collection-card">
                <div class="collection-cover">
                    <img src="https://images.unsplash.com/photo-1578926375602-3ad9e91ec0a1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Urban Series">
                    <span class="collection-count">8 artworks</span>
                </div>
                <div class="collection-info">
                    <h3 class="collection-title">Urban Series</h3>
                    <p class="collection-description">Exploring the textures and rhythms of city life</p>
                    <a href="#" class="btn btn-outline" style="padding: 8px 15px; font-size: 0.9rem;">View Collection</a>
                </div>
            </div>

            <!-- Collection 2 -->
            <div class="collection-card">
                <div class="collection-cover">
                    <img src="https://images.unsplash.com/photo-1547891654-e66ed7ebb968?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Political Works">
                    <span class="collection-count">5 artworks</span>
                </div>
                <div class="collection-info">
                    <h3 class="collection-title">Political Works</h3>
                    <p class="collection-description">Commentary on social and political issues</p>
                    <a href="#" class="btn btn-outline" style="padding: 8px 15px; font-size: 0.9rem;">View Collection</a>
                </div>
            </div>

            <!-- Collection 3 -->
            <div class="collection-card">
                <div class="collection-cover">
                    <img src="https://images.unsplash.com/photo-1516617442634-75371039cb3a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1374&q=80" alt="Neon Dreams">
                    <span class="collection-count">3 artworks</span>
                </div>
                <div class="collection-info">
                    <h3 class="collection-title">Neon Dreams</h3>
                    <p class="collection-description">Vibrant night-inspired pieces</p>
                    <a href="#" class="btn btn-outline" style="padding: 8px 15px; font-size: 0.9rem;">View Collection</a>
                </div>
            </div>

            <!-- Collection 4 -->
            <div class="collection-card">
                <div class="collection-cover">
                    <img src="https://images.unsplash.com/photo-1579762715118-a6f1d4b934f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=686&q=80" alt="Collaborations">
                    <span class="collection-count">5 artworks</span>
                </div>
                <div class="collection-info">
                    <h3 class="collection-title">Collaborations</h3>
                    <p class="collection-description">Works created with other artists</p>
                    <a href="#" class="btn btn-outline" style="padding: 8px 15px; font-size: 0.9rem;">View Collection</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Liked Art (Hidden by default) -->
    <section class="container" id="liked-tab" style="display: none;">
        <h2>Liked Artworks</h2>
        <p>Artworks that Juno has liked and saved</p>

        <div class="liked-art-grid">
            <!-- Liked Art 1 -->
            <div class="art-card">
                <div class="art-card-img">
                    <img src="https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Abstract Flow">
                </div>
            </div>


        </div>
    </section>

    <!-- Comments (Hidden by default) -->
    <section class="container" id="comments-tab" style="display: none;">
        <h2>Recent Comments</h2>
        <p>Juno's recent activity on Street & Ink</p>

        <div class="comments-section">
            <!-- Comment 1 -->
            <div class="comment-card">
                <img src="https://images.unsplash.com/photo-1531123897727-8f129e1688ce?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1374&q=80" alt="Juno Art" class="comment-avatar">
                <div class="comment-content">
                    <div class="comment-header">
                        <span class="comment-author">Juno Art</span>
                        <span class="comment-time">2 hours ago</span>
                    </div>
                    <div class="comment-text">
                        This piece is incredible! The use of color gradients is exactly what I've been trying to master in my own work.
                    </div>
                    <div class="comment-artwork">
                        <img src="https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Artwork">
                        <span>Comment on "Abstract Flow" by Maya Hayuk</span>
                    </div>
                </div>
            </div>


        </div>
    </section>



    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

<!-- Before closing </body> -->
<script src="{{ asset('js/loading.js') }}"></script>
</body>
</html>
