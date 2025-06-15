<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Social Feed | Street & Ink</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="theme-color" content="#000000"> <!-- Optional, for mobile status bar color -->

        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">

        <!-- Fonts and Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-yada..." crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-..." crossorigin="anonymous">

        <!-- Leaflet -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha384-..." crossorigin=""/>

        <!-- Custom CSS -->
        <link href="{{ asset('css/socialfeed.css') }}" rel="stylesheet">
        <link href="{{ asset('css/loading.css') }}" rel="stylesheet">


    <!-- Meta -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ Auth::id() ?? 'guest' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>
    <header id="header">
        @include('header')
    </header>

    @if(session('success'))
        <input type="hidden" id="postSuccessMessage" value="{{ session('success') }}">
    @endif

    <main class="social-feed-container">
        <!-- Hero Section -->
        <section class="social-hero">
            <div class="container">
                <h1 class="section-title">Social Feed</h1>
                <p class="text-center">Real-time street art drops, user activity, and artist moments from around the world.</p>
            </div>
        </section>

        <!-- Main Content -->
        <section class="section pt-0">
            <div class="container">
                <div class="social-container">
                    <div class="feed-content">
                        <!-- Post Creation Form -->
<div class="create-post card mb-4">
    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" id="postForm">
        @csrf
        <div class="card-body">
            <div class="create-post-header mb-3">
                <div class="user-avatar d-flex align-items-center gap-5">
                    <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : 'img/default.jpg' }}"
                         alt="User Avatar"
                         class="rounded-circle me-2">
                    <span class="fw-bold">{{ Auth::user()->username }}</span>
                </div>
                <input type="text" name="caption" class="form-control post-input mt-2" placeholder="What street art did you discover today?" required>
            </div>

            <!-- Preview Area -->
            <div id="photoPreviewContainer" style="margin-top: 10px; display: none;">
                <img id="photoPreview" src="" alt="Selected Photo Preview" style="max-width: 100%; border-radius: 8px;">
                <div id="locationPreview" style="margin-top:10px; font-weight: bold; color: #333; display:none;"></div>
                <div id="tagsPreview" style="margin-top:10px; font-weight: bold; color: #333; display:none;"></div>
            </div>

            <input type="file" id="photoInput" accept="image/*" name="image_url" class="d-none">

            <!-- Post Actions -->
            <div class="post-actions d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary post-action-btn" onclick="document.getElementById('photoInput').click();">
                    <i class="fas fa-image me-1"></i> Photo
                </button>

                <!-- Location Modal Trigger -->
                <button type="button" class="btn btn-outline-secondary post-action-btn" id="locationToggle">
                    <i class="fas fa-map-marker-alt me-1"></i> Location
                </button>

                <div id="locationModal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.6); z-index: 9999; justify-content: center; align-items: center;">
                    <div style="background: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 600px; position: relative;">
                        <button type="button" id="closeModal" style="position: absolute; top: 10px; right: 15px; font-size: 1.5rem; border:none; background:none; cursor:pointer;">×</button>
                        <h3>Select Location</h3>
                        <div id="map" style="height: 400px; margin-bottom: 10px;"></div>
                        <input type="text" name="location_name" id="location_name" placeholder="Location name" style="width: 100%; padding: 8px; margin-bottom: 10px;"/>
                        <input type="hidden" name="latitude" id="latitude" />
                        <input type="hidden" name="longitude" id="longitude" />
                        <button type="button" id="clearLocation" class="btn btn-secondary">Clear Location</button>
                        <button type="button" id="saveLocation" class="btn btn-primary" style="float: right;">Save Location</button>
                        <div style="clear: both;"></div>
                    </div>
                </div>

                <!-- Tags Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary post-action-btn" type="button" id="tagsButton">
                        <i class="fas fa-tag me-1"></i> Tags
                    </button>
                    <div class="dropdown-menu p-3" id="tagsDropdown" style="display: none;">
                        @foreach ($tags as $tag)
                            <div class="form-check">
                                <input class="form-check-input tag-checkbox" type="checkbox" name="tags[]" value="{{ $tag->name }}" id="tag{{ $tag->id }}">
                                <label class="form-check-label" for="tag{{ $tag->id }}">{{ $tag->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="tags[]" id="selectedTags">

                <button type="button" class="btn btn-primary ms-auto" id="postSubmitBtn">Post</button>
            </div>
        </div>
    </form>
</div>


                       <!-- Feed Tabs -->
<ul class="feed-tabs">
    <li class="feed-tab">
        <a class="feed-tab-link active" href="#" data-filter="all">All</a>
    </li>
    <li class="feed-tab">
        <a class="feed-tab-link" href="#" data-filter="following">Following</a>
    </li>
    <li class="feed-tab">
        <a class="feed-tab-link" href="#" data-filter="popular">Popular</a>
    </li>
    <li class="feed-tab">
        <a class="feed-tab-link" href="#" data-filter="shared">Shared</a>
    </li>
</ul>

<!-- Feed Posts Section -->
<div class="feed-posts mb-4">
    @if($posts->isEmpty())
    <div class="alert alert-info">No posts found.</div>
@else
    @foreach($posts as $post)

        @php
    $isShared = $post instanceof \App\Models\SharedPost;
    $originalPost = $isShared ? $post->post : $post;
    $sharedByUser = $isShared ? $post->user : null;
    $originalAuthor = $originalPost->user;
    $commentable = $post;
@endphp


        <div class="post-card card mb-4" data-post-id="{{ $originalPost->id }}">
            @if($isShared)
    <div class="shared-by-info card-header bg-light d-flex align-items-center">
        <img src="{{ $sharedByUser?->profile_picture ? asset('storage/' . $sharedByUser->profile_picture) : asset('img/default.jpg') }}"
             class="rounded-circle me-2" style="width: 50px; height: 50px; object-fit: cover;">
        <div>
           <strong>
    {{ $sharedByUser?->username ?? $sharedByUser?->name ?? 'Unknown User' }}
</strong> shared this post
            <small class="text-muted ms-2">
                <i class="far fa-clock me-1"></i> {{ $post->created_at->diffForHumans() }}
            </small>

            @if($post->caption)
                <p class="mb-0"><em>"{{ $post->caption }}"</em></p>
            @endif
        </div>
    </div>
@endif


                <div class="card-body">
                    <div class="post-header d-flex justify-content-between mb-3">
                        <div class="post-user d-flex align-items-center">
                            <div class="post-user-avatar me-3">
                                <img src="{{ $originalPost->user?->profile_picture ? asset('storage/' . $originalPost->user->profile_picture) : asset('img/default.jpg') }}"
                                    alt="User Avatar" class="rounded-circle" style="width: 50px; height: 50px;">
                            </div>
                            <div class="post-user-info">
                                <h5 class="mb-0">{{ $originalPost->user?->username ?? $originalPost->user?->name ?? 'Unknown User' }}</h5>
                                <small class="text-muted">{{ $originalPost->user?->email ?? 'unknown-user@example.com' }}</small>
                            </div>
                        </div>
                        <div class="post-meta text-end">
                            <a href="#"> <div class="text-muted" title="{{ $originalPost->location_name }}">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ \Illuminate\Support\Str::limit($originalPost->location_name ?? 'Unknown', 30) }}
                            </div>
                            </a>
                            <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $originalPost->created_at->diffForHumans() }}</small>

                        </div>
           <!-- Three-dot menu button -->
