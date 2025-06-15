<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header | Street & Ink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
     <link href="{{ asset('css/header.css') }}" rel="stylesheet">
    <script src="{{ asset('js/header.js') }}"></script>
</head>
<body>
    <header id="header">
        <div class="container header-container">
            <a href="{{ route('home') }}" class="logo" >
                    <img src="img/SI.png" alt="Street & Ink Logo" style="width: 40px; height: 40px; margin-right: 10px;" class="logo-img">
                    Street & <span>Ink</span>
                </a>
            <div class="header-right">
                <nav id="nav">
                    <ul>
                        @auth
                         <li><a href="{{ route('social_feed') }}">Social Feed</a></li>
                        <li><a href="{{ route('articles') }}">Articles</a></li>
                        <li><a href="{{ route('artist') }}">Artists</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                        <li><a href="{{ route('aboutus') }}">About</a></li>

                        <li class="theme-toggle-container">
                            <button class="theme-toggle" id="themeToggle">
                                <i class="fas fa-moon"></i>
                            </button>
                        </li>

                        <li class="profile-container">
                            <button class="profile-btn">
                                <span>{{ Auth::user()->name }}</span>
                                <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : 'img/default.jpg' }}" alt="Profile" class="profile-img">
                            </button>
                            <div class="profile-dropdown">
                                <a href="{{ route('profile') }}"><i class="fas fa-user-cog mr-2"></i> Manage Profile</a>
                                <div class="divider"></div>
                                @auth
                                    @if(Auth::user()->email === 'admin@gmail.com')
                                        <a href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                                        </a>
                                    @endif
                                @endauth
                                 <div class="divider"></div>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Sign Out
                                </a>
                            </div>
                        </li>
                        @else
                        <li><a href="{{ route('register') }}" class="btn btn-primary">Sign Up</a></li>
                        @endauth
                    </ul>
                </nav>

                <button class="mobile-menu-btn" id="mobileMenuBtn">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="logout-form" style="display: none;">
        @csrf
    </form>
</body>
</html>
