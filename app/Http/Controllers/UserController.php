<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = User::where('email', '!=', 'admin@gmail.com')
            ->withCount('posts')
            ->orderBy('created_at', 'desc');

        // For API responses
        if ($request->expectsJson()) {
            return response()->json($query->paginate(10));
        }

        // For web view
        $users = $query->paginate(10);
        return view('admin.user-management', compact('users'));
    }

    public function show(User $user)
    {
        return response()->json([
            'user' => $user->loadCount('posts'),
            'profile_picture_url' => $user->profile_picture
                ? Storage::url($user->profile_picture)
                : asset('/img/default.jpg')
        ]);
    }

    public function update(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'You cannot modify your own account'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'role' => 'required|in:user,artist,admin',
            'status' => 'required|in:active,inactive,banned',
            'verified_artist' => 'nullable|boolean',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            if ($request->hasFile('profile_picture')) {
                Storage::delete($user->profile_picture);
                $validated['profile_picture'] = $request->file('profile_picture')
                    ->store('profile_pictures', 'public');
            }

            $user->update($validated);

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error("User update failed: {$e->getMessage()}");
            return response()->json([
                'message' => 'Error updating user'
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'You cannot deactivate your own account'
            ], 403);
        }

        try {
            $user->update(['status' => 'inactive']);
            return response()->json(['message' => 'User deactivated successfully']);

        } catch (\Exception $e) {
            Log::error("User deactivation failed: {$e->getMessage()}");
            return response()->json([
                'message' => 'Error deactivating user'
            ], 500);
        }
    }
}