<div class="dropdown d-inline-block ms-2">
    <button class="btn btn-sm btn-link text-muted" type="button" id="postOptionsDropdown-{{ $originalPost->id }}" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-ellipsis-h"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="postOptionsDropdown-{{ $originalPost->id }}">
        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#reportModal-{{ $originalPost->id }}">Report Post</a></li>
    </ul>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal-{{ $originalPost->id }}" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Report Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('posts.report', $originalPost->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Why are you reporting this post?</p>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="report_reason" id="reason1-{{ $originalPost->id }}" value="Inappropriate Content" checked>
                            <label class="form-check-label" for="reason1-{{ $originalPost->id }}">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Inappropriate Content
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="report_reason" id="reason2-{{ $originalPost->id }}" value="Hate Speech">
                            <label class="form-check-label" for="reason2-{{ $originalPost->id }}">
                                <i class="fas fa-ban text-danger me-2"></i>Hate Speech
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="report_reason" id="reason3-{{ $originalPost->id }}" value="Harassment">
                            <label class="form-check-label" for="reason3-{{ $originalPost->id }}">
                                <i class="fas fa-user-slash text-danger me-2"></i>Harassment
                            </label>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label for="additionalInfo-{{ $originalPost->id }}" class="form-label">Additional details (optional):</label>
                        <textarea class="form-control" id="additionalInfo-{{ $originalPost->id }}" name="additional_info" rows="3" placeholder="Please provide more information..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-flag me-1"></i> Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
                    </div>

                    <div class="post-content mb-3">
                        <div class="post-text mb-3">{{ $originalPost->caption }}</div>
                        @if($originalPost->image_url)
                            <div class="post-image mb-3">
                                <img src="{{ asset('storage/' . $originalPost->image_url) }}"
                                    class="img-fluid rounded clickable-image"
                                    alt="Post Image">
                            </div>
                        @endif
                    </div>

                    <div class="post-actions d-flex justify-content-between border-top border-bottom py-2 mb-3">
