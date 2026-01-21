<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function index()
    {
        // ziskame aktualne prihlaseneho pouzivatela
        $user = Auth::user();

        // Подтягиваем заказы пользователя вместе с товарами
        $orders = $user->orders()
            ->with('items.product')
            // aby sa nenacitaval produkt pre kazdu polozku samostatne
            ->orderBy('created_at', 'desc')
            ->get();

        // posielame do sablony usera a jeho zakazy
        return view('account', compact('user', 'orders'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'    => 'required|string|max:100',
            // unique s ignorovanim aktualneho usera, aby si mohol nechat ten isty email
            'email'   => 'required|email|max:150|unique:users,email,' . $user->id,
            'address' => 'nullable|string|max:255',
        ]);

        $user->update($data);

        // flash sprava do session aby sa dala zobrazit v sablone
        return back()->with('success', 'Account updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            // confirmed ocakava field new_password_confirmation v requeste
            'new_password'     => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        // kontrola ci zadane aktualne heslo sedi s ulozenym hashom v DB
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->update([
            // heslo sa vzdy uklada zahashovane, nikdy nie v plain texte
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
