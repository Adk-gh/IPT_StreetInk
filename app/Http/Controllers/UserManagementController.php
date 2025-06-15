<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Display the user management page with paginated users
     */
 public function index()
    {
        // Initialize default empty paginators
        $users = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $artists = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, ['pageName' => 'artists_page']);
        $error = null;

        try {
           $users = User::all();

            $artists = User::where('verified_artist', true)
                ->withCount('posts')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'artists_page');
        } catch (\Exception $e) {
            Log::error('User Management Error: ' . $e->getMessage());
            $error = 'Failed to load users. Please try again later.';
        }

        return view('admin.UserManagement', [
            'users' => $users,
            'artists' => $artists,
            'error' => $error
        ]);
    }

    public function update(Request $request, $user_id)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,'.$user_id,
                'phone' => 'nullable|string|max:20',
                'location' => 'nullable|string|max:255',
                'bio' => 'nullable|string',
                'verified_artist' => 'sometimes|boolean',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'password' => 'nullable|string|min:8|confirmed',
            ]);

            $user = User::findOrFail($user_id);
            $data = $request->only([
                'name', 'email', 'phone', 'location',
                'bio', 'verified_artist'
            ]);

            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture) {
                    Storage::delete($user->profile_picture);
                }
                $path = $request->file('profile_picture')->store('profile_pictures');
                $data['profile_picture'] = $path;
            }

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            DB::commit();

            Log::info('User updated successfully', ['user_id' => $user_id]);
            return response()->json(['success' => 'User updated successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update user: ' . $e->getMessage()], 500);
        }
    }
    /**
     * View a single user's details
     */
    public function show($user_id)
    {
        try {
            $user = User::withCount('posts')->findOrFail($user_id);
            return response()->json($user);
        } catch (\Exception $e) {
            Log::error('Error fetching user details: ' . $e->getMessage());
            return response()->json(['error' => 'User not found'], 404);
        }
    }



    /**
     * Delete a user
     */
    public function destroy($user_id)
    {
        DB::beginTransaction();

        try {
            $user = User::findOrFail($user_id);

            // Delete profile picture if exists
            if ($user->profile_picture) {
                Storage::delete($user->profile_picture);
            }

            // Delete user's posts and related media
            $user->posts()->each(function($post) {
                if ($post->media_path) {
                    Storage::delete($post->media_path);
                }
                $post->delete();
            });

            $user->delete();

            DB::commit();

            Log::info('User deleted successfully', ['user_id' => $user_id]);
            return response()->json(['success' => 'User deleted successfully!']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete user: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk delete users
     */
    public function bulkDestroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:users,user_id',
            ]);

            $users = User::whereIn('user_id', $request->ids)->get();

            foreach ($users as $user) {
                // Delete profile picture if exists
                if ($user->profile_picture) {
                    Storage::delete($user->profile_picture);
                }

                // Delete user's posts and related media
                $user->posts()->each(function($post) {
                    if ($post->media_path) {
                        Storage::delete($post->media_path);
                    }
                    $post->delete();
                });

                $user->delete();
            }

            DB::commit();

            Log::info('Bulk users deleted', ['count' => count($request->ids)]);
            return response()->json(['success' => count($request->ids) . ' users deleted successfully!']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in bulk user deletion: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete users: ' . $e->getMessage()], 500);
        }
    }
}
