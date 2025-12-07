<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * Show all users + search
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users.users', compact('users'));
    }

    /**
     * Promote user to admin
     */
    public function promote($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->role === 'admin') {
            return back()->with('error', 'User is already an admin.');
        }

        $user->role = 'admin';
        $user->save();

        return back()->with('success', 'User promoted to admin.');
    }

    /**
     * Delete user
     */
    public function delete($userId)
    {
        $user = User::findOrFail($userId);

        // нельзя удалить самого себя
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}

