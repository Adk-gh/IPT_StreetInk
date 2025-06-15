<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Street & Ink | User Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin-usermanagement.css') }}" rel="stylesheet">
    <link href="{{ asset('css/loading.css') }}" rel="stylesheet">

    <style>
        /* Enhanced Tab styles */
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 0 20px;
        }

        .tab {
            padding: 12px 24px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            font-weight: 500;
            color: #6b7280;
            position: relative;
        }

        .tab:hover {
            color: #4f46e5;
            background-color: #f8f9fa;
        }

        .tab.active {
            border-bottom-color: #4f46e5;
            color: #4f46e5;
            font-weight: 600;
        }

        .tab-content {
            display: none;
            padding: 0 20px;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        /* Toast notification */
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1100;
        }

        /* Avatar styling */
        .avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Badge enhancements */
        .badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    @include('admin.adminsidebar')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        @include('admin.adminnavbar')

        <!-- Toast Notification Container -->
        <div class="toast-container">
            <div id="toast" class="toast align-items-center text-white" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body"></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <div class="tab active" data-target="users-tab-content">Users</div>
            <div class="tab" data-target="artists-tab-content">Artists</div>
        </div>

<!-- Users Tab Content -->
<div class="tab-content active" id="users-tab-content">
    <div class="data-table">
        <div class="table-header">
            <h1 class="table-title">User Management</h1>
            <div class="search-filter-container">
                <div class="input-group">
                    <!-- Search input would go here -->
                </div>
                <div>
                    <button class="btn btn-secondary me-2" id="filterBtn">
                        <i class="fas fa-filter"></i> Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions mb-3">
            <button class="btn btn-outline-danger btn-sm me-2" id="deleteSelectedBtn" disabled>
                <i class="fas fa-trash"></i> Delete Selected (<span id="selectedCount">0</span>)
            </button>
            <button class="btn btn-outline-secondary btn-sm" id="exportUsersBtn">
                <i class="fas fa-download"></i> Export
            </button>
        </div>

        @if(isset($error))
            <div class="alert alert-danger">{{ $error }}</div>
        @endif

        @if(isset($users) && $users->count())
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="40"><input type="checkbox" id="selectAllCheckbox"></th>
                            <th>User</th>
                            <th>Artworks</th>
                            <th>Location</th>
                            <th>Last Active</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td><input type="checkbox" class="user-checkbox" value="{{ $user->user_id }}"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : asset('img/default.jpg') }}"
                                         alt="User Avatar" class="avatar me-3">
                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->posts_count ?? 0 }}</td>
                            <td>{{ $user->location ?? 'Unknown' }}</td>
                            <td>{{ $user->last_active_at ? $user->last_active_at->diffForHumans() : 'Never' }}</td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-sm btn-outline-primary view-user-btn"
                                            data-user-id="{{ $user->user_id }}"
                                            data-user="{{ htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary edit-user-btn"
                                            data-user-id="{{ $user->user_id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-user-btn"
                                            data-user-id="{{ $user->user_id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                </div>
                <div>
                    @if($users->onFirstPage())
                        <span class="btn btn-outline-secondary disabled">Previous</span>
                    @else
                        <a class="btn btn-outline-secondary" href="{{ $users->previousPageUrl() }}">Previous</a>
                    @endif

                    @foreach(range(1, min(5, $users->lastPage())) as $page)
                        @if($page == $users->currentPage())
                            <span class="btn btn-primary disabled">{{ $page }}</span>
                        @else
                            <a class="btn btn-outline-primary" href="{{ $users->url($page) }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($users->hasMorePages())
                        <a class="btn btn-outline-secondary" href="{{ $users->nextPageUrl() }}">Next</a>
                    @else
                        <span class="btn btn-outline-secondary disabled">Next</span>
                    @endif
                </div>
            </div>
        @else
            <div class="alert alert-info">No users found.</div>
        @endif
    </div>
</div>

