<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Artwork;
use App\Models\Location;
use App\Models\ArtistPartner;
use App\Models\PostReport;
use App\Models\Post;
use App\Models\SharedPost;
use App\Models\Comment;
use App\Models\Tag;

class AdminController extends Controller
{
    public function showAdminDashboard()
    {
        $user = Auth::user();

        if ($user && $user->email === 'admin@gmail.com') {
            $reportCount = PostReport::count();
            $stats = [
                'locations' => Post::count(),
                'users' => User::count(),
                'artworks' => Post::count(),
                'reports' => $reportCount,
                'verified_artists' => User::where('verified_artist', 1)->count(),
                'comments' => Comment::count(),
            ];
            $reports = PostReport::with(['post.user', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(5);

            return view('admin.Dashboard', compact('user', 'stats', 'reports'));
        }

        return redirect()->route('home')
               ->withErrors(['You do not have permission to access this page.']);
    }

    public function showArtistPartner()
    {
        return view('admin.ArtistPartners');
    }

    public function showArtUpload()
    {
        $posts = Post::with(['user', 'comments', 'likes'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

        return view('admin.ArtUpload', compact('posts'));
    }

    public function showBackup()
    {
        return view('admin.Backup');
    }

    public function showLocation()
    {
        return view('admin.Location');
    }

    public function showReports()
    {
        $reports = PostReport::with(['post.user', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(5);
        return view('admin.Reports', compact('reports'));
    }

    public function getReportDetails($reportId)
    {
        try {
            $report = PostReport::with([
                    'reporter',
                    'post.user',
                    'post'
                ])
                ->findOrFail($reportId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $report->id,
                    'reason' => $report->reason,
                    'additional_info' => $report->additional_info,
                    'status' => $report->status,
                    'created_at' => $report->created_at,
                    'reviewed_at' => $report->reviewed_at,
                    'moderator_notes' => $report->moderator_notes,
                    'reporter' => $report->reporter,
                    'reported_user' => $report->post->user ?? null,
                    'post' => $report->post
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Report not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function showSettings()
    {
        return view('admin.Settings');
    }

    public function showUserManagement()
    {
        return view('admin.UserManagement');
    }

    public function scopeVerifiedArtists($query)
    {
        return $query->where('verified_artist', 1);
    }

    public function chartData()
    {
        $userGrowth = DB::table('users')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $artUploads = DB::table('posts')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'userGrowth' => $userGrowth,
            'artUploads' => $artUploads,
        ]);
    }

    public function bulkDeletePosts(Request $request)
    {
        try {
            $postIds = $request->input('postIds', []);
            
            // Delete posts
            Post::whereIn('id', $postIds)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Posts deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting posts: ' . $e->getMessage()
            ], 500);
        }
    }
}