<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Street & Ink | Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/admin-navbar.css') }}" rel="stylesheet">
    <script src="{{ asset('js/admin-navbar.js') }}" defer></script>



</head>
<body>
    <div class="header">
            <div class="header-left">
                <h1 class="header-title">{{ $pageTitle ?? 'Admin Dashboard' }}</h1>
            </div>
            <div class="header-right">
                <div class="search-bar">
                    <input type="text" class="search-input" placeholder="Search...">
                    <button class="search-btn"><i class="fas fa-search"></i></button>
                </div>

                <button class="theme-toggle" id="themeToggle">
                    <i class="fas fa-moon"></i>
                </button>

            </div>
        </div>

</body>
</html>
