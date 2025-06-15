<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Street & Ink | Art Uploads Management</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/admin-artupload.css') }}" rel="stylesheet">
    <link href="{{ asset('css/loading.css') }}" rel="stylesheet">
</head>
<body>
    @include('admin.adminsidebar')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        @include('admin.adminnavbar')

        <!-- View Toggle -->
        <div class="view-toggle">
            <button class="view-btn active" id="tableViewBtn">
                <i class="fas fa-list"></i> Table View
            </button>
            <button class="view-btn" id="gridViewBtn">
                <i class="fas fa-th-large"></i> Grid View
            </button>
        </div>

        <!-- Filters Section -->
        <div class="filter-section">
            <div class="filter-row">
                <div class="filter-group">
                    <label class="form-label">Status</label>
                    <select class="form-control">
                        <option>All Statuses</option>
                        <option>Pending</option>
                        <option>Approved</option>
                        <option>Rejected</option>
                        <option>Flagged</option>
                        <option>Featured</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="form-label">Artist Type</label>
                    <select class="form-control">
                        <option>All Artists</option>
                        <option>Verified Only</option>
                        <option>Regular Users</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="form-label">Location</label>
                    <select class="form-control">
                        <option>All Locations</option>
                        <option>New York, USA</option>
                        <option>London, UK</option>
                        <option>Berlin, Germany</option>
                        <option>Tokyo, Japan</option>
                        <option>Other</option>
                    </select>
                </div>
            </div>
            <div class="filter-row">
                <div class="filter-group">
                    <label class="form-label">Date Range</label>
                    <select class="form-control">
                        <option>All Time</option>
                        <option>Today</option>
                        <option>This Week</option>
                        <option>This Month</option>
                        <option>Last 3 Months</option>
                        <option>Custom Range</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="form-label">Tag/Style</label>
                    <select class="form-control">
                        <option>All Tags</option>
                        <option>Graffiti</option>
                        <option>Mural</option>
                        <option>Stencil</option>
                        <option>Installation</option>
                        <option>Political</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="form-label">Sort By</label>
                    <select class="form-control">
                        <option>Newest First</option>
                        <option>Oldest First</option>
                        <option>Most Popular</option>
                        <option>Most Reported</option>
                        <option>Title (A-Z)</option>
                        <option>Title (Z-A)</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button class="btn btn-secondary">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
                <button class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions">
            <input type="checkbox" id="selectAll" class="bulk-checkbox">
            <label for="selectAll">Select All</label>

            <button class="btn btn-secondary btn-sm" id="deleteSelectedBtn">
                <i class="fas fa-trash"></i> Delete Selected
            </button>
        </div>

        <div class="data-table" id="tableView">
            <div class="table-header">
                <h3 class="table-title">Social Feed Posts</h3>
            </div>
            <div class="table-container">
                <table class="posts-table">
                    <thead>
                        <tr>
                            <th style="width: 30px;"><input type="checkbox" id="selectAllHeader"></th>
                            <th>Artwork</th>
                            <th>Artist</th>
                            <th>Location</th>
                            <th>Tags</th>
                            <th>Uploaded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts ?? [] as $post)
                        @php
                            $isShared = $post instanceof \App\Models\SharedPost;
                            $originalPost = $isShared ? $post->post : $post;
                            $sharedByUser = $isShared ? $post->user : null;
                        @endphp
                        <tr data-post-id="{{ $originalPost->id ?? '' }}">
                            <td><input type="checkbox" class="post-checkbox"></td>
                            <td>
                                <div class="art-cell">
                                    @if($originalPost->image_url ?? false)
                                    <div class="art-thumb">
                                        <img src="{{ asset('storage/' . $originalPost->image_url) }}"
                                             alt="Post Image">
                                    </div>
                                    @endif
                                    <div class="art-info">
                                        <div class="art-title">{{ Str::limit($originalPost->caption ?? 'Untitled', 20) }}</div>
                                        @if($isShared)
                                        <div class="shared-by">Shared by {{ $sharedByUser->name ?? 'User' }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-sm">
                                        <img src="{{ ($originalPost->user->profile_picture ?? false) ? asset('storage/' . $originalPost->user->profile_picture) : asset('img/default.jpg') }}"
                                             alt="User Avatar">
                                    </div>
                                    <div class="user-info">
                                        <div class="user-name-sm">{{ $originalPost->user->name ?? 'Unknown' }}</div>
                                        <div class="user-email">{{ $originalPost->user->email ?? 'unknown' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ Str::limit($originalPost->location_name ?? 'Unknown', 15) }}</td>
                            <td>
                                <div class="tags">
                                    <span class="tag">#{{ $isShared ? 'shared' : 'original' }}</span>
                                    @if($originalPost->image_url ?? false)
                                    <span class="tag">#photo</span>
                                    @endif
                                    @if($originalPost->tags)
                                        @foreach(explode(',', $originalPost->tags) as $tag)
                                            <span class="tag">#{{ trim($tag) }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                            <td>{{ ($originalPost->created_at ?? now())->diffForHumans() }}</td>
                            <td>
                                <div class="action-btns">
                                    <button class="action-btn view" title="View" data-post-id="{{ $originalPost->id ?? '' }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn approve" title="Approve" data-post-id="{{ $originalPost->id ?? '' }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="action-btn reject" title="Reject" data-post-id="{{ $originalPost->id ?? '' }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No posts found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($posts) && $posts->count())
            <div class="pagination">
                <div class="pagination-info">Showing {{ $posts->firstItem() }} to {{ $posts->lastItem() }} of {{ $posts->total() }} posts</div>
                <div class="pagination-btns">
                    @if($posts->onFirstPage())
                        <button class="page-btn disabled"><i class="fas fa-chevron-left"></i></button>
                    @else
                        <a href="{{ $posts->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                    @endif

                    @foreach(range(1, $posts->lastPage()) as $page)
                        @if($page == $posts->currentPage())
                            <button class="page-btn active">{{ $page }}</button>
                        @else
                            <a href="{{ $posts->url($page) }}" class="page-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($posts->hasMorePages())
                        <a href="{{ $posts->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                    @else
                        <button class="page-btn disabled"><i class="fas fa-chevron-right"></i></button>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Grid View (Dynamic) -->
        <div class="art-grid" id="gridView" style="display: none;">
            @forelse($posts ?? [] as $post)
                @php
                    $isShared = $post instanceof \App\Models\SharedPost;
                    $originalPost = $isShared ? $post->post : $post;
                    $sharedByUser = $isShared ? $post->user : null;
                @endphp
                <div class="art-card">
                    @if($originalPost->image_url ?? false)
                        <img src="{{ asset('storage/' . $originalPost->image_url) }}"
                             alt="{{ $originalPost->caption ?? 'Artwork' }}"
                             class="art-card-img">
                    @endif
                    <div class="art-card-body">
                        <div class="art-card-title">{{ Str::limit($originalPost->caption ?? 'Untitled', 25) }}</div>
                        <div class="art-card-meta">
                            <span>{{ '@' . ($originalPost->user->username ?? 'unknown') }}</span><br>
                            <span class="art-card-date">{{ ($originalPost->created_at ?? now())->diffForHumans() }}</span>
                            <span>{{ $originalPost->location_name ?? 'Unknown' }}</span>
                        </div>
                        <div class="tags">
                            <span class="tag">#{{ $isShared ? 'shared' : 'original' }}</span>
                            @if($originalPost->image_url)
                                <span class="tag">#photo</span>
                            @endif
                            @if($originalPost->tags)
                                @foreach(explode(',', $originalPost->tags) as $tag)
                                    <span class="tag">#{{ trim($tag) }}</span>
                                @endforeach
                            @endif
                        </div>
                        <div class="art-card-footer">
                            <div class="art-card-actions">
                                <button class="action-btn view" title="View" data-post-id="{{ $originalPost->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-btn approve" title="Approve" data-post-id="{{ $originalPost->id }}">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="action-btn reject" title="Reject" data-post-id="{{ $originalPost->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center w-full">No posts found.</p>
            @endforelse
        </div>

        <!-- Artwork Detail Modal -->
        <div class="modal" id="artModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="modalArtTitle">Artwork Details</h3>
                    <button class="modal-close" onclick="closeArtModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <img id="modalArtImage" src="" alt="Artwork Preview" class="art-preview">
                    <div class="art-details">
                        <div>
                            <div class="detail-group">
                                <div class="detail-label">Artist</div>
                                <div class="detail-value" id="modalArtist">
                                    <!-- Dynamic content will go here -->
                                </div>
                            </div>
                            <div class="detail-group">
                                <div class="detail-label">Location</div>
                                <div class="detail-value" id="modalLocation">
                                    <!-- Dynamic content will go here -->
                                </div>
                            </div>
                            <div class="detail-group">
                                <div class="detail-label">Coordinates</div>
                                <div class="detail-value" id="modalCoordinates">
                                    <!-- Dynamic content will go here -->
                                </div>
                            </div>
                            <div class="detail-group">
                                <div class="detail-label">Upload Date</div>
                                <div class="detail-value" id="modalUploadDate">
                                    <!-- Dynamic content will go here -->
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="detail-group">
                                <div class="detail-label">Tags</div>
                                <div class="tags" id="modalTags">
                                    <!-- Dynamic content will go here -->
                                </div>
                            </div>
                            <div class="detail-group">
                                <div class="detail-label">Description</div>
                                <div class="detail-value" id="modalDescription">
                                    <!-- Dynamic content will go here -->
                                </div>
                            </div>
                            <div class="detail-group">
                                <div class="detail-label">Reports</div>
                                <div class="detail-value" id="modalReports">
                                    No reports
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="closeArtModal()">
                        <i class="fas fa-times"></i> Close
                    </button>
                    <button class="btn btn-secondary" id="modalEditBtn">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Scripts -->
    <script src="{{ asset('js/admin-artupload.js') }}"></script>
    <script src="{{ asset('js/loading.js') }}"></script>
</body>
</html>