<!-- Artists Table -->
<div class="tab-content" id="artists-content">
    @if(isset($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    @if(isset($artists) && $artists->count())
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><input type="checkbox" id="selectAllCheckbox"></th>
                    <th>Artist</th>
                    <th>Artworks</th>
                    <th>Location</th>
                    <th>Last Active</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody id="artistsTableBody">
                @foreach($artists as $user)
                    <tr data-user-id="{{ $user->user_id }}">
                        <td><input type="checkbox" class="user-checkbox" value="{{ $user->user_id }}"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->profile_picture ? Storage::url($user->profile_picture) : asset('img/default.jpg') }}"
                                     alt="User Avatar" class="avatar me-3" style="width: 40px; height: 40px;">
                                <div>
                                    <div class="fw-bold">{{ $user->name }}</div>
                                    <div class="text-muted small">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $user->posts_count ?? 0 }}</td>
                        <td>{{ $user->location ?? 'Unknown' }}</td>
                        <td>{{ $user->last_active_at ? $user->last_active_at->diffForHumans() : 'Never' }}</td>
                        <td class="text-end">
                            <!-- View Button -->
                            <button class="btn btn-sm btn-outline-primary view-user-btn"
                                    data-user-id="{{ $user->user_id }}"
                                    data-user="{{ htmlspecialchars(json_encode([
                                        'user_id' => $user->user_id,
                                        'name' => $user->name,
                                        'email' => $user->email,
                                        'phone' => $user->phone,
                                        'location' => $user->location,
                                        'bio' => $user->bio,
                                        'verified_artist' => $user->verified_artist,
                                        'profile_picture' => $user->profile_picture,
                                        'created_at' => $user->created_at ? $user->created_at->toISOString() : null,
                                        'last_active_at' => $user->last_active_at ? $user->last_active_at->toISOString() : null,
                                        'artworks_count' => $user->posts_count ?? 0
                                    ]), ENT_QUOTES, 'UTF-8') }}">
                                <i class="fas fa-eye"></i>
                            </button>

                            <!-- Edit Button -->
                            <button class="btn btn-sm btn-outline-secondary edit-user-btn"
                                    data-user-id="{{ $user->user_id }}"
                                    data-user-data="{{ htmlspecialchars(json_encode([
                                        'user_id' => $user->user_id,
                                        'name' => $user->name,
                                        'email' => $user->email,
                                        'phone' => $user->phone,
                                        'location' => $user->location,
                                        'bio' => $user->bio,
                                        'verified_artist' => $user->verified_artist,
                                        'profile_picture' => $user->profile_picture
                                    ]), ENT_QUOTES, 'UTF-8') }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <!-- Delete Button -->
                            <button class="btn btn-sm btn-outline-danger delete-user-btn"
                                    data-user-id="{{ $user->user_id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Showing {{ $artists->firstItem() }} to {{ $artists->lastItem() }} of {{ $artists->total() }} artists
            </div>
            <div>
                @if($artists->onFirstPage())
                    <span class="btn btn-outline-secondary disabled">Previous</span>
                @else
                    <a class="btn btn-outline-secondary" href="{{ $artists->previousPageUrl() }}">Previous</a>
                @endif

                @foreach(range(1, min(5, $artists->lastPage())) as $page)
                    @if($page == $artists->currentPage())
                        <span class="btn btn-primary disabled">{{ $page }}</span>
                    @else
                        <a class="btn btn-outline-primary" href="{{ $artists->url($page) }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($artists->hasMorePages())
                    <a class="btn btn-outline-secondary" href="{{ $artists->nextPageUrl() }}">Next</a>
                @else
                    <span class="btn btn-outline-secondary disabled">Next</span>
                @endif
            </div>
        </div>
    @else
        <p>No verified artists found.</p>
    @endif
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <img id="editUserAvatar" src="{{ asset('img/default.jpg') }}" alt="User Avatar" class="rounded-circle mb-2" style="width: 100px; height: 100px;">
                        <div>
                            <label class="btn btn-outline-primary">
                                Change Avatar
                                <input type="file" id="editProfilePicture" name="profile_picture" accept="image/*" hidden>
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editName" class="form-label">Name *</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editEmail" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editPhone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="editPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="editLocation" class="form-label">Location</label>
                        <input type="text" class="form-control" id="editLocation" name="location">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="editVerifiedArtist" name="verified_artist" value="1">
                        <label class="form-check-label" for="editVerifiedArtist">Verified Artist</label>
                    </div>
                    <div class="mb-3">
                        <label for="editBio" class="form-label">Bio</label>
                        <textarea class="form-control" id="editBio" name="bio" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="editPassword" name="password">
                        <small class="form-text text-muted">Minimum 8 characters</small>
                    </div>
                    <div class="mb-3">
                        <label for="editPasswordConfirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="editPasswordConfirmation" name="password_confirmation">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
        <!-- Modal content remains the same as original -->
    </div>

    <!-- JavaScript Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/loading.js') }}"></script>
    <script src="{{ asset('js/admin-usermanagement.js') }}" defer></script>
</body>
</html>
