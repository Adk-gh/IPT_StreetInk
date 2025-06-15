<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Street & Ink - Profile Setup</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/ProfileSetup.css') }}" rel="stylesheet">
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

</head>
<body>
    <!-- Split Layout -->
    <div class="split-layout">
        <!-- Left Side - Art Background -->
        <div class="art-side">
            <div>
                <a href="{{ route('profile') }}" class="logo large-logo" style="color: white;">
                    <img src="img/SI.png" alt="Street & Ink Logo" style="width: 40px; height: 40px; margin-right: 10px;">
                    Street & <span>Ink</span>
                </a>
            </div>
            <div class="art-content">
                <h2 style="font-family: 'Space Grotesk', sans-serif; margin-bottom: 20px; font-size: 1.8rem;">Complete Your Profile</h2>
                <p>Help other artists and enthusiasts get to know you. Your profile is your canvas - make it expressive!</p>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="form-side">
            <div class="form-container">


                <!-- Progress Steps Indicator -->
                <div class="steps">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                        <div class="step-label">Basic Info</div>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                        <div class="step-label">Community</div>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-number">3</div>
                        <div class="step-label">Art Identity</div>
                    </div>
                    <div class="steps-line"></div>
                </div>

                <!-- Progress Bar -->
                <div class="progress-container">
                    <div class="progress-bar" id="progressBar" style="width: 33%"></div>
                </div>

                <!-- Step 1: Basic Info -->
                <div class="form-step active" id="step1">
                    <div class="form-header">
                        <h1>Customize Your Profile</h1>
                        <p>Step 1 of 3: Share some basic info with the community</p>
                    </div>

                    <form id="profileForm" action="{{ route('profile.setup.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
<div class="avatar-upload">
    <div class="avatar-preview" id="avatarPreview">
        <div class="avatar-placeholder">
            <i class="fas fa-user"></i>
        </div>
        <img id="avatarImage" src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : '' }}" 
             alt="Preview" style="{{ Auth::user()->profile_picture ? 'display: block;' : 'display: none;' }}">
    </div>
    <input type="file" name="profile_picture" id="avatarInput" class="avatar-input" accept="image/*">
    <input type="hidden" name="remove_profile_picture" id="removeProfilePicture" value="0">
    <div class="avatar-actions">
        <label for="avatarInput" class="avatar-btn">
            <i class="fas fa-cloud-upload-alt"></i> Choose photo
        </label>
        <button type="button" class="avatar-btn remove" id="removeBtn" 
                style="{{ Auth::user()->profile_picture ? 'display: inline-flex;' : 'display: none;' }}">
            <i class="fas fa-trash"></i> Remove
        </button>
    </div>
</div>

                       <!-- In ProfileSetup.blade.php, update the form fields like this: -->

<!-- Display Name -->
<div class="form-group">
    <label for="displayName">Display Name / Artist Name</label>
    <input type="text" id="displayName" name="username" class="form-control" 
           placeholder="e.g., ShadowStreak" required
           value="{{ Auth::user()->username ?? '' }}">
</div>

<!-- Location -->
<div class="form-group">
    <label for="location">Location</label>
    <input type="text" id="location" name="location" class="form-control" 
           placeholder="Your city or country"
           value="{{ Auth::user()->location ?? '' }}">
    <small style="color: var(--text-light); font-size: 0.8rem; display: block; margin-top: 5px;">Helps us connect you with local artists</small>
</div>

<!-- Short Bio -->
<div class="form-group">
    <label for="bio">Short Bio</label>
    <textarea id="bio" name="bio" rows="3" class="form-control" 
              placeholder="Tell us about yourself or your art style...">{{ Auth::user()->bio ?? '' }}</textarea>
    <small style="color: var(--text-light); font-size: 0.8rem; display: block; margin-top: 5px;">Max 150 characters</small>
</div>

                        <div class="form-navigation">
                            <button type="button" class="btn btn-primary" id="nextBtn1">Continue</button>
                        </div>
                </div>

                <!-- Step 2: Community Details -->
                <div class="form-step" id="step2">
                    <div class="form-header">
                        <h1>Community Details</h1>
                        <p>Step 2 of 3: Tell us about your role in the street art community</p>
                    </div>

                    <!-- Birthdate -->