@auth
                        <!-- Like -->
            <!-- Replace your like button with this -->
<button class="like-btn btn btn-sm btn-outline-secondary"
        data-likeable-id="{{ $commentable->id }}"
        data-likeable-type="{{ get_class($commentable) }}"
        data-liked="{{ $commentable->isLikedByUser() ? 'true' : 'false' }}">
    <i class="{{ $commentable->isLikedByUser() ? 'fas fa-heart text-danger' : 'far fa-heart' }} me-1"></i>
    <span class="like-count">{{ $commentable->likes_count }}</span>
</button>

@endauth



                        <!-- Comment -->
                        <button class="post-action-btn comment-btn btn btn-sm btn-outline-secondary"
                                data-bs-toggle="modal"
                                data-bs-target="#commentModal-{{ $commentable->id }}"
                                data-is-shared="{{ $isShared ? 'true' : 'false' }}">
                            <i class="far fa-comment me-1"></i>
                            <span class="comment-count">{{ $commentable->comments->count() }}</span>
                        </button>

                        <!-- Share -->
                       <button class="post-action-btn share-btn btn btn-sm btn-outline-secondary"
        data-bs-toggle="modal"
        data-bs-target="#shareModal-{{ $originalPost->id }}"
        data-post-id="{{ $originalPost->id }}">
    <i class="fas fa-share me-1"></i>

</button>

                        <!-- Save -->
                        <button class="post-action-btn save-btn btn btn-sm btn-outline-secondary"
        data-post-id="{{ $originalPost->id }}"
        data-saved="{{ auth()->check() && auth()->user()->hasSavedPost($originalPost->id) ? 'true' : 'false' }}">
    <i class="{{ auth()->check() && auth()->user()->hasSavedPost($originalPost->id) ? 'fas' : 'far' }} fa-bookmark me-1"></i>
    Save
</button>
                    </div>

             @php
    $firstComment = $commentable->comments?->first();
@endphp

@if($firstComment)
    @php
        $user = $firstComment->user;
        $username = $user->username ?? $user->name ?? 'Unknown User';
        $profilePic = $user?->profile_picture
            ? asset('storage/' . $user->profile_picture)
            : asset('img/default.jpg');
    @endphp

    <div class="comment d-flex align-items-start mb-3">
        <img src="{{ $profilePic }}" class="rounded-circle me-2" style="width: 40px; height: 40px;">
        <div class="flex-grow-1">
            <strong>{{ $username }}</strong>
            <p class="mb-1">{{ $firstComment->text }}</p>
            <small class="text-muted">{{ $firstComment->created_at->diffForHumans() }}</small>
        </div>
    </div>
@endif

@if($commentable->comments->count() > 1)
    <button type="button" class="btn btn-link p-0 mb-3"
            data-bs-toggle="modal"
            data-bs-target="#commentModal-{{ $commentable->id }}"
            style="text-decoration: underline;">
        View all {{ $commentable->comments->count() }} comments
    </button>
