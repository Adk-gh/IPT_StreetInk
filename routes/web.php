<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileSetupController;
use App\Http\Controllers\SharedPostInteractionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use App\Http\Controllers\SavedPostController;

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ArtUploadController;

use Illuminate\Support\Facades\Auth;

// 🔓 Public Routes (manually guarded in controllers)
Route::get('/', [AuthController::class, 'showSignInForm'])->name('signin');
Route::get('/signup', [AuthController::class, 'showSignUpForm'])->name('register');
Route::post('/signup', [AuthController::class, 'signUp'])->name('register.post');

Route::get('/signin', [AuthController::class, 'showSignInForm'])->name('login');
Route::post('/signin', [AuthController::class, 'signIn'])->name('login.post');

Route::get('/about', [AuthController::class, 'showAboutUs'])->name('aboutus');
Route::get('/contact', [AuthController::class, 'showContact'])->name('contact');
Route::get('/support', [AuthController::class, 'showSupport'])->name('support');
Route::get('/patterns', [AuthController::class, 'showPatterns'])->name('patterns');

// 🔐 Authenticated routes
Route::middleware(['auth','profile.complete'])->group(function () {
    // Main application routes
    Route::get('/home', [AuthController::class, 'showIndex'])->name('home');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::post('/user/cover-update', [AuthController::class, 'updateCoverPhoto'])->name('user.cover.update');
    Route::post('/user/bio-update', [AuthController::class, 'updateBio'])->name('user.bio.update');
    Route::get('/ProfileSetup', [ProfileSetupController::class, 'show'])->name('profile.setup');

    // Posts
    Route::post('/posts', [AuthController::class, 'storePost'])->name('posts.store');
    Route::get('/posts', function () {
        $posts = App\Models\Post::with(['user', 'tags'])->latest()->get();
        return view('posts.index', compact('posts'));
    });

    // Likes & Comments
    Route::post('/posts/{post}/like', [LikeController::class, 'store'])->name('posts.like');
    Route::delete('/posts/{post}/unlike', [LikeController::class, 'destroy'])->name('posts.unlike');
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');

    // Social features
    Route::get('/social-feed', [AuthController::class, 'socialFeed'])->name('social_feed');
    Route::get('/artist', [AuthController::class, 'showArtist'])->name('artist');
    Route::get('/artist/{id}', [AuthController::class, 'showArtistProfile'])->name('artist.profile');
    Route::get('/article', [AuthController::class, 'showArticles'])->name('articles');

    // Tag list
    Route::get('/tags/list', function () {
        return App\Models\Tag::orderBy('name')->get();
    });

  // Admin dashboard routes with simple email check
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'showAdminDashboard'])->name('admin.dashboard');
    Route::get('/ArtistPartners', [AdminController::class, 'showArtistPartner'])->name('admin.ArtistPartners');
    Route::get('/ArtUpload', [AdminController::class, 'showArtUpload'])->name('admin.ArtUpload');
    Route::get('/Backup', [AdminController::class, 'showBackup'])->name('admin.Backup');
    Route::get('/Location', [AdminController::class, 'showLocation'])->name('admin.Location');
    Route::get('/Reports', [AdminController::class, 'showReports'])->name('admin.Reports');
    Route::get('/Settings', [AdminController::class, 'showSettings'])->name('admin.Settings');
    Route::get('/UserManagement', [AdminController::class, 'showUserManagement'])->name('admin.UserManagement');



});

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});

Route::middleware(['auth'])->group(function () {
    Route::get('/ProfileSetup', [ProfileSetupController::class, 'show'])->name('profile.setup');
    Route::post('/ProfileSetup', [ProfileSetupController::class, 'store'])->name('profile.setup.store');
    Route::post('/ProfileSetup/skip', [ProfileSetupController::class, 'skip'])->name('profile.skip');

    Route::middleware(['profile.complete'])->group(function () {
        Route::get('/home', [AuthController::class, 'index'])->name('home');
        // other protected routes here
    });

Route::post('/posts/{post}/share', [AuthController::class, 'share'])->name('posts.share');




Route::get('/users', [UserController::class, 'index'])->name('admin.user.table');
Route::get('/admin/users', [UserController::class, 'index'])->name('admin.UserManagement');


Route::get('/admin/reports/{report}/details', [AdminController::class, 'getReportDetails'])
    ->middleware('auth')
    ->name('reports.details');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');


// routes/web.php

Route::get('/admin/dashboard/chart-data', [DashboardController::class, 'chartData']);

// in web.php or api.php
Route::get('/api/tags', function () {
    return \App\Models\Tag::select('id', 'name')->get();
});

});

Route::post('/posts/{post}/report', [AuthController::class, 'report'])->name('posts.report')->middleware('auth');



Route::post('/change-language', function (Request $request) {
    $locale = $request->input('locale');

    // Optional: add validation
    if (in_array($locale, ['en', 'es', 'fr'])) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('changeLang');

Route::get('/dashboard/chart-data', [AdminController::class, 'chartData']);

Route::post('/likes/toggle', [LikeController::class, 'toggle'])->middleware('auth')->name('likes.toggle');
// routes/web.php
Route::post('/posts/save', [SavedPostController::class, 'toggleSave'])
    ->name('posts.save')
    ->middleware('auth');

Route::middleware('auth')->get('/tags/list', [AuthController::class, 'listTags']);

Route::middleware('auth')->group(function () {
    Route::post('/posts', [AuthController::class, 'storePost'])->name('posts.store');
    Route::get('/posts/filter', [AuthController::class, 'filterPosts'])->name('posts.filter');
});

Route::prefix('admin')->group(function () {
    // Art uploads management
    Route::get('/art-uploads', [ArtUploadController::class, 'index'])->name('admin.art-uploads');
    Route::get('/posts/{id}', [ArtUploadController::class, 'show']);
    Route::delete('/posts/{id}', [ArtUploadController::class, 'destroy']);
    Route::post('/posts/{id}/status', [ArtUploadController::class, 'updateStatus']);
});
