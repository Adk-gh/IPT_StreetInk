<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management | Street & Ink</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Reusing your existing styling conventions */
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            font-family: 'Inter', sans-serif;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .table-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            color: #333;
        }

        .search-filter-container {
            display: flex;
            gap: 15px;
        }

        .search-input {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Inter', sans-serif;
        }

        .table-wrapper {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
            border-bottom: 2px solid #eee;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-info {
            line-height: 1.4;
        }

        .user-name {
            font-weight: 500;
            color: #333;
        }

        .user-email {
            font-size: 0.85rem;
            color: #777;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background-color: #e6f7ee;
            color: #28a745;
        }

        .status-pending {
            background-color: #fff3e0;
            color: #fd7e14;
        }

        .role-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .role-admin {
            background-color: #f8d7da;
            color: #dc3545;
        }

        .role-artist {
            background-color: #d4edda;
            color: #28a745;
        }

        .role-user {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .action-btns {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            transition: color 0.2s;
        }

        .action-btn:hover {
            color: #333;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .empty-state {
            text-align: center;
            padding: 50px 0;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="table-header mt-5">
    <h1 class="table-title">Verified Artists</h1>
</div>

<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Status</th>
                <th>Role</th>
                <th>Artworks</th>
                <th>Location</th>
                <th>Last Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($verifiedArtists as $user)
                <tr>
                    <td>
                        <div class="user-cell">
                            <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('img/default.jpg') }}" alt="User Avatar"
                                class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <div class="user-name">{{ $user->name }}</div>
                                <div class="user-email">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge status-active">Active</span>
                    </td>
                    <td>
                        <span class="role-badge role-artist">Artist</span>
                    </td>
                    <td>{{ $user->posts_count ?? 0 }}</td>
                    <td>{{ $user->location ?? 'Unknown Location' }}</td>
                    <td>{{ $user->updated_at?->diffForHumans() ?? $user->created_at->diffForHumans() }}</td>
                    <td>
                        <div class="action-btns">
                            <button class="action-btn" title="View Profile"><i class="fas fa-eye"></i></button>
                            <button class="action-btn" title="Edit"><i class="fas fa-edit"></i></button>
                            <button class="action-btn" title="Message"><i class="fas fa-envelope"></i></button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">No verified artists found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>


    </div>
</body>
</html>
