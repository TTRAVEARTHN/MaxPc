<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{

    public function index(Request $request)
    {
        // filter podla mena alebo emailu
        $search = $request->query('search');

        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // posielame userov do admin view
        return view('admin.users.users', compact('users'));
    }



    public function delete($userId)
    {
        // nedovolime adminovi zmazat sam seba
        $user = User::findOrFail($userId);

        // нельзя удалить самого себя
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        // zmazanie usera
        $user->delete();

        return back()->with('success', 'User deleted.');
    }
}

