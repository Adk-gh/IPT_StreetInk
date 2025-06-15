document.addEventListener('DOMContentLoaded', () => {
    // ========== Mobile Menu ==========
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const nav = document.querySelector('nav');

    menuBtn?.addEventListener('click', () => {
        nav?.classList.toggle('active');
        menuBtn.classList.toggle('active');
    });

    // ========== Header on Scroll ==========
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
        header?.classList.toggle('scrolled', window.scrollY > 50);
    });

    // ========== Smooth Scrolling ==========
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', e => {
            e.preventDefault();
            document.querySelector(anchor.getAttribute('href'))?.scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // ========== Theme Toggle ==========
    const themeToggle = document.getElementById('theme-toggle');
    themeToggle?.addEventListener('click', () => {
        const darkMode = document.body.classList.toggle('dark-theme');
        localStorage.setItem('theme', darkMode ? 'dark' : 'light');
    });

    // Load saved theme
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-theme');
    }

    // ========== Interactive Map ==========
    // Define map layers// Light (Carto Light - clean, modern look)
const lightLayer = L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, Tiles &copy; Humanitarian OpenStreetMap Team'
});




// Dark (Carto Dark - optimized for nighttime/dark UI)
const darkLayer = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
    maxZoom: 20,
    attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>'
});


