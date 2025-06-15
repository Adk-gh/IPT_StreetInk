<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Street & Ink | Discover Street Art Near You</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

    <link href="{{ asset('css/loading.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <script src="{{ asset('js/index.js') }}"></script>
    <!-- Add this to both files -->

    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css">
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!-- Header -->
     <header id="header">
        @include('header')
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Explore the Streets, One Wall at a Time</h1>
            <p>Discover and share the most vibrant street art from around the world. Join our community of urban art enthusiasts and creators.</p>
            <div class="hero-btns">
                <a href="{{ route('social_feed') }}" class="btn btn-primary btn-large" id="ajax-link">Get Started</a>
                <a href="{{ route('aboutus') }}" class="btn btn-secondary btn-large">Learn More</a>
            </div>
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Search by city, neighborhood or artist...">
                <button class="search-btn">Search</button>
            </div>
            <a href="#map" class="location-btn">
                <i class="fas fa-location-arrow"></i> Find Art Near Me
            </a>
        </div>
    </section>

    <!-- Map Preview Section -->
    <section class="section map-preview" id="map">
        <div class="container">
            <h2 class="section-title">Interactive Street Art Map</h2>
            <p class="text-center" style="margin-bottom: 2rem;">Explore thousands of street art locations around the world. Click on markers to discover new artworks.</p>
            <div class="map-container">
               <div id="streetArtMap" style="height: 500px;"></div>
                <div class="map-overlay">
                    <h3>San Pablo City Street Art</h3>
                    <p>Discover over 1,200 documented street artworks in City Of Seven Lakes.</p>
                   <a href="#" class="btn btn-primary" id="viewFullMapBtn">View Full Map</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Wrapper -->
    <div id="postModal" class="hidden position-fixed top-0 start-0 w-100 h-100" style="z-index: 1050;">
      <div id="modalContent" class="h-100 d-flex align-items-center justify-content-center"></div>
    </div>

    <!-- Post Modal -->
    <div id="postModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div id="modalContent" class="bg-white rounded-xl shadow-xl p-5 max-w-md w-full max-h-[90vh] overflow-y-auto"></div>
    </div>

    <!-- Artists Section -->
    <section class="section artists" id="artists">
        <div class="container">
            <h2 class="section-title">Featured Artists</h2>
            <p class="text-center">Meet the creative minds behind the art. Discover their styles, stories, and contributions to the street art scene.</p>

            <div class="artist-grid">
                @forelse ($users as $user)
                    <div class="artist-card">
                        <div class="artist-card-img">
                            <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'img/default.jpg' }}" alt="User Avatar">
                        </div>
                        <div class="artist-card-content">
                            <h3 class="artist-card-name">{{ $user->name }}</h3>
                            <h4 class="artist-card-username">{{ $user->username }}</h4>
                            <div class="artist-card-style">{{ $user->style ?? 'Unknown Style' }}</div>
                            <p class="artist-card-bio">{{ $user->bio ?? 'No bio available.' }}</p>
                            <div class="artist-social">
                                @if($user->instagram) <a href="{{ $user->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a> @endif
                                @if($user->twitter) <a href="{{ $user->twitter }}" target="_blank"><i class="fab fa-twitter"></i></a> @endif
                                @if($user->website) <a href="{{ $user->website }}" target="_blank"><i class="fas fa-globe"></i></a> @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p>No artists found.</p>
                @endforelse
            </div>
            <div class="view-all-container">
                <a href="{{ route('artist', $user->id ?? '#') }}" class="btn btn-primary btn-view-artist">View All Artist</a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="section" id="categories">
        <div class="container">
            <h2 class="section-title">Explore by Categories</h2>
            <p class="text-center">Find street art that matches your interests and style preferences.</p>

            <div class="categories-grid">
                <!-- Category 1 -->
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-paint-roller"></i>
                    </div>
                    <h3 class="category-title">Murals</h3>
                    <div class="category-count">1,240+ Artworks</div>
                </div>

                <!-- Category 2 -->
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-spray-can"></i>
                    </div>
                    <h3 class="category-title">Graffiti</h3>
                    <div class="category-count">980+ Artworks</div>
                </div>

                <!-- Category 3 -->
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-sticky-note"></i>
                    </div>
                    <h3 class="category-title">Stickers</h3>
                    <div class="category-count">320+ Artworks</div>
                </div>

                <!-- Category 4 -->
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-cut"></i>
                    </div>
                    <h3 class="category-title">Stencils</h3>
                    <div class="category-count">450+ Artworks</div>
                </div>

                <!-- Category 5 -->
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <h3 class="category-title">Installations</h3>
                    <div class="category-count">210+ Artworks</div>
                </div>

                <!-- Category 6 -->
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-arrows-alt-h"></i>
                    </div>
                    <h3 class="category-title">Wheatpaste</h3>
                    <div class="category-count">180+ Artworks</div>
                </div>

                <!-- Category 7 -->
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-marker"></i>
                    </div>
                    <h3 class="category-title">Tagging</h3>
                    <div class="category-count">670+ Artworks</div>
                </div>

                <!-- Category 8 -->
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3 class="category-title">Light Art</h3>
                    <div class="category-count">90+ Artworks</div>
                </div>
            </div>
        </div>
    </section>

    <!-- App Section -->
    <section class="section">
        <div class="container">
            <div class="app-container">
                <div class="app-content">
                    <h2>Street & Ink Mobile App</h2>
                    <p>Take the street art discovery experience with you wherever you go. Our mobile app makes it easy to find, save, and share street art in real-time.</p>
                    <ul style="margin: 20px 0 30px; padding-left: 20px;">
                        <li style="margin-bottom: 10px; display: flex; align-items: flex-start;">
                            <i class="fas fa-check" style="color: var(--accent); margin-right: 10px; margin-top: 3px;"></i>
                            <span>Augmented Reality street art detection</span>
                        </li>
                        <li style="margin-bottom: 10px; display: flex; align-items: flex-start;">
                            <i class="fas fa-check" style="color: var(--accent); margin-right: 10px; margin-top: 3px;"></i>
                            <span>Offline access to saved locations</span>
                        </li>
                        <li style="margin-bottom: 10px; display: flex; align-items: flex-start;">
                            <i class="fas fa-check" style="color: var(--accent); margin-right: 10px; margin-top: 3px;"></i>
                            <span>Exclusive app-only artworks</span>
                        </li>
                        <li style="display: flex; align-items: flex-start;">
                            <i class="fas fa-check" style="color: var(--accent); margin-right: 10px; margin-top: 3px;"></i>
                            <span>Instant notifications for new art in your area</span>
                        </li>
                    </ul>
                    <div class="app-buttons">
                        <a href="#" class="app-btn">
                            <i class="fab fa-apple"></i>
                            <div class="app-btn-text">
                                <span>Download on the</span>
                                <span>App Store</span>
                            </div>
                        </a>
                        <a href="#" class="app-btn">
                            <i class="fab fa-google-play"></i>
                            <div class="app-btn-text">
                                <span>Get it on</span>
                                <span>Google Play</span>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="app-image">
                    <img src="https://images.unsplash.com/photo-1555774698-0b77e0d5fac6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80" alt="Street & Ink Mobile App">
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="section blog">
        <div class="container">
            <h2 class="section-title">Street Art Stories</h2>
            <p class="text-center">Read about the latest in street art culture, events, and artist interviews.</p>

            <div class="blog-grid">
                <!-- Blog Post 1 -->
                <div class="blog-card">
                    <div class="blog-card-img">
                        <img src="https://images.pexels.com/photos/1045534/pexels-photo-1045534.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Blog Post">
                    </div>
                    <div class="blog-card-content">
                        <div class="blog-card-date">January 15, 2024</div>
                        <h3 class="blog-card-title">The Rise of Political Street Art in 2024</h3>
                        <p class="blog-card-text">How artists are using urban spaces to comment on current events and social issues around the world.</p>
                        <a href="#" class="blog-card-link">Read More <span class="arrow-right"></span></a>
                    </div>
                </div>

                <!-- Blog Post 2 -->
                <div class="blog-card">
                    <div class="blog-card-img">
                        <img src="https://images.pexels.com/photos/177283/pexels-photo-177283.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Blog Post">
                    </div>
                    <div class="blog-card-content">
                        <div class="blog-card-date">January 8, 2024</div>
                        <h3 class="blog-card-title">Interview: The Neon Artist in Downtown LA</h3>
                        <p class="blog-card-text">We sit down with the anonymous artist behind the glowing murals transforming the city.</p>
                        <a href="#" class="blog-card-link">Read More <span class="arrow-right"></span></a>
                    </div>
                </div>

                <!-- Blog Post 3 -->
                <div class="blog-card">
                    <div class="blog-card-img">
                        <img src="https://images.pexels.com/photos/8275181/pexels-photo-8275181.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="Blog Post">
                    </div>
                    <div class="blog-card-content">
                        <div class="blog-card-date">December 30, 2023</div>
                        <h3 class="blog-card-title">Street Art Festivals You Can't Miss This Summer</h3>
                        <p class="blog-card-text">From Berlin to Buenos Aires, here are the must-visit urban art events of the season.</p>
                        <a href="#" class="blog-card-link">Read More <span class="arrow-right"></span></a>
                    </div>
                </div>
            </div>

            <div class="text-center" style="margin-top: 50px;">
                <a href="#" class="btn btn-primary">View All Events</a>
            </div>
        </div>
    </section>

    <!-- Community Section -->
    <section class="section community" id="community">
        <div class="container">
            <h2 class="section-title">Our Community</h2>
            <p class="text-center">Connect with street art enthusiasts, share your finds, and join our global community.</p>

            <div class="community-stats">
                <div class="stat-item">
                    <div class="stat-number">25K+</div>
                    <div class="stat-title">Artworks</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="stat-title">Members</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">150+</div>
                    <div class="stat-title">Cities</div>
                </div>
            </div>

            <h3 class="contributors-title">Top Contributors</h3>
            <div class="contributors-grid">
                <div class="contributor-card">
                    <div class="contributor-avatar">
                        <img src="https://randomuser.me/api/portraits/women/1.jpg" alt="Contributor">
                    </div>
                    <div class="contributor-info">
                        <h4 class="contributor-name">Sophie L.</h4>
                        <div class="contributor-stats">
                            <span class="contributor-count">450</span>
                            <span class="contributor-label">Contributions</span>
                        </div>
                    </div>
                </div>
                <div class="contributor-card">
                    <div class="contributor-avatar">
                        <img src="https://randomuser.me/api/portraits/men/2.jpg" alt="Contributor">
                    </div>
                    <div class="contributor-info">
                        <h4 class="contributor-name">James K.</h4>
                        <div class="contributor-stats">
                            <span class="contributor-count">400</span>
                            <span class="contributor-label">Contributions</span>
                        </div>
                    </div>
                </div>
                <div class="contributor-card">
                    <div class="contributor-avatar">
                        <img src="https://randomuser.me/api/portraits/women/3.jpg" alt="Contributor">
                    </div>
                    <div class="contributor-info">
                        <h4 class="contributor-name">Emma W.</h4>
                        <div class="contributor-stats">
                            <span class="contributor-count">380</span>
                            <span class="contributor-label">Contributions</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center" style="margin-top: 50px;">
                <a href="#" class="btn btn-primary btn-large">Join Now</a>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="section newsletter">
        <div class="container">
            <div class="newsletter-content">
                <h2 class="section-title">Stay in Touch</h2>
                <p class="newsletter-text">Sign up for our newsletter to get the latest updates on street art and events.</p>
                <form class="newsletter-form">
                    <div class="form-group">
                        <input type="email" class="newsletter-input" placeholder="Enter your email" required>
                        <button type="submit" class="btn btn-primary newsletter-btn">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Include Footer-->
    <footer>
        @include('footer')
    </footer>

    <script>
        const postsWithLocation = [
            @foreach ($posts as $post)
                @if ($post->latitude && $post->longitude)
                    {
                        id: {{ $post->id }},
                        title: @json($post->caption ?? 'Untitled'),
                        latitude: {{ $post->latitude }},
                        longitude: {{ $post->longitude }},
                        location_name: @json($post->location_name ?? ''),
                        image_url: @json($post->image_url ? asset('storage/' . $post->image_url) : null),
                    },
                @endif
            @endforeach
        ];
    </script>

    <script src="https://unpkg.com/leaflet.js"></script>
    <script src="{{ asset('js/loading.js') }}"></script>
</body>
</html>