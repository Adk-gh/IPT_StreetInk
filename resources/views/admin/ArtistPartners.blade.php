<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Street & Ink | Artist & Partner Management</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/admin-artistpartners.css') }}" rel="stylesheet">
    <script src="{{ asset('js/admin-artistpartners.js') }}" defer></script>
      <!-- Inside <head> -->
    <link href="{{ asset('css/loading.css') }}" rel="stylesheet">
</head>
<body>
      @include('admin.adminsidebar')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
       @include('admin.adminnavbar')

        <!-- Tabs -->
        <div class="tabs">
            <div class="tab active" data-target="artists-tab">Artists</div>
            <div class="tab" data-target="partners-tab">Partners</div>
        </div>

        <!-- Artists Tab -->
        <div class="tab-content active" id="artists-tab">
            <!-- Filter Panel -->
            <div class="filter-panel">
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">Search</label>
                        <input type="text" class="form-control" placeholder="Search by name or username...">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Verification Status</label>
                        <select class="form-control">
                            <option>All</option>
                            <option>Verified Only</option>
                            <option>Unverified Only</option>
                            <option>Pending Verification</option>
                        </select>
                    </div>
                </div>
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">Location</label>
                        <select class="form-control">
                            <option>All Locations</option>
                            <option>New York</option>
                            <option>London</option>
                            <option>Berlin</option>
                            <option>Tokyo</option>
                            <option>Los Angeles</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Sort By</label>
                        <select class="form-control">
                            <option>Most Recent</option>
                            <option>Most Uploads</option>
                            <option>Alphabetical (A-Z)</option>
                            <option>Alphabetical (Z-A)</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button class="btn btn-secondary btn-sm">Reset Filters</button>
                    <button class="btn btn-primary btn-sm">Apply Filters</button>
                </div>
            </div>

            <!-- Artists Table -->
            <div class="data-table">
                <div class="table-header">
                    <h3 class="table-title">Artist Directory</h3>
                    <div class="table-actions">
                        <button class="btn btn-secondary btn-sm">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <button class="btn btn-primary btn-sm" id="addArtistBtn">
                            <i class="fas fa-plus"></i> Add Artist
                        </button>
                    </div>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Artist</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Location</th>
                                <th>Verified</th>
                                <th>Uploads</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-sm">
                                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Artist">
                                        </div>
                                        <div class="user-info">
                                            <div class="user-name-sm">Jennie Cruz</div>
                                        </div>
                                    </div>
                                </td>
                                <td>@inkjenn</td>
                                <td>jennie@email.com</td>
                                <td>Quezon City</td>
                                <td><span class="status active">Yes</span></td>
                                <td>24</td>
                                <td>Feb 21, 2024</td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn" title="View" onclick="openArtistModal('Jennie Cruz')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <div class="pagination-info">Showing 1 to 5 of 512 artists</div>
                    <div class="pagination-btns">
                        <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn">2</button>
                        <button class="page-btn">3</button>
                        <button class="page-btn">...</button>
                        <button class="page-btn">10</button>
                        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Partners Tab -->
        <div class="tab-content" id="partners-tab">
            <!-- Filter Panel -->
            <div class="filter-panel">
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">Search</label>
                        <input type="text" class="form-control" placeholder="Search by name or type...">
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Partner Type</label>
                        <select class="form-control">
                            <option>All Types</option>
                            <option>Venue Partner</option>
                            <option>Art Supply</option>
                            <option>Sponsor</option>
                            <option>Cultural Org</option>
                            <option>Brand Collab</option>
                        </select>
                    </div>
                </div>
                <div class="filter-row">
                    <div class="filter-group">
                        <label class="filter-label">Country</label>
                        <select class="form-control">
                            <option>All Countries</option>
                            <option>United States</option>
                            <option>United Kingdom</option>
                            <option>Germany</option>
                            <option>Japan</option>
                            <option>Philippines</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Status</label>
                        <select class="form-control">
                            <option>All Statuses</option>
                            <option>Active</option>
                            <option>Inactive</option>
                            <option>Pending</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button class="btn btn-secondary btn-sm">Reset Filters</button>
                    <button class="btn btn-primary btn-sm">Apply Filters</button>
                </div>
            </div>

            <!-- Partners Table -->
            <div class="data-table">
                <div class="table-header">
                    <h3 class="table-title">Partner Directory</h3>
                    <div class="table-actions">
                        <button class="btn btn-secondary btn-sm">
                            <i class="fas fa-download"></i> Export
                        </button>
                        <button class="btn btn-primary btn-sm" id="addPartnerBtn">
                            <i class="fas fa-plus"></i> Add Partner
                        </button>
                    </div>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Organization</th>
                                <th>Type</th>
                                <th>Contact</th>
                                <th>Country</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ArtSpace Manila</td>
                                <td>Venue Partner</td>
                                <td>hello@artspace.ph</td>
                                <td>Philippines</td>
                                <td><span class="status active">Active</span></td>
                                <td>Jan 2024</td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn" title="View" onclick="openPartnerModal('ArtSpace Manila')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>SprayCo</td>
                                <td>Art Supply</td>
                                <td>sales@sprayco.com</td>
                                <td>United States</td>
                                <td><span class="status pending">Pending</span></td>
                                <td>Apr 5, 2024</td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn" title="View" onclick="openPartnerModal('SprayCo')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Urban Canvas</td>
                                <td>Cultural Org</td>
                                <td>info@urbancanvas.org</td>
                                <td>United Kingdom</td>
                                <td><span class="status active">Active</span></td>
                                <td>Sep 2023</td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn" title="View" onclick="openPartnerModal('Urban Canvas')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Nike Street Art</td>
                                <td>Brand Collab</td>
                                <td>streetart@nike.com</td>
                                <td>Global</td>
                                <td><span class="status active">Active</span></td>
                                <td>Mar 2023</td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn" title="View" onclick="openPartnerModal('Nike Street Art')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Berlin Walls</td>
                                <td>Venue Partner</td>
                                <td>contact@berlinwalls.de</td>
                                <td>Germany</td>
                                <td><span class="status inactive">Inactive</span></td>
                                <td>Jun 2022</td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn" title="View" onclick="openPartnerModal('Berlin Walls')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <div class="pagination-info">Showing 1 to 5 of 32 partners</div>
                    <div class="pagination-btns">
                        <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn">2</button>
                        <button class="page-btn">3</button>
                        <button class="page-btn">...</button>
                        <button class="page-btn">7</button>
                        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- Partner Applications -->
            <div class="data-table">
                <div class="table-header">
                    <h3 class="table-title">Partner Applications</h3>
                    <div class="table-actions">
                        <button class="btn btn-secondary btn-sm">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Organization</th>
                                <th>Type</th>
                                <th>Contact Email</th>
                                <th>Proposal</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Graffiti Hub</td>
                                <td>Venue Partner</td>
                                <td>contact@graffitihub.com</td>
                                <td><a href="#">View PDF</a></td>
                                <td>Apr 10, 2024</td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="action-btn" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="action-btn" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Street Wear Co</td>
                                <td>Brand Collab</td>
                                <td>partnerships@streetwear.com</td>
                                <td><a href="#">View PDF</a></td>
                                <td>Apr 8, 2024</td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="action-btn" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="action-btn" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Art Revolution</td>
                                <td>Cultural Org</td>
                                <td>hello@artrevolution.org</td>
                                <td><a href="#">View PDF</a></td>
                                <td>Apr 5, 2024</td>
                                <td>
                                    <div class="action-btns">
                                        <button class="action-btn" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="action-btn" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="action-btn" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    <div class="pagination-info">Showing 1 to 3 of 3 applications</div>
                    <div class="pagination-btns">
                        <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
                        <button class="page-btn active">1</button>
                        <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Artist Modal -->
    <div class="modal" id="artistModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="artistModalTitle">Artist Profile</h3>
                <button class="modal-close" onclick="closeModal('artistModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="artist-profile">
                    <div class="artist-avatar">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Artist">
                    </div>
                    <div class="artist-details">
                        <h2 class="artist-name" id="artistName">Jennie Cruz</h2>
                        <div class="artist-username">@inkjenn</div>
                        <div class="artist-bio">
                            Street artist specializing in large-scale murals with social commentary. Based in Quezon City, Philippines. Known for vibrant colors and intricate patterns that reflect Filipino culture and contemporary issues.
                        </div>
                        <div class="artist-social">
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-behance"></i></a>
                            <a href="#" class="social-link"><i class="fas fa-globe"></i></a>
                        </div>
                        <div class="artist-stats">
                            <div class="stat-box">
                                <div class="stat-value">24</div>
                                <div class="stat-label">Artworks</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-value">1,842</div>
                                <div class="stat-label">Followers</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-value">156</div>
                                <div class="stat-label">Likes</div>
                            </div>
                        </div>
                        <div class="artist-tags">
                            <span class="tag">Muralist</span>
                            <span class="tag">Street Art</span>
                            <span class="tag">Social Commentary</span>
                            <span class="tag">Philippines</span>
                        </div>
                    </div>
                </div>

                <div class="artist-gallery">
                    <h3 class="gallery-title">Featured Artworks</h3>
                    <div class="gallery-grid">
                        <div class="gallery-item">
                            <img src="img/pexels-vincent-gerbouin-445991-2263686.jpg" alt="Artwork">
                        </div>
                        <div class="gallery-item">
                            <img src="img/pexels-conojeghuo-173301.jpg" alt="Artwork">
                        </div>
                        <div class="gallery-item">
                            <img src="img/pexels-fernando-dos-santos-campos-1309016-2510245 (1).jpg" alt="Artwork">
                        </div>
                        <div class="gallery-item">
                            <img src="img/pexels-heftiba-1194420.jpg" alt="Artwork">
                        </div>
                        <div class="gallery-item">
                            <img src="img/pexels-tobiasbjorkli-2119706 (1).jpg" alt="Artwork">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary">Message Artist</button>
                <button class="btn btn-secondary">View All Artworks</button>
                <button class="btn btn-primary">Verify Artist</button>
            </div>
        </div>
    </div>

    <!-- Partner Modal -->
    <div class="modal" id="partnerModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="partnerModalTitle">Partner Profile</h3>
                <button class="modal-close" onclick="closeModal('partnerModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="partner-profile">
                    <div class="partner-logo">
                        <img src="https://via.placeholder.com/150" alt="Partner Logo">
                    </div>
                    <div class="partner-details">
                        <h2 class="partner-name" id="partnerName">ArtSpace Manila</h2>
                        <div class="partner-type">Venue Partner</div>
                        <div class="partner-contact">
                            <div class="contact-item">
                                <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                                <div>hello@artspace.ph</div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-icon"><i class="fas fa-phone"></i></div>
                                <div>+63 912 345 6789</div>
                            </div>
                            <div class="contact-item">
                                <div class="contact-icon"><i class="fas fa-user"></i></div>
                                <div>Maria Santos (Primary Contact)</div>
                            </div>
                        </div>
                        <div class="partner-description">
                            ArtSpace Manila is a contemporary art gallery and event space in the heart of Makati City. We provide a platform for emerging street artists to showcase their work through monthly exhibitions and live painting events. Our mission is to bridge the gap between street art and traditional gallery spaces.
                        </div>
                    </div>
                </div>

                <div class="partner-documents">
                    <h3 class="documents-title">Partnership Documents</h3>
                    <div class="document-item">
                        <div class="document-icon"><i class="fas fa-file-pdf"></i></div>
                        <div class="document-info">
                            <div class="document-name">Partnership Agreement.pdf</div>
                            <div class="document-meta">Signed on Jan 15, 2024 • 2.4 MB</div>
                        </div>
                        <div class="document-actions">
                            <button class="action-btn" title="Download">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="action-btn" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="document-item">
                        <div class="document-icon"><i class="fas fa-file-image"></i></div>
                        <div class="document-info">
                            <div class="document-name">Venue Photos.zip</div>
                            <div class="document-meta">Uploaded on Jan 10, 2024 • 8.7 MB</div>
                        </div>
                        <div class="document-actions">
                            <button class="action-btn" title="Download">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="action-btn" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary">Message Partner</button>
                <button class="btn btn-secondary">View Events</button>
                <button class="btn btn-primary">Renew Partnership</button>
            </div>
        </div>
    </div>

    <!-- Add Artist Modal -->
    <div class="modal" id="addArtistModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add New Artist</h3>
                <button class="modal-close" onclick="closeModal('addArtistModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-container">
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" placeholder="Enter first name">
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" placeholder="Enter last name">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" placeholder="Enter username">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <select class="form-control">
                            <option>Select location</option>
                            <option>New York</option>
                            <option>London</option>
                            <option>Berlin</option>
                            <option>Tokyo</option>
                            <option>Los Angeles</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bio</label>
                        <textarea class="form-control form-textarea" placeholder="Enter artist bio"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Artist Tags</label>
                        <input type="text" class="form-control" placeholder="Enter tags separated by commas">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Verification Status</label>
                        <select class="form-control">
                            <option>Unverified</option>
                            <option>Verified</option>
                            <option>Pending Verification</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('addArtistModal')">Cancel</button>
                <button class="btn btn-primary">Save Artist</button>
            </div>
        </div>
    </div>

    <!-- Add Partner Modal -->
    <div class="modal" id="addPartnerModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add New Partner</h3>
                <button class="modal-close" onclick="closeModal('addPartnerModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-container">
                    <div class="form-group">
                        <label class="form-label">Organization Name</label>
                        <input type="text" class="form-control" placeholder="Enter organization name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Partner Type</label>
                        <select class="form-control">
                            <option>Select type</option>
                            <option>Venue Partner</option>
                            <option>Art Supply</option>
                            <option>Sponsor</option>
                            <option>Cultural Org</option>
                            <option>Brand Collab</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">Contact Person</label>
                                <input type="text" class="form-control" placeholder="Enter contact name">
                            </div>
                        </div>
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label">Contact Email</label>
                                <input type="email" class="form-control" placeholder="Enter contact email">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" placeholder="Enter phone number">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Country</label>
                        <select class="form-control">
                            <option>Select country</option>
                            <option>United States</option>
                            <option>United Kingdom</option>
                            <option>Germany</option>
                            <option>Japan</option>
                            <option>Philippines</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Website</label>
                        <input type="url" class="form-control" placeholder="Enter website URL">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Organization Logo</label>
                        <input type="file" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Partnership Agreement</label>
                        <input type="file" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-control">
                            <option>Active</option>
                            <option>Inactive</option>
                            <option>Pending</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('addPartnerModal')">Cancel</button>
                <button class="btn btn-primary">Save Partner</button>
            </div>
        </div>
    </div>

<!-- Before closing </body> -->
<script src="{{ asset('js/loading.js') }}"></script>
</body>
</html>