@endif



                    <!-- Add Comment -->

                    <form action="{{ route('comments.store') }}" method="POST" class="add-comment d-flex">
                        @csrf
                        <input type="hidden" name="commentable_id" value="{{ $commentable->id }}">
                        <input type="hidden" name="commentable_type" value="{{ get_class($commentable) }}">
                        <input type="text" name="text" class="form-control me-1" placeholder="Write a comment..." required>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </form>
                </div>
            </div>

            <div class="modal fade" id="commentModal-{{ $commentable->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Comments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($commentable->comments->count())
                    @foreach($commentable->comments as $comment)
                        <div class="d-flex mb-3">
                            <img src="{{ $comment->user?->profile_picture ? asset('storage/' . $comment->user->profile_picture) : asset('img/default.jpg') }}"
                                class="rounded-circle me-2" style="width: 40px; height: 40px;">
                            <div>
                                <strong>{{ $comment->user?->username ?? $comment->user?->name ?? 'Unknown User' }}</strong>
                                <p class="mb-1">{{ $comment->text }}</p>
                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">No comments yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

            <!-- Share Modal -->
            <div class="modal fade" id="shareModal-{{ $originalPost->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('posts.share', $originalPost->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">Share this post</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <textarea name="caption" class="form-control" rows="3" placeholder="Add a caption..."></textarea>
                                </div>
                                <div class="border rounded p-3 bg-light">
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="{{ $originalPost->user?->profile_picture ? asset('storage/' . $originalPost->user->profile_picture) : asset('img/default.jpg') }}"
                                            alt="User Avatar" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                                        <div>
                                            <strong>{{ $originalPost->user?->username ?? $originalPost->user?->name ?? 'Unknown User' }}</strong><br>
                                            <small class="text-muted">{{ $originalPost->created_at->format('Y-m-d') }}</small>
                                        </div>
                                    </div>
                                    <p>{{ $originalPost->caption }}</p>
                                    @if($originalPost->image_url)
                                        <img src="{{ asset('storage/' . $originalPost->image_url) }}" class="img-fluid rounded" alt="Post Image">
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Share</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            <img class="modal-content img-fluid" id="modalImage" alt="Expanded Post Image">
        </div>
    </div>
</div>

<script>document.addEventListener('DOMContentLoaded', function() {
    // Handle like button clicks
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();

            // Check if user is logged in
            const userId = document.querySelector('meta[name="user-id"]').content;
            if (userId === 'guest') {
                window.location.href = "{{ route('login') }}";
                return;
            }

            const likeableId = this.getAttribute('data-likeable-id');
            const likeableType = this.getAttribute('data-likeable-type');
            const isLiked = this.getAttribute('data-liked') === 'true';

            // Add loading state
            const icon = this.querySelector('i');
            const originalIconClass = icon.className;
            icon.className = 'fas fa-spinner fa-spin me-1';
            this.disabled = true;

            try {
                const response = await fetch("{{ route('likes.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        likeable_id: likeableId,
                        likeable_type: likeableType
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to toggle like');
                }

                if (data.success) {
                    // Update button state
                    this.setAttribute('data-liked', data.liked.toString());

                    // Update icon
                    icon.className = data.liked
                        ? 'fas fa-heart text-danger me-1'
                        : 'far fa-heart me-1';

                    // Update count
                    const countElement = this.querySelector('.like-count');
                    if (countElement) {
                        countElement.textContent = data.likes_count;
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred: ' + error.message);
                // Revert to original icon on error
                icon.className = originalIconClass;
            } finally {
                this.disabled = false;
            }
        });
    });
});




//#

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.save-btn').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();

            const postId = this.getAttribute('data-post-id');
            const isSaved = this.getAttribute('data-saved') === 'true';

            // Add loading state
            const icon = this.querySelector('i');
            const originalIconClass = icon.className;
            icon.className = 'fas fa-spinner fa-spin me-1';
            this.disabled = true;

            try {
                const response = await fetch("{{ route('posts.save') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        post_id: postId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Update button state
                    this.setAttribute('data-saved', data.saved.toString());

                    // Update icon
                    icon.className = data.saved
                        ? 'fas fa-bookmark me-1'
                        : 'far fa-bookmark me-1';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to save post');
            } finally {
                this.disabled = false;
            }
        });
    });
});
</script>


    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="{{ asset('js/socialfeed.js') }}" defer></script>
    <script src="{{ asset('js/loading.js') }}"></script>
</body>
</html>