// Satellite (Esri World Imagery - high quality satellite tiles)
const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    maxZoom: 60,
    attribution: '&copy; <a href="https://www.esri.com/">Esri</a>, Maxar, Earthstar Geographics'
});

    // Initialize map with the light layer
    const map = L.map("streetArtMap", {
        layers: [lightLayer],
        zoomControl: false
    }).setView([14.0555, 121.3250], 13);

    // Add zoom control with position
    L.control.zoom({
        position: 'topright'
    }).addTo(map);

    // Layer arrays and index
    const layers = [lightLayer, darkLayer, satelliteLayer];
    const layerNames = ['Light', 'Dark', 'Satellite'];
    let currentIndex = 0;

    // Create a custom Leaflet control for theme toggle
    const ThemeToggleControl = L.Control.extend({
        options: {
            position: 'topleft'
        },
        onAdd: function () {
            const container = L.DomUtil.create('button', 'leaflet-bar leaflet-control leaflet-control-custom');
            container.style.backgroundColor = 'white';
            container.style.width = '90px';
            container.style.height = '30px';
            container.style.lineHeight = '30px';
            container.style.textAlign = 'center';
            container.style.cursor = 'pointer';
            container.style.fontWeight = '600';
            container.title = 'Toggle map theme';

            container.textContent = layerNames[currentIndex];

            L.DomEvent.disableClickPropagation(container);

            container.addEventListener('click', () => {
                map.removeLayer(layers[currentIndex]);
                currentIndex = (currentIndex + 1) % layers.length;
                map.addLayer(layers[currentIndex]);
                container.textContent = layerNames[currentIndex];
            });

            return container;
        }
    });

    // Add the custom control to map
    map.addControl(new ThemeToggleControl());

    let clusteredMarkers;

    async function loadPostMarkers() {
        try {
            const res = await fetch('/api/posts');
            if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
            const posts = await res.json();

            clusteredMarkers = L.markerClusterGroup({
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true,
                maxClusterRadius: 60
            });

            posts.forEach(post => {
                const originalPost = post.post || post;

                // Skip if no coordinates
                if (!originalPost.latitude || !originalPost.longitude) {
                    console.warn('Missing coordinates for post:', originalPost.id);
                    return;
                }

                const lat = parseFloat(originalPost.latitude);
                const lng = parseFloat(originalPost.longitude);

                // Get user data with fallbacks
                const user = originalPost.user || {};
                const userName = user.name || 'Unknown User';
                const userEmail = user.email || '';
                const userAvatar = user.profile_picture
                    ? user.profile_picture.startsWith('http')
                        ? user.profile_picture
                        : user.profile_picture
                    : '/img/default-avatar.jpg';

                // Get post data with fallbacks
                const location = originalPost.location_name || 'Unknown location';
                const description = originalPost.caption || '';
                const createdAt = originalPost.created_at
                    ? new Date(originalPost.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                      })
                    : 'Unknown date';

                const imageUrl = originalPost.image_url
                    ? originalPost.image_url.startsWith('http')
                        ? originalPost.image_url
                        : `/storage/${originalPost.image_url.replace(/^storage\/|^public\//, '')}`
                    : null;

                // Create marker icon
                const markerIcon = L.divIcon({
                    html: `<div style="
                        width: 40px;
                        height: 40px;
                        background: url('${userAvatar}');
                        background-size: cover;
                        border-radius: 50%;
                        border: 2px solid white;
                        box-shadow: 0 0 5px rgba(0,0,0,0.3);
                    "></div>`,
                    className: 'custom-marker',
                    iconSize: [40, 40],
                    iconAnchor: [20, 40]
                });

                // Create marker
                const marker = L.marker([lat, lng], {
                    icon: markerIcon,
                    title: description || 'Post location',
                    riseOnHover: true,
                    zIndexOffset: 1000
                });

                // Modern minimalist popup content
                const popupContent = `
                <div class="post-popup" style="
                    width: 320px;
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
                ">
                    <!-- Header Section -->
                    <div style="
                        display: flex;
                        align-items: center;
                        padding: 16px;
                        background: #ffffff;
                        border-bottom: 1px solid #f0f0f0;
                    ">
                        <img src="${userAvatar}"
                             style="
                                width: 48px;
                                height: 48px;
                                border-radius: 50%;
                                object-fit: cover;
                                margin-right: 12px;
                                border: 1px solid #f5f5f5;
                             "
                             onerror="this.src='/img/default-avatar.jpg'">
                        <div>
                            <h4 style="
                                margin: 0 0 4px 0;
                                font-size: 15px;
                                font-weight: 600;
                                color: #222;
                            ">${userName}</h4>
                            <small style="
                                color: #888;
                                font-size: 13px;
                                display: flex;
                                align-items: center;
                                gap: 4px;
                            ">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                ${location}
                            </small>
                        </div>
                    </div>

                    <!-- Post Image -->
                    ${imageUrl ? `
                    <div style="
                        width: 100%;
                        height: 220px;
                        background: #f9f9f9;
                    ">
                        <img src="${imageUrl}"
                             style="
                                width: 100%;
                                height: 100%;
                                object-fit: cover;
                                display: block;
                             "
                             onerror="this.style.display='none'">
                    </div>` : ''}

                    <!-- Content Section -->
                    <div style="padding: 16px; background: #ffffff;">
                        <!-- Date -->
                        <div style="
                            display: flex;
                            align-items: center;
                            gap: 6px;
                            margin-bottom: 12px;
                            color: #888;
                            font-size: 13px;
                        ">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            ${createdAt}
                        </div>

                        <!-- Post Caption -->
                        ${description ? `
                        <div style="
                            margin-bottom: 16px;
                            color: #333;
                            font-size: 15px;
                            line-height: 1.5;
                        ">
                            ${description}
                        </div>` : ''}

                        <!-- Tags -->
                        ${originalPost.tags && originalPost.tags.length ? `
                        <div style="
                            display: flex;
                            flex-wrap: wrap;
                            gap: 6px;
                            margin-top: 12px;
                        ">
                            ${originalPost.tags.map(tag => `
                                <span style="
                                    background: #f0f0f0;
                                    color: #555;
                                    padding: 4px 10px;
                                    border-radius: 20px;
                                    font-size: 12px;
                                ">#${tag}</span>
                            `).join('')}
                        </div>` : ''}
                    </div>
                </div>`;

                marker.bindPopup(popupContent, {
                    maxWidth: 320,
                    minWidth: 320,
                    className: 'modern-popup',
                    autoPanPaddingTopLeft: [20, 20],
                    autoPanPaddingBottomRight: [20, 20]
                });

                clusteredMarkers.addLayer(marker);
            });

            // Clear existing clusters
            map.eachLayer(layer => {
                if (layer instanceof L.MarkerClusterGroup) {
                    map.removeLayer(layer);
                }
            });

            // Add to map and fit bounds
            map.addLayer(clusteredMarkers);
            if (posts.length > 0) {
                map.fitBounds(clusteredMarkers.getBounds(), {
                    padding: [50, 50],
                    maxZoom: 15
                });
            }

            // Force redraw
            setTimeout(() => {
                map.invalidateSize(true);
                clusteredMarkers.refreshClusters();
            }, 100);

        } catch (err) {
            console.error("Map error:", err);
            alert("Error loading map data. Please try again.");
        }
    }

    // Load posts on map initialization
    loadPostMarkers();

     // ========== Robust Fullscreen Toggle ==========
    const mapContainer = document.querySelector('.map-container');
    const viewFullMapBtn = document.getElementById('viewFullMapBtn');
    let isFullscreen = false;

    function toggleFullscreen(e) {
    if (e) e.preventDefault();

    isFullscreen = !isFullscreen;

    // Toggle classes
    mapContainer.classList.toggle('fullscreen', isFullscreen);
    document.body.classList.toggle('fullscreen-map-active', isFullscreen);

    // Update button
    if (viewFullMapBtn) {
        const icon = isFullscreen ? 'compress' : 'expand';
        viewFullMapBtn.innerHTML = `<i class="fas fa-${icon}"></i> ${isFullscreen ? 'Exit Full Map' : 'View Full Map'}`;

        // Ensure button is clickable
        viewFullMapBtn.style.pointerEvents = 'auto';
        viewFullMapBtn.style.zIndex = isFullscreen ? '10001' : '1001';
    }

    // Resize map after transition
    setTimeout(() => {
        map.invalidateSize(true);
        if (isFullscreen && clusteredMarkers) {
            map.fitBounds(clusteredMarkers.getBounds());
        }
    }, 100);
}

    // Add click event listener with proper checks
    if (viewFullMapBtn) {
        viewFullMapBtn.addEventListener('click', toggleFullscreen);
    }

    // Add escape key handler
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isFullscreen) {
            toggleFullscreen(e);
        }
    });

    // [Rest of your existing code]
});