<div class="form-group">
    <label for="birthdate">Birthdate</label>
    <div class="date-input-container">
        <i class="fas fa-calendar-alt"></i>
        <input type="text" id="birthdate" name="birthdate" class="form-control" placeholder="Select date" readonly>
    </div>
</div>
                    <!-- Phone Number -->
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" class="form-control" 
                               placeholder="Your phone number"
                               value="{{ Auth::user()->phone ?? '' }}">
                    </div>

                  <!-- Social Links -->
            <div class="form-group">
                <label>Connect your social profiles (optional)</label>
                <div class="social-link">
                    <i class="fab fa-instagram"></i>
                    <input type="text" class="form-control" name="instagram" 
                        placeholder="Instagram username"
                        value="{{ Auth::user()->instagram ?? '' }}">
                </div>

                <div class="social-link">
                    <i class="fab fa-twitter"></i>
                    <input type="text" class="form-control" name="twitter" 
                        placeholder="Twitter username"
                        value="{{ Auth::user()->twitter ?? '' }}">
                </div>

                <div class="social-link">
                    <i class="fab fa-facebook"></i>
                    <input type="text" class="form-control" name="facebook" 
                        placeholder="Facebook username or URL"
                        value="{{ Auth::user()->facebook ?? '' }}">
                </div>
                </button>
                                    <button type="button" class="btn btn-primary" id="nextBtn2">Continue</button>
            </div>
              </div>

                <!-- Step 3: Art Identity -->
                <div class="form-step" id="step3">
                    <div class="form-header">
                        <h1>Art Identity</h1>
                        <p>Step 3 of 3: Express your street art identity</p>
                    </div>

                    <!-- Profile Banner Upload -->
                    <div class="form-group">
                        <label>Profile Banner</label>
                        <div class="banner-preview" id="bannerPreview">
                            <i class="fas fa-mountain"></i>
                            <img id="bannerImage" src="" alt="Banner Preview">
                        </div>
                        <div class="banner-actions">
                            <button type="button" class="banner-btn" id="uploadBannerBtn">
                                <i class="fas fa-upload"></i> Upload Banner
                            </button>
                            <button type="button" class="banner-btn remove" id="removeBannerBtn" style="display: none;">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                            <input type="file" name="cover_photo" id="bannerInput" class="banner-input" accept="image/*">
                        </div>
                        <div class="banner-tooltip">
                            <i class="fas fa-info-circle"></i>
                            <span>Upload a wide banner that represents your vibe or wall art (1200×400px recommended)</span>
                        </div>
                    </div>

                    <!-- Street Art Style Tags -->
                    <div class="form-group">
                        <label>Street Art Style (choose up to 3)</label>
                        <div class="tags-grid">
                            @foreach($tags as $tag)
                                <div class="tag-item">
                                    <input type="checkbox"
                                        id="tag-{{ $tag->id }}"
                                        name="style_tag[]"
                                        value="{{ $tag->name }}">
                                    <label for="tag-{{ $tag->id }}">{{ $tag->name }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="tag-limit" id="tagCounter">0/3 tags selected</div>
                    </div>

                    <div class="form-navigation">
                        <button type="button" class="btn btn-back" id="backBtn2">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="submit" class="btn btn-primary">Finish Setup</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Add this AFTER jQuery but BEFORE your ProfileSetup.js -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
   flatpickr("#birthdate", {
  theme: "none", // Disable default theme
  dateFormat: "Y-m-d",
  maxDate: "today",
  disableMobile: true, // Force desktop UI on mobile
  nextArrow: '<i class="fas fa-chevron-right"></i>',
  prevArrow: '<i class="fas fa-chevron-left"></i>',
});

document.addEventListener('DOMContentLoaded', function() {
    // Pre-fill existing images
    const existingAvatar = "{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : '' }}";
    if (existingAvatar) {
        document.getElementById('avatarImage').src = existingAvatar;
    }

    const existingBanner = "{{ Auth::user()->cover_photo ? asset('storage/' . Auth::user()->cover_photo) : '' }}";
    if (existingBanner) {
        document.getElementById('bannerImage').src = existingBanner;
    }
  });

</script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ asset('js/ProfileSetup.js') }}"></script>
</body>
</html>
